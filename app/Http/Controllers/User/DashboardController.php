<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodPressure;
use App\Models\WeightTracker;
use App\Models\WorkoutTracker;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user health metrics overview dashboard.
     */
    public function index() {
        $auth = Auth::user();
        $userId = $auth->id;

        // BP Reading
        $latestBP = BloodPressure::where('user_id', $userId)->latest()->first();
        $totalBPCount = BloodPressure::where('user_id', $userId)->count();

        // WEIGHT METRICS
        $latestWeight = WeightTracker::where('user_id', $userId)->latest()->first();
        $totalWeightCount = WeightTracker::where('user_id', $userId)->count();

        // WORKOUT TRACKS
        $latestWorkout = WorkoutTracker::where('user_id', $userId)->latest()->first();
        $totalWorkoutCount = WorkoutTracker::where('user_id', $userId)->count();

        // RECENT ACTIVITIES STREAM
        $recentActivities = ActivityLog::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('page.dashboard.index', compact(
            'latestBP',
            'totalBPCount',
            'latestWeight',
            'totalWeightCount',
            'latestWorkout',
            'totalWorkoutCount',
            'recentActivities'
        ));
    }

    /**
     * Activities Logs
     */
    public function activities(){
        $auth = Auth::user();

        $activities = ActivityLog::where('user_id', $auth->id)
                    ->latest()
                    ->paginate(10);

        return view('page.dashboard.activities', compact('activities'));
    }
}
