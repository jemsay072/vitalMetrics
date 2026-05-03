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
     * Display the specified resource
     */
    public function show($id){
        $bp = BloodPressure::findOrFail($id);
        return response()->json($bp);
    }
}
