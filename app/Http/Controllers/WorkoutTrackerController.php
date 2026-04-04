<?php

namespace App\Http\Controllers;

use App\Models\WorkoutTracker;
use Illuminate\Http\Request;

class WorkoutTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workoutList = WorkoutTracker::where('user_id', auth()->id())->latest()->get();
        //view
        return view('page.workout.index', compact('workoutList'));
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
        //Validation start

        try{
            $validate = $request->validate([
                'activity_type' => 'required|string',
                'duration_minutes' => 'required|integer',
                'distance_km' => 'nullable|numeric',
                'calories_burned' => 'nullable|numeric',
            ]);
    
            $validate['user_id'] = auth()->id();
    
            WorkoutTracker::create($validate);
    
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
        $workout = WorkoutTracker::findOrFail($id);
        return response()->json($workout);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkoutTracker $workoutTracker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $workout = WorkoutTracker::findOrFail($id);
        $data = [
            'activity_type' => $request->activity_type,
            'duration_minutes' => $request->duration_minutes,
            'distance_km' => $request->distance_km,
            'calories_burned' => $request->calories_burned,
        ];

        try{
            $workout->update($data);
            return back()->with('success', 'Workout updated successfully!');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to update workout.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $workout = WorkoutTracker::findOrFail($id);
        $workout->delete();
        return back()->with('success', 'Workout deleted successfully!');
    }
}
