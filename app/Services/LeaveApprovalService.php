<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApprovalStageNotification;
use App\Traits\SendsPushNotifications;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LeaveApprovalService
{
    use SendsPushNotifications;

    /**
     * Determine initial approval stage and status when a leave request is submitted.
     * Scenario 1: Normal staff in a team -> Team Leader (stage: tl)
     * Scenario 2: HR designation -> Direct to MD (stage: md)
     */
    public function determineInitialStage(User $applicant): array
    {
        // Scenario 2: If applicant has HR designation, skip TL and HR -> direct to MD
        if ($applicant->isHr()) {
            return [
                'approval_stage' => 'md',
                'status'         => 'Pending MD Approval',
                'role_label'     => 'MD',
                'target_role'    => 'md',
            ];
        }

        // Scenario 1: Normal staff in a team with a leader
        $team = $applicant->team;
        $teamLeader = $team?->leader;

        if ($teamLeader && $teamLeader->id !== $applicant->id) {
            return [
                'approval_stage' => 'tl',
                'status'         => 'Pending TL Approval',
                'role_label'     => 'Team Leader',
                'target_role'    => 'tl',
                'team_leader'    => $teamLeader,
            ];
        }

        // If staff is not HR and has no TL (or is the TL), goes to HR
        return [
            'approval_stage' => 'hr',
            'status'         => 'Pending HR Approval',
            'role_label'     => 'HR',
            'target_role'    => 'hr',
        ];
    }

    /**
     * Retrieve all active HR users.
     */
    public function getHrUsers(): Collection
    {
        return User::where('status', 1)
            ->where(function ($q) {
                $q->where('designation', 'LIKE', '%HR%')
                  ->orWhereHas('roles', function ($rq) {
                      $rq->whereRaw('LOWER(name) LIKE ?', ['%hr%']);
                  });
            })
            ->get();
    }

    /**
     * Retrieve all active MD users (and Super Admins).
     */
    public function getMdUsers(): Collection
    {
        return User::where('status', 1)
            ->where(function ($q) {
                $q->where('designation', 'LIKE', '%MD%')
                  ->orWhere('id', 1)
                  ->orWhereHas('roles', function ($rq) {
                      $rq->whereRaw('LOWER(name) IN (?, ?, ?)', ['super admin', 'super-admin', 'admin']);
                  });
            })
            ->get();
    }

    /**
     * Check if a specific user is authorized to approve/reject the leave request at its current stage.
     */
    public function canUserApprove(User $user, LeaveRequest $leave): bool
    {
        if ($user->isSuperAdmin() || $user->id === 1) {
            return true;
        }

        if (in_array($leave->status, ['Approved', 'Rejected'])) {
            return false;
        }

        $applicant = $leave->user;
        $stage = $leave->approval_stage ?? 'tl';

        if ($stage === 'tl') {
            $teamLeaderId = $applicant?->team?->leader_id;
            return ($teamLeaderId && $user->id === $teamLeaderId) || $user->isAdmin();
        }

        if ($stage === 'hr') {
            return $user->isHr() || $user->isAdmin();
        }

        if ($stage === 'md') {
            return $user->isMd() || $user->isAdmin();
        }

        return $user->isAdmin();
    }

    /**
     * Send notifications when a new leave request is submitted.
     */
    public function notifyOnSubmit(LeaveRequest $leave, User $applicant): void
    {
        try {
            $fromDate  = Carbon::parse($leave->from_date)->format('d-m-Y');
            $toDate    = Carbon::parse($leave->to_date)->format('d-m-Y');
            $days      = $leave->number_of_days;
            $stage     = $leave->approval_stage;

            // 1. Notify Applicant
            $stageTitle = ($stage === 'md') ? 'MD Approval' : (($stage === 'hr') ? 'HR Approval' : 'Team Leader Approval');
            $appMsg = "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) has been submitted and is pending {$stageTitle}.";
            $applicant->notify(new LeaveApprovalStageNotification($leave, 'Leave Request Submitted', $appMsg, $stage, $applicant));
            $this->sendPushNotification($applicant, 'Leave Request Submitted', $appMsg, [
                'leave_id' => (string) $leave->id,
                'status'   => (string) $leave->status,
                'stage'    => (string) $stage,
            ]);

            // 2. Notify Approvers based on current stage
            if ($stage === 'tl') {
                $teamLeader = $applicant->team?->leader;
                if ($teamLeader && $teamLeader->id !== $applicant->id) {
                    $tlMsg = "{$applicant->name} has applied for leave ({$fromDate} to {$toDate}, {$days} day(s)). Pending your Team Leader approval.";
                    $teamLeader->notify(new LeaveApprovalStageNotification($leave, 'New Leave Request (Pending TL Approval)', $tlMsg, 'tl', $applicant));
                    $this->sendPushNotification($teamLeader, 'New Leave Request Pending Approval', $tlMsg, [
                        'leave_id' => (string) $leave->id,
                        'stage'    => 'tl',
                    ]);
                }
            } elseif ($stage === 'hr') {
                $hrUsers = $this->getHrUsers();
                foreach ($hrUsers as $hr) {
                    if ($hr->id === $applicant->id) continue;
                    $hrMsg = "Leave request from {$applicant->name} ({$fromDate} to {$toDate}, {$days} day(s)) is pending HR approval.";
                    $hr->notify(new LeaveApprovalStageNotification($leave, 'Leave Request Pending HR Approval', $hrMsg, 'hr', $applicant));
                    $this->sendPushNotification($hr, 'Leave Request Pending HR Approval', $hrMsg, [
                        'leave_id' => (string) $leave->id,
                        'stage'    => 'hr',
                    ]);
                }
            } elseif ($stage === 'md') {
                // Direct to MD (Scenario 2 or escalated)
                $mdUsers = $this->getMdUsers();
                foreach ($mdUsers as $md) {
                    if ($md->id === $applicant->id) continue;
                    $mdMsg = "New leave request from {$applicant->name} ({$applicant->designation}) for {$fromDate} to {$toDate} is pending your MD approval.";
                    $md->notify(new LeaveApprovalStageNotification($leave, 'Leave Request Pending MD Approval', $mdMsg, 'md', $applicant));
                    $this->sendPushNotification($md, 'Leave Request Pending MD Approval', $mdMsg, [
                        'leave_id' => (string) $leave->id,
                        'stage'    => 'md',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('LeaveApprovalService: notifyOnSubmit error: ' . $e->getMessage());
        }
    }

    /**
     * Process approval at the current stage (TL -> HR -> MD).
     */
    public function approveStage(User $approver, LeaveRequest $leave, ?string $remarks = null): array
    {
        $currentStage = $leave->approval_stage ?? 'tl';
        $applicant    = $leave->user ?? User::find($leave->user_id);
        $fromDate     = Carbon::parse($leave->from_date)->format('d-m-Y');
        $toDate       = Carbon::parse($leave->to_date)->format('d-m-Y');
        $days         = $leave->number_of_days;

        if ($currentStage === 'tl') {
            // Stage 1 (TL) Approved -> Move to HR
            $leave->tl_approved_by = $approver->id;
            $leave->tl_approved_at = now();
            $leave->tl_remarks     = $remarks;
            $leave->approval_stage = 'hr';
            $leave->status         = 'Pending HR Approval';
            $leave->save();

            // Notify Applicant
            if ($applicant) {
                $msg = "Your leave request for {$fromDate} to {$toDate} was approved by Team Leader ({$approver->name}) and forwarded to HR for approval.";
                $applicant->notify(new LeaveApprovalStageNotification($leave, 'Leave Approved by Team Leader', $msg, 'hr', $approver));
                $this->sendPushNotification($applicant, 'Leave Approved by Team Leader', $msg, ['leave_id' => (string)$leave->id, 'stage' => 'hr']);
            }

            // Notify HR
            $hrUsers = $this->getHrUsers();
            foreach ($hrUsers as $hr) {
                if ($hr->id === $approver->id) continue;
                $msg = "Leave request for {$applicant?->name} ({$fromDate} to {$toDate}) approved by TL ({$approver->name}), pending your HR approval.";
                $hr->notify(new LeaveApprovalStageNotification($leave, 'Leave Pending HR Approval', $msg, 'hr', $approver));
                $this->sendPushNotification($hr, 'Leave Pending HR Approval', $msg, ['leave_id' => (string)$leave->id, 'stage' => 'hr']);
            }

            return [
                'status'        => true,
                'message'       => 'Leave request approved by Team Leader and forwarded to HR.',
                'next_stage'    => 'hr',
                'status_label'  => 'Pending HR Approval',
            ];
        } elseif ($currentStage === 'hr') {
            // Stage 2 (HR) Approved -> Move to MD
            $leave->hr_approved_by = $approver->id;
            $leave->hr_approved_at = now();
            $leave->hr_remarks     = $remarks;
            $leave->approval_stage = 'md';
            $leave->status         = 'Pending MD Approval';
            $leave->save();

            // Notify Applicant
            if ($applicant) {
                $msg = "Your leave request for {$fromDate} to {$toDate} was approved by HR ({$approver->name}) and forwarded to MD for final approval.";
                $applicant->notify(new LeaveApprovalStageNotification($leave, 'Leave Approved by HR', $msg, 'md', $approver));
                $this->sendPushNotification($applicant, 'Leave Approved by HR', $msg, ['leave_id' => (string)$leave->id, 'stage' => 'md']);
            }

            // Notify MD
            $mdUsers = $this->getMdUsers();
            foreach ($mdUsers as $md) {
                if ($md->id === $approver->id) continue;
                $msg = "Leave request for {$applicant?->name} ({$fromDate} to {$toDate}) approved by HR ({$approver->name}), pending your final MD approval.";
                $md->notify(new LeaveApprovalStageNotification($leave, 'Leave Pending MD Approval', $msg, 'md', $approver));
                $this->sendPushNotification($md, 'Leave Pending MD Approval', $msg, ['leave_id' => (string)$leave->id, 'stage' => 'md']);
            }

            return [
                'status'        => true,
                'message'       => 'Leave request approved by HR and forwarded to MD for final approval.',
                'next_stage'    => 'md',
                'status_label'  => 'Pending MD Approval',
            ];
        } else {
            // Stage 3 (MD) Final Approval -> Fully Approved
            $leave->md_approved_by = $approver->id;
            $leave->md_approved_at = now();
            $leave->md_remarks     = $remarks;
            $leave->approved_by    = $approver->id;
            $leave->admin_remarks  = $remarks;
            $leave->approval_stage = 'approved';
            $leave->status         = 'Approved';
            $leave->save();

            // Set user on leave if dates include today
            $today = Carbon::today()->toDateString();
            if ($leave->from_date && $leave->to_date) {
                $fStr = Carbon::parse($leave->from_date)->toDateString();
                $tStr = Carbon::parse($leave->to_date)->toDateString();
                if ($fStr <= $today && $tStr >= $today) {
                    if ($applicant) {
                        $applicant->is_on_leave = true;
                        $applicant->save();
                    }
                }
            }

            // Notify Applicant of final approval
            if ($applicant) {
                $msg = "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) has been fully approved by MD ({$approver->name}).";
                $applicant->notify(new LeaveApprovalStageNotification($leave, 'Leave Request Fully Approved', $msg, 'approved', $approver));
                $this->sendPushNotification($applicant, 'Leave Request Fully Approved', $msg, ['leave_id' => (string)$leave->id, 'status' => 'Approved']);
            }

            return [
                'status'        => true,
                'message'       => 'Leave request fully approved by MD.',
                'next_stage'    => 'approved',
                'status_label'  => 'Approved',
            ];
        }
    }

    /**
     * Process rejection at the current stage.
     */
    public function rejectStage(User $rejector, LeaveRequest $leave, ?string $remarks = null): array
    {
        $currentStage = $leave->approval_stage ?? 'tl';
        $applicant    = $leave->user ?? User::find($leave->user_id);
        $fromDate     = Carbon::parse($leave->from_date)->format('d-m-Y');
        $toDate       = Carbon::parse($leave->to_date)->format('d-m-Y');

        if ($currentStage === 'tl') {
            $leave->tl_remarks = $remarks;
            $stageName = 'Team Leader';
        } elseif ($currentStage === 'hr') {
            $leave->hr_remarks = $remarks;
            $stageName = 'HR';
        } else {
            $leave->md_remarks = $remarks;
            $stageName = 'MD';
        }

        $leave->status         = 'Rejected';
        $leave->approval_stage = 'rejected';
        $leave->approved_by    = $rejector->id;
        $leave->admin_remarks  = $remarks;
        $leave->save();

        // Notify Applicant
        if ($applicant) {
            $msg = "Your leave request for {$fromDate} to {$toDate} was rejected by {$stageName} ({$rejector->name})." . ($remarks ? " Reason: {$remarks}" : "");
            $applicant->notify(new LeaveApprovalStageNotification($leave, 'Leave Request Rejected', $msg, 'rejected', $rejector));
            $this->sendPushNotification($applicant, 'Leave Request Rejected', $msg, ['leave_id' => (string)$leave->id, 'status' => 'Rejected']);
        }

        return [
            'status'       => true,
            'message'      => "Leave request rejected by {$stageName}.",
            'status_label' => 'Rejected',
        ];
    }
}
