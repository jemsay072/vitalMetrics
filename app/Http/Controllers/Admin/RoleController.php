<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;


use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Permission;

class RoleController extends Controller
{

    //Index
    public function index(){

        // $roles = Role::orderBy('name')
        //     ->get();

        $columns = [
            [
                'key' => 'name',
                'label' => 'Name',
                'type' => 'text'
            ],
            [
                'key' => 'description',
                'label' => 'Description',
                'type' => 'text'
            ],
            [
                'key' => 'users_count',
                'label' => 'Users',
                'type' => 'number'
            ],
            [
                'key' => 'permissions',
                'label' => 'Permissions',
                'type' => 'list'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'text'
            ],
        ];

        $permissions = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'type' => 'text',
            ],
            [
                'key' => 'trackers',
                'label' => 'Trackers',
                'type' => 'text',
            ],
            [
                'key' => 'users',
                'label' => 'Users',
                'type' => 'text',
            ],
            [
                'key' => 'profile',
                'label' => 'Profile',
                'type' => 'text',
            ],
            [
                'key' => 'roles',
                'label' => 'Roles',
                'type' => 'text',
            ],
            [
                'key' => 'patients',
                'label' => 'Patients',
                'type' => 'text',
            ],
            [
                'key' => 'settings',
                'label' => 'Settings',
                'type' => 'text',
            ],
            [
                'key' => 'export',
                'label' => 'Export',
                'type' => 'text',
            ],
            [
                'key' => 'reports',
                'label' => 'Reports',
                'type' => 'text',
            ],
            [
                'key' => 'view_only',
                'label' => 'View Only',
                'type' => 'text',
            ],
        ];

        return view('admin.role.index', compact('columns', 'permissions'));
    }

    /**
     * Create
     */
    public function create(Request $request){

    }

    /**
     * Store
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permission.*' => [
                'string',
                'exists:permissions,name'
            ]
        ]);

        // Create Role
        DB::transaction( function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $permissionIds  = Permission::whereIn(
                'name',
                $validated['permissions'] ?? []
            )->pluck('id')->all();

            $role->permissions()->sync($permissionIds);
        });

        return back()
         ->with('success', 'Role created successfully.');
    }

    /**
     * Search Filter
     */

    public function search(Request $request) {
        $query = Role::query()
            ->withCount('users')
            ->with('permissions:id,name,label');

        // Search by name or by role
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $roles = $query
            ->orderBy('name')
            ->paginate($perPage);

        // Format the data for the listing
        $roles->getCollection()->transform(function ($role) {
            return[
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'users_count' => $role->users_count,
                'permissions' => $role->permissions
                    ->pluck('label')
                    ->values(),
                'status' => $role->status,
            ];
        });

        return response()->json($roles);
    }

    /**
     * Show
     */
    public function show(Role $role) {
        $role->load([
            'permissions:id,name,label',
            'users:id,name,email,role_id',
        ]);

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'status' => $role->status,
            'users_count' => $role->users->count(),
            'permissions_count' => $role->permissions->count(),

            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'name' => $permission->name,
                    'label' => $permission->label,
                ];
            })->values(),

            'users' => $role->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            })->values(),
        ]);
    }

    /**
     * Update Data
     */
    public function update(Request $request, Role $role){
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],

        ]);
        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $permissionIds = Permission::whereIn(
                'name',
                $validated['permissions'] ?? []
            )->pluck('id')->all();

            $role->permissions()->sync($permissionIds);
        });

        return response()->json([
            'message' => 'Role updated successfully.'
        ]);
    }

    /**
     * Trash
     */
    public function trash(Role $role){

        // Prevent from deleting the admin.
        if($role->name === 'Admin'){
            return response()->json([
                'message' => 'The Admin role cannot be deleted.'
            ], 403);
        }

        // Prevent the assigned existing role from deleting.
        if($role->users()->exists()){
            return response()->json([
                'message' => 'This role cannot be deleted because it is assigned to users.'
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role moved to trash successfully.'
        ]);
    }


    /**
     * Trash List
     */
    public function trashList(Request $request){

        $query = Role::onlyTrashed()
            ->withCount('users')
            ->with('permissions:id,name,label');

        // Search by name or description
        if($request->filled('search')){
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 10);

        if(!in_array($perPage, [10, 25, 50], true)){
            $perPage = 10;
        }

        $roles = $query
            ->orderByDesc('deleted_at')
            ->paginate($perPage);

        // Format the data
        $roles->getCollection()->transform( function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'users_count' => $role->users_count,
                'permissions' => $role->permissions
                    ->pluck('label')
                    ->values(),
                'status' => $role->status,
                'deleted_at' => $role->deleted_at?->format('M d, Y'),
            ];
        });

        return response()->json($roles);

    }

    /**
     * Restore
     */
    public function restore($id){
        $role = Role::onlyTrashed()->findOrFail($id);

        $role->restore();

        $role->update([
            'status' => 'inactive'
        ]);

        return response()->json([
            'message' => 'Role restored successfully.'
        ]);
    }

    /**
     * Force Delete
     */
    public function forceDelete($id) {
        $role = Role::onlyTrashed()->findOrFail($id);

        if ($role->name === 'Admin') {
            return response()->json([
                'message' => 'The Admin role cannot be permanently deleted.'
            ], 403);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'This role cannot be permanently deleted because it is assigned to users.'
            ], 403);
        }

        $role->forceDelete();

        return response()->json([
            'message' => 'Role permanently deleted.'
        ]);
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, Role $role){
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Prevent deactivation of the built-in Admin role
        if($role->name === 'Admin' && $validated['status'] === 'inactive'){
           return response()->json([
            'message' => 'The Admin role cannot be deactivated.'
           ], 403);
        }

        // Prevent unnecessary update
        if($role->status === $validated['status']){
            return response()->json([
                'message' => 'Role is already' . $role->status . '.'
            ]);
        }

        $role->update([
            // 'status' => 'active'
            'status' => $validated['status']
        ]);

        return response()->json([
            'message' => 'Role status updated successfully.',
            'status' => $role->status
        ]);
    }
}
