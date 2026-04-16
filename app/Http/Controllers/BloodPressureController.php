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
}
