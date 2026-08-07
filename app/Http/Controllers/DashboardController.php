<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Role;


class DashboardController extends Controller
{
    public function index()
    {
        $data['totalcustomers'] = Customer::count();

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
        $data['totalroles'] = Role::count();
        return view('dashboard',$data);
    }
}
