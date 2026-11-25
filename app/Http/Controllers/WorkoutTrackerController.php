<?php

namespace App\Http\Controllers;

use App\Models\WorkoutTracker;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = Auth::user();

        $workoutList = WorkoutTracker::where('user_id', $auth->id)->latest()->get();

        $columns = [
            [
                'key' => 'activity_type',
                'label' => 'Activity Type',
                'type' => 'text',
            ],
            [
                'key' => 'duration_minutes',
                'label' => 'Duration (min)',
                'type' => 'text',
            ],
            [
                'key' => 'distance_km',
                'label' => 'Distance (km)',
                'type' => 'text',
            ],
            [
                'key' => 'calories_burned',
                'label' => 'Calories Burned',
                'type' => 'text',
            ],
            [
                'key' => 'steps',
                'label' => 'Steps',
                'type' => 'text',
            ],
        ];

        //view
        return view('page.workout.index', compact('workoutList', 'columns'));
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
        $auth = Auth::user();

        //Validation start
        $validate = $request->validate([
            'activity_type' => 'required|string',
            'duration_minutes' => 'required|integer',
            'distance_km' => 'nullable|numeric',
            'calories_burned' => 'nullable|numeric',
            'steps' => 'nullable|integer|min:0',
        ]);

        $activity = strtolower($validate['activity_type']);

        if (!in_array($activity, ['running', 'walking', 'treadmill'])) {
            $validate['steps'] = null;
        }

        if ($activity === 'lifting') {
            $validate['distance_km'] = null;
        }


        try{
            $validate['user_id'] = $auth->id;

            WorkoutTracker::create($validate);

            ActivityLog::create([
                'user_id' => $auth->id,
                'type' => 'Workout',
                'description' => "Performed a {$validate['activity_type']} session lasting {$validate['duration_minutes']} mins, covering " . ($validate['distance_km'] ?? 0) . " km and burning " . ($validate['calories_burned'] ?? 0) . " kcal.",
                'status' => 'completed',
            ]);


            return redirect()->route('workout-tracker')->with('success', 'Workout Added Successfully');
        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to save workout.');
        }


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $workout = WorkoutTracker::findOrFail($id);
        $workout = WorkoutTracker::where('user_id', Auth::id())->findOrFail($id);

        // remove the json once input to the address bar.
        // if (! request()->expectsJson()) abort(404);

        return response()->json($workout);
    }

    /**
     * Search Filter
     */
    public function search(Request $request){
        $auth = Auth::user();
        $query = WorkoutTracker::where('user_id', $auth->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity_type', 'like', "%{$search}%")
                  ->orWhere('duration_minutes', 'like', "%{$search}%")
                  ->orWhere('distance_km', 'like', "%{$search}%")
                  ->orWhere('calories_burned', 'like', "%{$search}%")
                  ->orWhere('steps', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);

        if(!in_array($perPage, [10, 25, 50], true)) $perPage = 10;

        $workouts = $query->latest()->paginate($perPage); // It now depends to the selection of the user.
        return response()->json($workouts);
    }

    /**
     * Export All
     */
    public function exportAll(Request $request){
        $auth = Auth::user();
        $query = WorkoutTracker::where('user_id', $auth->id);

        if($request->filled('search')){
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('activity_type', 'like', "%{$search}%")
                  ->orWhere('duration_minutes', 'like', "%{$search}%")
                  ->orWhere('distance_km', 'like', "%{$search}%")
                  ->orWhere('calories_burned', 'like', "%{$search}%");
            });;
        }

        $workouts = $query->latest()->get();
        return response()->json($workouts);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkoutTracker $workoutTracker) { }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        $auth = Auth::user();

        // $workout = WorkoutTracker::findOrFail($id);
        $workout = WorkoutTracker::where('user_id', $auth->id)->findOrFail($id);

        $validate = $request->validate([
            'activity_type' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'distance_km' => 'nullable|numeric|min:0',
            'calories_burned' => 'nullable|numeric|min:0',
            'steps' => 'nullable|integer|min:0',
        ]);

        $activity = strtolower($validate['activity_type']);

        if (!in_array($activity, ['running', 'walking', 'treadmill'])) {
            $validate['steps'] = null;
        }

        if ($activity === 'lifting') {
            $validate['distance_km'] = null;
        }


        try{
            $workout->update($validate);
            ActivityLog::create([
                'user_id' => $auth->id,
                'type' => 'Workout',
                'description' => "Updated a previous {$validate['activity_type']} workout record to {$validate['duration_minutes']} mins.",
                'status' => 'updated',
            ]);


            return back()->with('success', 'Workout updated successfully!');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to update workout.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        $auth = Auth::user();

        // $workout = WorkoutTracker::findOrFail($id);
        $workout = WorkoutTracker::where('user_id', $auth->id)->findOrFail($id);

        $workDetails = "{$workout->activity_type} session recorded on " . $workout->created_at->format('M d, Y');

        $workout->delete();
        ActivityLog::create([
            'user_id' => $auth->id,
            'type' => 'Workout',
            'description' => "Deleted a workout entry of {$workDetails}",
            'status' => 'deleted',
        ]);
        return back()->with('success', 'Workout deleted successfully!');
    }
}
