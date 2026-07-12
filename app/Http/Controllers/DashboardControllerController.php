<?php

namespace App\Http\Controllers;

use App\Models\BloodPressure;
use App\Models\WorkoutTracker;
use App\Models\WeightTracker;
use Illuminate\Http\Request;

class DashboardControllerController extends Controller
{
    //

    public function index(){
        return view('page.dashboard.index');
    }
}
