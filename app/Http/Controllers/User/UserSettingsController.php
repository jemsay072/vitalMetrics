<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserPreferences;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $preferences = Auth::user()->preferences ?? new UserPreferences();
        //view the settings page
        return view('page.settings.index', compact('preferences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request){

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'theme' => 'required|string|in:light,dark',
            'weight_unit' => 'required|string|in:kg,lbs',
            'date_format' => 'required|string|in:M d, Y,Y-m-d,d/m/Y',
            'dashboard_landing' => 'required|string|in:dashboard,bp,workout-tracker,weight',
            'notifications_enabled' => 'nullable|boolean',
            'email_notifications' => 'nullable|boolean',
        ]);

        $validated['notifications_enabled'] = $request->has('notifications_enabled');
        $validated['email_notifications'] = $request->has('email_notifications');

        $user->preferences()->updateOrCreate([], $validated);

        return redirect()->back()->with([
            'success' => 'Preferences updated successfully!',
            'active_tab' => $request->has('email_notifications') || $request->has('notifications_enabled')
                ? 'notifications'
                : ($request->has('weight_unit') ? 'localization' : 'general')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
