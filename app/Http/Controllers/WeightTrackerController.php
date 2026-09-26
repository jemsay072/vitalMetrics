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

        $columns = [
            [
                'key' => 'weight',
                'label' => 'Weight',
                'type' => 'text',
            ],
            [
                'key' => 'notes',
                'label' => 'Notes',
                'type' => 'text',
            ],
            [
                'key' => 'formatted_date',
                'label' => 'Date',
                'type' => 'reading',
            ],
        ];

        return view('page.weight-tracker.index', compact('wtView', 'columns'));
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
     * Search Filter
     */
    public function search(Request $request){
        $query = WeightTracker::where('user_id', auth()->id());

        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('weight', 'like', "%{$search}%")
                  ->orWhere('measurement_date', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);

        if(!in_array($perPage, [10, 25, 50], true)) $perPage = 10;

        $weight = $query->latest()->paginate($perPage);
        return response()->json($weight);
    }

    /**
     * Export All
     */
    public function exportAll(Request $request){
        $query = WeightTracker::where('user_id', auth()->id());

        if($request->filled('search')){
            $search = $request->search;

            $query->where(function($q) use ($search){
                $q->where('weight', 'like', "%{$search}%")
                  ->orWhere('measurement_date', 'like', "%{$search}%");
            });
        }

        $weight = $query->latest()->get();
        return response()->json($weight);
    }

    /**
     * Display the specified resource.
     */
    public function show(WeightTracker $weightTracker, $id)
    {
        //
        $weight = WeightTracker::findOrFail($id);
        return response()->json($weight);
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
    public function update(Request $request, WeightTracker $weightTracker, $id)
    {
        //
        $weight = WeightTracker::findOrFail($id);
        $data = [
            'weight' => $request->weight,
            'measurement_date' =>$request->measurement_date,
            'notes' => $request->notes,
        ];

        try {
            $weight->update($data);
            return back()->with('success', 'Weight updated successfully!');
        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to update Weight.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeightTracker $weightTracker, $id)
    {
        $weight = WeightTracker::findOrFail($id);

        $weight->delete();
        return back()->with('Success', 'Blood Pressure Deleted Successfully!.');
    }
}
