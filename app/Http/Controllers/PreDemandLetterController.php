<?php

namespace App\Http\Controllers;


use App\Models\DemandLetterIssue;
use App\Models\PreDemandLetter;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Partner;
use App\Notifications\AgentGetNewPreDemandLetterNotification;
use App\Notifications\AgencyGetNewDemandLetterOrder;
use App\Notifications\AgencySendDemandLetterToAdmin;
use App\Notifications\AdminSendDemandLetterToAgent;
use App\Notifications\HiringAgencyMakePreDemandLetter;




class PreDemandLetterController extends Controller
{
    public function index()
    {
        $predemandLetters = PreDemandLetter::paginate(10);
        return response()->json($predemandLetters);
    }

    public function agentView(Request $request)
    {
        $predemandLetters = PreDemandLetter::where('status', 'approved')
        ->whereJsonContains('approved_agency_list', $request->userId) // Assuming you want to check the current user's ID
        ->paginate(10);

    return response()->json($predemandLetters);
    }

    public function agreed_predemand_letter(Request $request)
    {
        $predemandLetters = PreDemandLetter::whereNotNull('bd_agency_agree') // Assuming you want to check the current user's ID
        ->paginate(10);

        return response()->json($predemandLetters);

    }

    public function getUsersFromBdAgencyAgree($id)
    {
        // Find the PreDemandLetter by ID
        $preDemandLetter = PreDemandLetter::find($id);

        if (!$preDemandLetter) {
            return response()->json(['message' => 'PreDemandLetter not found.'], 404);
        }

        // Check if bd_agency_agree is set and is an array
        if (is_array($preDemandLetter->bd_agency_agree) && count($preDemandLetter->bd_agency_agree) > 0) {
            // Fetch all users whose ID is in the bd_agency_agree array
            $users = User::whereIn('id', $preDemandLetter->bd_agency_agree)->with('partner')->get();

            return response()->json([
                'pre_demand_letter' => $preDemandLetter,
                'users' => $users
            ]);
        }
    }





    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'description' => 'required',
            'positions' => 'required|array',  // This ensures positions must be an array
            'terms_conditions' => 'required|string',
        ]);

        // Log the input to ensure positions is an array


        // Create the PreDemandLetter record
        $demandLetter = PreDemandLetter::create([
            'description' => $request->input('description'),
            'positions' => $request->input('positions'), // Ensure it's passed as an array
            'terms_conditions' => $request->input('terms_conditions'),
            'bd_agency_agree' => [],
        ]);

        // Notify all admins
        $admins = User::where('role_id', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new HiringAgencyMakePreDemandLetter());
        }

        // Return a JSON response with the created demand letter
        return response()->json($demandLetter, 201);
    }


    public function destroy($id)
    {
        $predemandLetter = PreDemandLetter::find($id);

        if (!$predemandLetter) {
            return response()->json(['message' => 'Pre Demand letter not found'], 404);
        }

        $predemandLetter->delete();

        return response()->json(['message' => 'Pre Demand letter deleted successfully'], 200);
    }

    public function show($id)
    {
        $predemandLetter = PreDemandLetter::find($id);

        if (!$predemandLetter) {
            return response()->json(['message' => 'Pre Demand letter not found'], 404);
        }

        return response()->json($predemandLetter, 200);
    }

    public function changeStatus($id){

        $predemandLetter = PreDemandLetter::find($id);
        if($predemandLetter){
                $predemandLetter->status = 'approved';
                $predemandLetter->save();
        }

        return response()->json([
            'success'=> 'Status approved Successfully',
            'data' => $predemandLetter
        ], 200);

    }



    public function agencyAgreementStatusChange( $id, $userId){

        $predemandLetter = PreDemandLetter::find($id);

        $existingAgreements = $predemandLetter->bd_agency_agree ?? [];
        $newAgreements = array_unique(array_merge($existingAgreements, [$userId]));


        if(in_array($userId, $existingAgreements)){
            $newAgreements = array_diff($newAgreements, [$userId]);
        }






        if($predemandLetter){

                $predemandLetter->bd_agency_agree = $newAgreements;
                $predemandLetter->save();
        }

        $usersWithRole6 = User::where('role_id', 6)->get();

        // Send notification to each user
        foreach ($usersWithRole6 as $user) {
            $user->notify(new AgencyGetNewDemandLetterOrder());
        }


        return response()->json([
            'success'=> 'Status changed Successfully',
            'data' => $predemandLetter
        ], 200);


        // $validator = Validator::make($request->all(), [
        //     'bd_agency_agree' => 'array',
        //     'bd_agency_agree.*' => [
        //         'integer',
        //         function ($attribute, $value, $fail) {
        //             $user = User::find($value);
        //             if (!$user || $user->role_id != 5) {
        //                 $fail('The user is not allowed to agree.');
        //             }
        //         }
        //     ],
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // dd(auth()->user()->id);



    }

    public function agreed_pdl_to_agency(){

        $preDemandLetters = PreDemandLetter::whereJsonLength('bd_agency_agree', '>', 0)->paginate(10);
        return response()->json($preDemandLetters);
    }


    public function agreed_pdl_to_agency_single($id){


        $preDemandLetter = PreDemandLetter::find($id);
        $userIds = $preDemandLetter->bd_agency_agree;

        // Fetch user data using those IDs
        $users = User::whereIn('id', $userIds)->get();
        $preDemandLetter->users = $users;



        return $preDemandLetter;
    }

    public function demand_letter_make($id, $preId){
        $deamndLetter = DemandLetterIssue::create([
            'user_id' => $id,
            'predemand_letter_id' => $preId,
            'agency_verify' => now()
        ]);

        $admin = User::where('role_id', 1)->get();

        // Send notification to each user
        foreach ($admin as $user) {
            $user->notify(new AgencySendDemandLetterToAdmin());
        }

        return $deamndLetter;
    }


    public function show_demand_letter($id){
        $demandLetterIssues = DemandLetterIssue::where('predemand_letter_id', $id)
            ->with('preDemandLetter') // Eager load the related PreDemandLetter data
            ->get();

        return $demandLetterIssues;
    }



    public function getFilteredDemandLettersWithUser()
{
    // Fetch DemandLetterIssue data with related preDemandLetter and user
    $demandLetterIssues = DemandLetterIssue::with(['preDemandLetter', 'user', 'partner']) // Eager load preDemandLetter and user relationships
        ->whereNotNull('agency_verify') // Filter condition for agency_verify

        ->paginate(10); // Paginate results to 10 per page

    // Prepare the data for React consumption
    $response = [
        'data' => $demandLetterIssues->items(),  // Get paginated data
        'pagination' => [
            'total' => $demandLetterIssues->total(),
            'per_page' => $demandLetterIssues->perPage(),
            'current_page' => $demandLetterIssues->currentPage(),
            'last_page' => $demandLetterIssues->lastPage(),
        ]
    ];

    // Return the response as JSON for the frontend
    return response()->json($response);
}

    public function approve_demand_letter($id)
    {
        // Find the DemandLetterIssue by ID and eager load the related user
        $demandLetterIssue = DemandLetterIssue::with('user')->find($id);

        if (!$demandLetterIssue) {
            return response()->json(['message' => 'Demand Letter Issue not found.'], 404);
        }

        // Update the admin_verify field to the current time
        $demandLetterIssue->admin_verify = \Carbon\Carbon::now();
        $demandLetterIssue->save(); // Save the updated model

        // Get the user associated with this specific DemandLetterIssue
        $user = $demandLetterIssue->user;


        if ($user) {
            // Send notification to this specific user
            $user->notify(new AdminSendDemandLetterToAgent($user));

            return response()->json(['message' => 'Admin verify updated successfully for user ID ' . $user->id]);
        } else {
            return response()->json(['message' => 'User not found for this demand letter issue.'], 404);
        }
    }


    public function already(){
        $demandLetterIssues = DemandLetterIssue::whereNotNull('admin_verify')->get();

        return $demandLetterIssues;
    }

    public function adminApprovedAgentAgreedPreDemand($id, Request $request)
    {
        // Find the PreDemandLetter by ID
        $predemand = PreDemandLetter::find($id);
        
        // Ensure the PreDemandLetter exists
        if (!$predemand) {
            return response()->json(['message' => 'PreDemandLetter not found'], 404);
        }
    
        // Set the admin_approved_pre_demand column with the incoming request payload
        $predemand->admin_approved_pre_demand = $request->all(); // Laravel automatically casts this to JSON
        
        // Save the changes to the database
        $predemand->save();
    
        // Return the updated PreDemandLetter as JSON response
        return response()->json($predemand, 200);
    }
    


    public function getAllAgent(){
        try {
            $data = Partner::orderby('id', 'desc')->with('user')->with('role')->get();
            return response()->json([
                'success' => true,
                'message' => 'Successful!',
                'data' => $data,
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function adminAssignAgentForPreDemandLetter(Request $request)
{

    // Validate the request to ensure `pre_demand_id` and `selectedAgents` are provided
    $request->validate([
        'pre_demand_id' => 'required|integer',
        'selectedAgents' => 'required|array',
        'selectedAgents.*' => 'integer' // Ensure all elements of the array are integers
    ]);


    // Find the PreDemandLetter by the provided pre_demand_id
    $preDemandLetter = PreDemandLetter::findOrFail($request->pre_demand_id);

    // Get the selected agents from the request
    $selectedAgents = $request->selectedAgents;


    // Update the `approved_agency_list` with the new agents
    $preDemandLetter->approved_agency_list = $selectedAgents;
    $preDemandLetter->status= "approved";

    // Save the changes to the PreDemandLetter
    $preDemandLetter->save();


    // Notify each user (agent) in the `selectedAgents` array
    foreach ($selectedAgents as $agentId) {
        // Find the agent (user) by their ID
        $agent = User::find($agentId);


        if ($agent) {
            // Send notification to the agent
            $agent->notify(new AgentGetNewPreDemandLetterNotification());
        }
    }



    // Return a response
    return response()->json([
        'success' => true,
        'message' => 'Agents have been successfully assigned.',
    ]);
}







}
