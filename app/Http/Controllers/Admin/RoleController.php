<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;

class RoleController extends Controller
{
    //Index
    public function index(){

        $roles = Role::orderBy('name')
            ->get();

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

        return view('admin.role.index', compact('roles', 'columns', 'permissions'));
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
            'description' => 'nullable|string|max:255'
        ]);

        // Create Role
        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()
         ->with('success', 'Role created successfully.');
    }

    /**
     * Search Filter
     */

    public function search(Request $request)
{
        $query = Role::query()
            ->withCount('users');

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

        return response()->json($roles);
    }
}
