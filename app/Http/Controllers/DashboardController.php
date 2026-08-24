<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\LeadRequirement;
use App\Models\LostReason;
use App\Models\Followup;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Customers Module
        $data['totalcustomers']    = Customer::forUser($user)->count();
        $data['activecustomers']   = Customer::forUser($user)->where('status', 1)->count();
        $data['inactivecustomers'] = Customer::forUser($user)->where('status', 0)->count();

        // 2. Staff Module (Excluding Admin / Super Admin)
        $data['totalstaff'] = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', [
                'Super Admin',
                'Super-Admin',
                'super admin',
                'super-admin',
                'Admin',
                'admin',
            ]);
        })->count();
        $data['activestaff'] = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', [
                'Super Admin',
                'Super-Admin',
                'super admin',
                'super-admin',
                'Admin',
                'admin',
            ]);
        })->count();

        // 3. Roles Module
        $data['totalroles'] = Role::count();

        // 4. Leads Module
        $data['totalleads']    = Lead::forUser($user)->count();
        $data['activeleads']   = Lead::forUser($user)->where('status', 1)->count();
        $data['inactiveleads'] = Lead::forUser($user)->where('status', 0)->count();

        // 5. Lead Sources Module
        $data['totallead_sources']  = LeadSource::count();
        $data['activelead_sources'] = LeadSource::where('status', 1)->count();

        // 6. Lead Stages Module
        $data['totallead_stages']  = LeadStage::count();
        $data['activelead_stages'] = LeadStage::where('status', 1)->count();

        // 7. Lead Requirements Module
        $data['totallead_requirements']  = LeadRequirement::count();
        $data['activelead_requirements'] = LeadRequirement::where('status', 1)->count();

        // 8. Lost Reasons Module
        $data['totallost_reasons']  = LostReason::count();
        $data['activelost_reasons'] = LostReason::where('status', 1)->count();

        // 9. Followups Module
        $data['totalfollowups']   = Followup::forUser($user)->count();
        $data['pendingfollowups'] = Followup::forUser($user)->where(function ($q) {
            $q->where('followup_status', 'pending')
              ->orWhere('followup_status', 'Pending')
              ->orWhereNull('followup_status');
        })->count();

        // Recent Records
        $data['recentCustomers'] = Customer::forUser($user)->latest('customer_id')->take(5)->get();
        $data['recentLeads']     = Lead::forUser($user)->with(['customer', 'leadStage', 'assignedUser'])->latest('lead_id')->take(5)->get();

        $data['popularRequirements'] = LeadRequirement::withCount(['leads' => function ($q) use ($user) {
            $q->forUser($user);
        }])
        ->orderBy('leads_count', 'desc')
        ->take(5)
        ->get();

        return view('dashboard', $data);
    }
}
