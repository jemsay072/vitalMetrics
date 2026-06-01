<?php

namespace App\Http\Controllers;

use App\Models\WeightTracker;
use Illuminate\Http\Request;

class WeightTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //View the Weight Tracker Page
        $wtView = WeightTracker::where('user_id', auth()->id())->latest()->get();

        return view('page.weight-tracker.index', compact('wtView'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //Validation using try catch
        try{
            // dd($request->all());

            //validate the data from the form.
            $validate = $request->validate([
                'weight' => 'required|numeric|min:1|max:500',
                'measurement_date' => 'required|date_format:H:i',
                'notes' => 'nullable|string',
            ]);

            $validate['user_id'] = auth()->id();

            WeightTracker::create($validate);

            return redirect()->route('weight')->with('success', 'Your Weight is Added Successfully.');

        } catch(\Exception $e){
            dd($e->getMessage());
            // return redirect()->back()->with('error', 'Failed to save Weight.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WeightTracker $weightTracker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WeightTracker $weightTracker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WeightTracker $weightTracker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeightTracker $weightTracker)
    {
        //
    }
}
