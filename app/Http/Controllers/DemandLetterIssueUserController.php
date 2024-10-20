<?php

namespace App\Http\Controllers;

use App\Models\DemandLetterIssueUser;
use Illuminate\Http\Request;
use App\Models\User;

class DemandLetterIssueUserController extends Controller
{
    public function store(Request $request)
    {

        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'demand_letter_issue_id' => 'required|exists:demand_letter_issues,id',
        //     'candidate_list' => 'required|array',
        // ]);


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


    public function getSelectedCandidate($demmand_letter_id)
    {
        $agent_id = auth()->user()->id;
        $demandLetterIssueUser = DemandLetterIssueUser::where('user_id', $agent_id)
            ->where('demand_letter_issue_id', $demmand_letter_id)
            ->first();

        // Step 2: Get the candidate_list and decode it if necessary
        $candidateList =$demandLetterIssueUser->candidate_list;

        // Step 3: Retrieve users whose ids are in the candidate_list
        $users = User::whereIn('id', $candidateList)
            ->where('created_by', $agent_id)
            ->where('role_id', 5)
            ->with([
                'candidate',
                'partner',
                'createdBy',
                'candidate.designation',
                'role'
            ])
            ->get();

        return response()->json($users);
        
    }


    public function MaximumCandidateSelected()
    {
        $demandLetterIssueUser = DemandLetterIssueUser::where('user_id', 8)
        ->where('demand_letter_issue_id', 2)
        ->first();

        // Step 2: Get the candidate_list and decode it if necessary
        $candidateList = json_decode($demandLetterIssueUser->candidate_list, true);

        $firstQueryCount =   User::whereIn('id', $candidateList)
            ->where('created_by', auth()->user()->id)
            ->where('role_id', 5)
            ->count(); // Get the count of the first query

        // Second query without demandLetterIssues conditions
        $secondQueryCount = User::where('created_by', auth()->user()->id)
            ->where('role_id', 5)
            ->count(); // Get the count of the second query

        // Compare the counts
        return $firstQueryCount >= $secondQueryCount;
        
    }




}
