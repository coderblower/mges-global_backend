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
        // Fetch data with eager loading of `user` and `preDemandLetter`
        // $demandLetters = DemandLetterIssue::whereNull('admin_verify') // Condition where `admin_verify` is null
        //     ->whereNotNull('agency_verify') // Condition where `agency_verify` is not null
        //     ->with(['user', 'preDemandLetter']) // Eager load related `user` and `preDemandLetter`
        //     ->get();

        // return response()->json($demandLetters);

        $usersWithLettersAndPartners = User::with([
            'demandLetterIssues.preDemandLetter', // Load PreDemandLetter data
            'demandLetterIssues.partner'          // Load Partner data
        ])
        ->whereHas('demandLetterIssues', function($query) {
            // Additional filter, for example:
            $query->whereNotNull('agency_verify');

        })
        ->get();



        return $usersWithLettersAndPartners;
    }

    public function approve_demand_letter($id)
    {
        // Fetch data with eager loading of `user` and `preDemandLetter`
        // $demandLetters = DemandLetterIssue::whereNull('admin_verify') // Condition where `admin_verify` is null
        //     ->whereNotNull('agency_verify') // Condition where `agency_verify` is not null
        //     ->with(['user', 'preDemandLetter']) // Eager load related `user` and `preDemandLetter`
        //     ->get();

        // return response()->json($demandLetters);
         // Change this to the user ID you want to update
        $user = User::with('demandLetterIssues')->find($id);

        if ($user) {

            $user->notify(new AdminSendDemandLetterToAgent($user));

            foreach ($user->demandLetterIssues as $issue) {
                // Update the admin_verify field to now()
                $issue->admin_verify = Carbon::now(); // Set to current date and time
                $issue->save(); // Save the changes

            }

            return response()->json(['message' => 'Admin verify updated successfully.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }

    }

    public function already(){
        $demandLetterIssues = DemandLetterIssue::whereNotNull('admin_verify')->get();

        return $demandLetterIssues;
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
