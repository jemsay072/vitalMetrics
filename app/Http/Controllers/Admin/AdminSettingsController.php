<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AdminSetting;

class AdminSettingsController extends Controller
{
    // view index
    public function index(){

        $settings = AdminSetting::first();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request){
        $validation = $request->validate([
            'app_name' => 'required|string|max:100',
            'app_description' => 'nullable|string|max:500'
        ]);

        $admin_settings = AdminSetting::firstOrCreate([], $validation);

        $admin_settings->update($validation);

        return redirect()
         -> route('admin.settings')
         -> with('success', 'Settings updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request){

    }
}
