<?php

namespace App\Http\Controllers;

use App\Models\DemandLetterIssueUser;
use Illuminate\Http\Request;

class DemandLetterIssueUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'demand_letter_issue_id' => 'required|exists:demand_letter_issues,id',
            'candidate_list' => 'required|array',
        ]);

        $entry = DemandLetterIssueUser::create([
            'user_id' => $request->user_id,
            'demand_letter_issue_id' => $request->demand_letter_issue_id,
            'candidate_list' => $request->candidate_list,
        ]);

        return response()->json($entry, 201);
    }

    public function index()
    {
        $entries = DemandLetterIssueUser::all();
        return response()->json($entries);
    }
}
