<?php

namespace App\Http\Controllers;

use App\Models\DemandLetter;
use Illuminate\Http\Request;

class DemandLetterController extends Controller
{
    public function index()
    {
        $demandLetters = DemandLetter::paginate(10);
        return response()->json($demandLetters);
    }

    public function agentView()
    {
        $demandLetters = DemandLetter::where('status', 'approved')->paginate(10);
        return response()->json($demandLetters);
    }

    public function store(Request $request)
    {

        $request->validate([
            'company_name' => 'required|string',
            'license_no' => 'required|string',
            'visa_number' => 'required|string',
            'visa_date' => 'required|date',
            'positions' => 'required|array',
            'terms_conditions' => 'required|string',
        ]);

        $demandLetter = DemandLetter::create($request->all());

        return response()->json($demandLetter, 201);
    }

    public function destroy($id)
    {
        $demandLetter = DemandLetter::find($id);

        if (!$demandLetter) {
            return response()->json(['message' => 'Demand letter not found'], 404);
        }

        $demandLetter->delete();

        return response()->json(['message' => 'Demand letter deleted successfully'], 200);
    }

    public function show($id)
    {
        $demandLetter = DemandLetter::find($id);

        if (!$demandLetter) {
            return response()->json(['message' => 'Demand letter not found'], 404);
        }

        return response()->json($demandLetter, 200);
    }


    public function changeStatus($id){

        $predemandLetter = DemandLetter::find($id);
        if($predemandLetter){
                $predemandLetter->status = 'approved';
                $predemandLetter->save();
        }

        return response()->json([
            'success'=> 'Status approved Successfully',
            'data' => $predemandLetter
        ], 200);

    }



}
