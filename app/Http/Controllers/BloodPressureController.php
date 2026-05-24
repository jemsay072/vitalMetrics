<?php

namespace App\Http\Controllers;

use App\Models\BloodPressure;
use Illuminate\Http\Request;

class BloodPressureController extends Controller
{
    //index
    public function index(){
        $bpview = BloodPressure::where('user_id', auth()->id())->latest()->get();
        return view('page.bp.index', compact('bpview'));
    }

    /**
     * Function
     */
    private function detectTerms($s, $d){
        if ($s >= 140 || $d >= 90) return 'Hypertension';
        if (($s >= 120 && $s < 140) || ($d >= 80 && $d < 90)) return 'Prehypertension';
        if ($s < 120 && $d < 80) return 'Normal';

        return 'Unknown';
    }

    private function detectRiskLevel($s, $d){
        if($s >= 140 || $d >= 90) return 'High';
        if(($s >= 120 && $s < 140) || ($d >= 80 && $d < 90)) return 'Medium';
        return 'Low';
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request){

        // Validate
        try{
            $validate = $request->validate([
                'systolic' => 'required|integer',
                'diastolic' => 'required|integer',
                'pulse' => 'nullable|integer',
                'notes' => 'present|string',
                'reading_time' => 'required|date_format:H:i',
            ]);

            $validate['terms'] = $this->detectTerms(
                $request->systolic,
                $request->diastolic,
            );

            $validate['risk_level'] = $this->detectRiskLevel(
                $request->systolic,
                $request->diastolic,
            );

            $validate['user_id'] = auth()->id();

            BloodPressure::create($validate);

            return redirect()->route('bp.index')->with('success', 'Blood Pressure Added Successfully.');

        } catch (\Exception $e){
            return redirect()->back()->with('error', 'Failed to save Blood Pressure.');
        }
    }

    /**
     * Search Filter
     */
    public function search(Request $request){
        $query = BloodPressure::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('systolic', 'like', "%{$search}%")
                  ->orWhere('diastolic', 'like', "%{$search}%")
                  ->orWhere('terms', 'like', "%{$search}%")
                  ->orWhere('risk_level', 'like', "%{$search}%");
            });
        }

        $bp = $query->latest()->paginate(10); // 10 items per page
        return response()->json($bp);
    }


    /**
     * Display the specified resource
     */
    public function show($id){
        $bp = BloodPressure::findOrFail($id);
        return response()->json($bp);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id){
        $bp = BloodPressure::findOrFail($id);
        $data = [
            'systolic' => $request->systolic,
            'diastolic' => $request->diastolic,
            'pulse' => $request->pulse,
            'notes' => $request->notes,
            'reading_time' => $request->reading_time,
        ];

        $data['terms'] = $this->detectTerms(
            $request->systolic,
            $request->diastolic,
        );

        $data['risk_level'] = $this->detectRiskLevel(
            $request->systolic,
            $request->diastolic,
        );

        try{
            $bp->update($data);
            return back()->with('success', 'Blood Pressure updated successfully!');

        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to update Blood Pressure.');
        }
    }

    /**
     * Delete the specified resource from storage.
     */

    public function destroy($id){
        $bp = BloodPressure::findOrFail($id);

        $bp->delete();
        return back()->with('Success', 'Blood Pressure Deleted Successfully!.');
    }
}
