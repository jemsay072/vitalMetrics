<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodPressure; // I-adjust base sa totoong model name mo
use App\Models\WeightTracker;        // I-adjust base sa totoong model name mo
use App\Models\WorkoutTracker;       // I-adjust base sa totoong model name mo
use App\Models\ActivityLog;   // I-adjust base sa totoong model name mo


class DashboardController extends Controller
{
    /**
     * Display the user health metrics overview dashboard.
     */
    public function index()
    {
        $userId = auth()->id();

        // 1. BLOOD PRESSURE READING
        // Kunin ang pinakahuling BP record
        $latestBP = BloodPressure::where('user_id', $userId)->latest()->first();
        // Kunin ang kabuuang bilang ng BP readings para sa summary card description
        $totalBPCount = BloodPressure::where('user_id', $userId)->count();

        // 2. WEIGHT METRICS
        $latestWeight = WeightTracker::where('user_id', $userId)->latest()->first();
        $totalWeightCount = WeightTracker::where('user_id', $userId)->count();

        // 3. WORKOUT TRACKS
        $latestWorkout = WorkoutTracker::where('user_id', $userId)->latest()->first();
        $totalWorkoutCount = WorkoutTracker::where('user_id', $userId)->count();

        // 4. RECENT ACTIVITIES STREAM
        // Kukuha ng huling 5 activities ng active user
        // $recentActivities = ActivityLog::where('user_id', $userId)
        //     ->latest()
        //     ->take(5)
        //     ->get();

        return view('page.dashboard.index', compact(
            'latestBP',
            'totalBPCount',
            'latestWeight',
            'totalWeightCount',
            'latestWorkout',
            'totalWorkoutCount',
            // 'recentActivities'
        ));
    }
}
