<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $data['roles'] = Role::withCount('permissions')->orderBy('id', 'DESC')->get();
        return view('Role.view', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST) {
            $data['permissions'] = Permission::orderBy('name')->get();
            return view('Role.add', $data);
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9 _-]+$/', Rule::unique('roles', 'name')],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['integer', 'exists:permissions,id'],
            ],
            [
                'name.regex' => 'Role name is an invalid format',
                'name.unique' => 'Role name already exists',
            ]
        );

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $permissionIds = $validated['permissions'] ?? [];
        $role->syncPermissions(Permission::whereIn('id', $permissionIds)->get());

        session()->flash('success', 'Role added successfully');
        return redirect('admin/roles_with_filter');
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return view('errors.404');
        }

        if (!$_POST) {
            $data['role'] = $role->load('permissions');
            $data['permissions'] = Permission::orderBy('name')->get();
            $data['selectedPermissionIds'] = $role->permissions->pluck('id')->all();
            return view('Role.edit', $data);
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9 _-]+$/', Rule::unique('roles', 'name')->ignore($role->id)],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['integer', 'exists:permissions,id'],
            ],
            [
                'name.regex' => 'Role name is an invalid format',
                'name.unique' => 'Role name already exists',
            ]
        );

        $role->name = $validated['name'];
        $role->save();

        $permissionIds = $validated['permissions'] ?? [];
        $role->syncPermissions(Permission::whereIn('id', $permissionIds)->get());

        session()->flash('success', 'Role updated successfully');
        return redirect('admin/roles_with_filter');
    }

    public function delete(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return view('errors.404');
        }

        if (strtolower($role->name) === 'super admin' || strtolower($role->name) === 'super-admin') {
            session()->flash('danger', 'Super Admin role cannot be deleted');
            return redirect('admin/roles_with_filter');
        }

        $role->delete();
        session()->flash('danger', 'Role deleted successfully');
        return redirect('admin/roles_with_filter');
    }
}
