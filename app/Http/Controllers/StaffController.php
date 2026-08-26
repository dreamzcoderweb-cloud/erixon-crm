<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $data['staffs'] = User::with('roles')
                ->staffOnly()
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $data['staffs'] = User::with('roles')
                ->where('id', $user->id)
                ->get();
        }

        return view('staff.view', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST) {
            $data['roles'] = Role::orderBy('name')->get();
            return view('staff.add', $data);
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->withoutTrashed()],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role_id' => ['required', 'integer', 'exists:roles,id'],
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
            ]
        );

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
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
        $user->is_on_leave = false;
        $user->password = Hash::make($validated['password']);
        $user->save();

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

        if ($user->hasRole('Super Admin')) {
            session()->flash('danger', 'Super Admin user cannot be edited from Staff module');
            return redirect('admin/staff');
        }

        if (!$_POST) {
            $data['staff'] = $user;
            $data['roles'] = Role::orderBy('name')->get();
            $data['selectedRoleId'] = $user->roles->first()?->id;
            return view('staff.edit', $data);
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->withoutTrashed()],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role_id' => ['required', 'integer', 'exists:roles,id'],
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
            ]
        );

        $user->name = $validated['name'];
        $user->email = $validated['email'];
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
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $role = Role::find($validated['role_id']);
        if ($role) {
            $user->syncRoles([$role]);
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

        if ($user->hasRole('Super Admin')) {
            session()->flash('danger', 'Super Admin user cannot be deleted');
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

        $statusText = $user->is_on_leave ? 'on leave' : 'active (available)';

        return response()->json([
            'status'      => true,
            'is_on_leave' => $user->is_on_leave,
            'message'     => "Staff {$user->name} is now marked as {$statusText}."
        ]);
    }
}
