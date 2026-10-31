<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

class DashboardController extends Controller
{
    // index
    public function index(){

        $totalUsers =  User::count();

        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();

        $totalRoles = Role::count();

        $recentUsers = User::latest()
            ->take(5)
            ->get([
               'id',
               'name',
               'email',
               'status',
               'created_at'
            ]);

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalRoles',
            'recentUsers'
        ));

    }
}
