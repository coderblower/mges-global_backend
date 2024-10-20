<?php

namespace App\Http\Controllers;

use App\Models\DemandLetterIssueUser;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DemandLetterIssueUserController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'demand_letter_issue_id' => 'required|exists:demand_letter_issues,id',
            'candidate_list' => 'required|array',
        ]);

        // Try to find an existing record with the same demand_letter_issue_id and user_id
        $entry = DemandLetterIssueUser::where('user_id', $request->user_id)
            ->where('demand_letter_issue_id', $request->demand_letter_issue_id)
            ->first();

        if ($entry) {
            // Update the existing entry's candidate_list
            $entry->candidate_list = array_merge( $request->candidate_list, $entry->candidate_list);
            $entry->save();
        } else {
            // Create a new entry if none exists
            $entry = DemandLetterIssueUser::create([
                'user_id' => $request->user_id,
                'demand_letter_issue_id' => $request->demand_letter_issue_id,
                'candidate_list' => $request->candidate_list,
            ]);
        }

        // Return the created or updated entry
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

    public function candidateListToAssign(Request $request){




        $candidateList=[];
        $demandLetterIssueUser = DemandLetterIssueUser::where('user_id', auth()->user()->id)
        ->where('demand_letter_issue_id', $request->can_id)
        ->first();

    // Step 2: Get the candidate_list

    if($demandLetterIssueUser ){
        $candidateList = $demandLetterIssueUser->candidate_list;
    }

    // Step 3: Query users that are not in the candidate list
    $users = User::whereNotIn('id', $candidateList) // Use whereNotIn to filter out the candidate IDs
        ->where('created_by', auth()->user()->id)
        ->where('role_id', 5)
        ->whereHas('candidate', function($query) {
            $query->where('approval_status', 'approved'); // Filter for approved candidates
        })
        ->with('candidate')
        ->paginate(10);

        return $users;
    }




}
