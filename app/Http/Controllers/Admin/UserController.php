<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

// use App\Models\cr;
use App\Models\User;
use App\Models\UserProfile;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // List user

        $users = User::with('role')
            ->orderBy('name')
            ->get();

        $columns = [
            [
                'key' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'type' => 'text',
            ],
            [
                'key' => 'role',
                'label' => 'Role',
                'type' => 'relation',
                'field' => 'name',
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'text',
            ],
        ];

        return view('admin.user.index', compact('users', 'columns'));
    }

    /**
     * Search Filter
     */

    public function search(Request $request){
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);

        if(!in_array($perPage, [10, 25, 50], true)){
            $perPage = 10;
        }

        $user = $query->latest()->paginate($perPage); // it now depends to the selection of the user
        return response()->json($user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',

            'phone' => 'nullable|string|max:30',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,widowed,divorced,separated',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:30',
            'province' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ]);

        // Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'status' => $validated['status'],
        ]);

        // Create User Profile
        $user->profile()->create([
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
        ]);

        return redirect()
         ->route('admin.users.show', $user)
         ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role', 'profile');
        $roles = Role::orderBy('name')->get();

        return view('admin.user.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'status' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:30',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,widowed,divorced,separated',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:30',
            'province' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ]);

        //User
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'role_id' => $validated['role_id'],
        ]);

        $user->profile()->updateOrCreate(
            [],
            [
                'phone' => $validated['phone'],
                'birth_date' => $validated['birth_date'],
                'sex' => $validated['sex'],
                'marital_status' => $validated['marital_status'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'zip_code' => $validated['zip_code'],

            ]
        );

        return redirect()
         -> route('admin.users.show', $user)
         -> with('success', 'User Information updated successfully.');
    }

    /**
     * User Avatar
     */
    public function updateAvatar(Request $request, User $user){

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete' => 'nullable|boolean'
        ]);

        $profile = $user->profile;

        // Delete
        if($request->input('delete_avatar') == 1){

            // Remove Avatar
            Storage::disk('public')->delete($profile->avatar);

            $profile->update(['avatar' => '']);

            return redirect()
             -> route('admin.users.show', $user)
             -> with('success', 'Avatar deleted successfully and reset to initials.');

        }

        // Upload or Replace image
        if($request->hasFile('avatar')){

            //Checking if avatar already exist
            if($profile && $profile->avatar){
                Storage::disk('public')->delete($profile->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            $user->profile()->updateOrCreate(
                [],
                [
                    'avatar' => $avatarPath
                ]
            );

            return redirect()
             -> route('admin.users.show', $user)
             -> with('success', 'Avatar updated successfully.');
        }

        return redirect()
         -> route('admin.users.show', $user)
         -> with('warning', 'No action performed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
    }
}
