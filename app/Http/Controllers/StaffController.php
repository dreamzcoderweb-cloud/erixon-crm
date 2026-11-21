<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $data['staffs'] = User::with(['roles', 'branch', 'team', 'branches', 'teams'])
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $data['staffs'] = User::with(['roles', 'branch', 'team', 'branches', 'teams'])
                ->where('id', $user->id)
                ->get();
        }

        return view('staff.view', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST) {
            $data['roles'] = Role::orderBy('name')->get();
            $data['branches'] = Branch::where('status', 1)->orderBy('name')->get();
            $data['teams'] = Team::with('branch:id,name,code')->where('status', 1)->orderBy('name')->get();
            return view('staff.add', $data);
        }

        if ($request->filled('email')) {
            $trashedUser = User::onlyTrashed()->where('email', trim((string)$request->input('email')))->first();
            if ($trashedUser) {
                $suffix = '_d' . $trashedUser->id;
                $maxLen = 191 - strlen($suffix);
                $trashedUser->email = substr($trashedUser->email, 0, $maxLen) . $suffix;
                $trashedUser->saveQuietly();
            }
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->withoutTrashed()],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role_id' => ['required', 'integer', 'exists:roles,id'],
                'branch_ids' => ['nullable', 'array'],
                'branch_ids.*' => ['integer', 'exists:branches,id'],
                'branch_id' => ['nullable', 'exists:branches,id'],
                'team_ids' => ['nullable', 'array'],
                'team_ids.*' => ['integer', 'exists:teams,id'],
                'team_id' => ['nullable', 'exists:teams,id'],
                'mobile_number' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
                'date_of_birth' => ['nullable', 'date'],
                'date_of_joining' => ['nullable', 'date'],
                'designation' => ['nullable', 'string', 'max:100'],
                'staff_type' => ['nullable', 'string', 'in:Temporary,Permanent'],
                'base_salary' => ['nullable', 'numeric', 'min:0'],
                'available_leave_count' => ['nullable', 'numeric', 'min:0'],
                'check_in_time' => ['nullable'],
                'allow_check_in_time' => ['nullable'],
                'late_attendance_count' => ['nullable', 'integer', 'min:0'],
                'increment_amount' => ['nullable', 'numeric', 'min:0'],
                'increment_date' => ['nullable', 'date'],
                'check_out_time' => ['nullable'],
                'status' => ['nullable', 'in:0,1'],
            ]
        );

        $branchIds = $request->input('branch_ids', []);
        if (empty($branchIds) && $request->filled('branch_id')) {
            $branchIds = [(int) $request->input('branch_id')];
        }
        $branchIds = array_values(array_unique(array_filter($branchIds)));

        $teamIds = $request->input('team_ids', []);
        if (empty($teamIds) && $request->filled('team_id')) {
            $teamIds = [(int) $request->input('team_id')];
        }
        $teamIds = array_values(array_unique(array_filter($teamIds)));

        $primaryBranchId = !empty($branchIds) ? $branchIds[0] : null;
        $primaryTeamId   = !empty($teamIds) ? $teamIds[0] : null;

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $primaryBranchId;
        $user->team_id = $primaryTeamId;
        $user->mobile_number = $validated['mobile_number'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->date_of_birth = $validated['date_of_birth'] ?? null;
        $user->date_of_joining = $validated['date_of_joining'] ?? null;
        $user->designation = $validated['designation'] ?? null;
        $user->staff_type = $validated['staff_type'] ?? 'Permanent';
        $user->base_salary = $validated['base_salary'] ?? 0.00;
        $user->available_leave_count = ($user->staff_type === 'Temporary') ? 0.00 : ($validated['available_leave_count'] ?? 0.00);
        $user->check_in_time = $validated['check_in_time'] ?? null;
        $user->allow_check_in_time = $validated['allow_check_in_time'] ?? null;
        $user->late_attendance_count = $validated['late_attendance_count'] ?? 0;
        $user->increment_amount = $validated['increment_amount'] ?? 0.00;
        $user->increment_date = $validated['increment_date'] ?? null;
        $user->check_out_time = $validated['check_out_time'] ?? null;
        $user->status = isset($validated['status']) ? (int) $validated['status'] : 1;
        $user->is_on_leave = false;
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Sync multiple branches
        if (!empty($branchIds)) {
            $branchSync = [];
            foreach ($branchIds as $idx => $bId) {
                $branchSync[$bId] = ['is_primary' => ($idx === 0)];
            }
            $user->branches()->sync($branchSync);
        } else {
            $user->branches()->detach();
        }

        // Sync multiple teams
        if (!empty($teamIds)) {
            $teamSync = [];
            foreach ($teamIds as $idx => $tId) {
                $teamSync[$tId] = [
                    'role_in_team' => 'member',
                    'is_primary'   => ($idx === 0)
                ];
            }
            $user->teams()->sync($teamSync);
        } else {
            $user->teams()->detach();
        }

        $role = Role::find($validated['role_id']);
        if ($role) {
            $user->syncRoles([$role]);
        }

        session()->flash('success', 'Staff created successfully');
        return redirect('admin/staff');
    }

    public function update(Request $request, $id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return view('errors.404');
        }

        if ($user->id === 1 && !auth()->user()->isSuperAdmin()) {
            session()->flash('danger', 'Primary Super Admin user cannot be edited from Staff module');
            return redirect('admin/staff');
        }

        if (!$_POST) {
            $data['staff'] = $user;
            $data['roles'] = Role::orderBy('name')->get();
            $data['branches'] = Branch::where('status', 1)->orderBy('name')->get();

            // Load assigned branch IDs from pivot and primary column
            $assignedBranchIds = $user->branches->pluck('id')->toArray();
            if ($user->branch_id && !in_array($user->branch_id, $assignedBranchIds)) {
                $assignedBranchIds[] = (int) $user->branch_id;
            }
            $data['assignedBranchIds'] = array_values(array_unique(array_filter($assignedBranchIds)));

            // Load assigned team IDs from pivot and primary column
            $assignedTeamIds = $user->teams->pluck('id')->toArray();
            if ($user->team_id && !in_array($user->team_id, $assignedTeamIds)) {
                $assignedTeamIds[] = (int) $user->team_id;
            }
            $data['assignedTeamIds'] = array_values(array_unique(array_filter($assignedTeamIds)));

            // Load teams belonging to any of the assigned branches (or all active teams if none)
            $data['teams'] = !empty($data['assignedBranchIds'])
                ? Team::with('branch:id,name,code')->whereIn('branch_id', $data['assignedBranchIds'])->where('status', 1)->orderBy('name')->get()
                : Team::with('branch:id,name,code')->where('status', 1)->orderBy('name')->get();

            $data['selectedRoleId'] = $user->roles->first()?->id;
            return view('staff.edit', $data);
        }

        if ($request->filled('email')) {
            $trashedUser = User::onlyTrashed()->where('email', trim((string)$request->input('email')))->where('id', '!=', $user->id)->first();
            if ($trashedUser) {
                $suffix = '_d' . $trashedUser->id;
                $maxLen = 191 - strlen($suffix);
                $trashedUser->email = substr($trashedUser->email, 0, $maxLen) . $suffix;
                $trashedUser->saveQuietly();
            }
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->withoutTrashed()],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role_id' => ['required', 'integer', 'exists:roles,id'],
                'branch_ids' => ['nullable', 'array'],
                'branch_ids.*' => ['integer', 'exists:branches,id'],
                'branch_id' => ['nullable', 'exists:branches,id'],
                'team_ids' => ['nullable', 'array'],
                'team_ids.*' => ['integer', 'exists:teams,id'],
                'team_id' => ['nullable', 'exists:teams,id'],
                'mobile_number' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
                'date_of_birth' => ['nullable', 'date'],
                'date_of_joining' => ['nullable', 'date'],
                'designation' => ['nullable', 'string', 'max:100'],
                'staff_type' => ['nullable', 'string', 'in:Temporary,Permanent'],
                'base_salary' => ['nullable', 'numeric', 'min:0'],
                'available_leave_count' => ['nullable', 'numeric', 'min:0'],
                'check_in_time' => ['nullable'],
                'allow_check_in_time' => ['nullable'],
                'late_attendance_count' => ['nullable', 'integer', 'min:0'],
                'increment_amount' => ['nullable', 'numeric', 'min:0'],
                'increment_date' => ['nullable', 'date'],
                'check_out_time' => ['nullable'],
                'status' => ['nullable', 'in:0,1'],
            ]
        );

        $branchIds = $request->input('branch_ids', []);
        if (empty($branchIds) && $request->filled('branch_id')) {
            $branchIds = [(int) $request->input('branch_id')];
        }
        $branchIds = array_values(array_unique(array_filter($branchIds)));

        $teamIds = $request->input('team_ids', []);
        if (empty($teamIds) && $request->filled('team_id')) {
            $teamIds = [(int) $request->input('team_id')];
        }
        $teamIds = array_values(array_unique(array_filter($teamIds)));

        $primaryBranchId = !empty($branchIds) ? $branchIds[0] : null;
        $primaryTeamId   = !empty($teamIds) ? $teamIds[0] : null;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $primaryBranchId;
        $user->team_id = $primaryTeamId;
        $user->mobile_number = $validated['mobile_number'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->date_of_birth = $validated['date_of_birth'] ?? null;
        $user->date_of_joining = $validated['date_of_joining'] ?? null;
        $user->designation = $validated['designation'] ?? null;
        $user->staff_type = $validated['staff_type'] ?? 'Permanent';
        $user->base_salary = $validated['base_salary'] ?? 0.00;
        $user->available_leave_count = ($user->staff_type === 'Temporary') ? 0.00 : ($validated['available_leave_count'] ?? 0.00);
        $user->check_in_time = $validated['check_in_time'] ?? null;
        $user->allow_check_in_time = $validated['allow_check_in_time'] ?? null;
        $user->late_attendance_count = $validated['late_attendance_count'] ?? 0;
        $user->increment_amount = $validated['increment_amount'] ?? 0.00;
        $user->increment_date = $validated['increment_date'] ?? null;
        $user->check_out_time = $validated['check_out_time'] ?? null;
        if (isset($validated['status'])) {
            $user->status = (int) $validated['status'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // Sync multiple branches in pivot table
        if (!empty($branchIds)) {
            $branchSync = [];
            foreach ($branchIds as $idx => $bId) {
                $branchSync[$bId] = ['is_primary' => ($idx === 0)];
            }
            $user->branches()->sync($branchSync);
        } else {
            $user->branches()->detach();
        }

        // Sync multiple teams in pivot table
        if (!empty($teamIds)) {
            $teamSync = [];
            foreach ($teamIds as $idx => $tId) {
                $teamSync[$tId] = [
                    'role_in_team' => 'member',
                    'is_primary'   => ($idx === 0)
                ];
            }
            $user->teams()->sync($teamSync);
        } else {
            $user->teams()->detach();
        }

        $role = Role::find($validated['role_id']);
        if ($role) {
            $user->syncRoles([$role]);
            if ($user->id === 1 && $role->name !== 'Super Admin') {
                $superAdminRole = Role::where('name', 'Super Admin')->first();
                if ($superAdminRole) {
                    $user->assignRole($superAdminRole);
                }
            }
        }

        session()->flash('success', 'Staff updated successfully');
        return redirect('admin/staff');
    }

    public function delete(Request $request, $id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return view('errors.404');
        }

        if ($user->id === 1) {
            session()->flash('danger', 'Primary Super Admin account cannot be deleted');
            return redirect('admin/staff');
        }

        if ($user->id === Auth::id()) {
            session()->flash('danger', 'You cannot delete your own account');
            return redirect('admin/staff');
        }

        if ($user->hasRole('Super Admin') && !Auth::user()->isSuperAdmin()) {
            session()->flash('danger', 'Only a Super Admin can delete a Super Admin staff');
            return redirect('admin/staff');
        }

        if (!empty($user->profile_image)) {
            delete_file($user->profile_image);
        }

        $user->delete();
        session()->flash('danger', 'Staff deleted successfully');
        return redirect('admin/staff');
    }

    /**
     * Requirement 2: Toggle staff leave status (On Leave / Active)
     */
    public function toggleLeave(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Staff not found.'
            ], 404);
        }

        $user->is_on_leave = !$user->is_on_leave;
        $user->save();

        if (!empty($user->branch_id)) {
            DB::table('branch_user')->updateOrInsert(
                ['user_id' => $user->id, 'branch_id' => $user->branch_id],
                ['is_primary' => true, 'updated_at' => now()]
            );
        }
        if (!empty($user->team_id)) {
            DB::table('team_user')->updateOrInsert(
                ['user_id' => $user->id, 'team_id' => $user->team_id],
                ['role_in_team' => 'member', 'is_primary' => true, 'updated_at' => now()]
            );
        }

        $statusText = $user->is_on_leave ? 'on leave' : 'active (available)';

        return response()->json([
            'status'      => true,
            'is_on_leave' => $user->is_on_leave,
            'message'     => "Staff {$user->name} is now marked as {$statusText}."
        ]);
    }
}
