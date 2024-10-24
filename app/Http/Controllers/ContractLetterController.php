<?php

namespace App\Http\Controllers;

use App\Models\ContractLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class ContractLetterController extends Controller
{
    public function index()
    {

        $contractLetters = ContractLetter::all();
        return view('contract_letters.index', compact('contractLetters'));
    }

    public function create()
    {
        return view('contract_letters.create');
    }

    public function store(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'primary_candidates' => 'required|array',
            'demand_letter_id' => 'required|exists:demand_letter_issues,id',
        ]);

        // Convert demand_letter_id to an integer
        $data['demand_letter_id'] = (int) $data['demand_letter_id'];

        // Automatically set agent_id from authenticated user
        $data['agent_id'] = auth()->user()->id;

        // Check if a contract letter already exists for the authenticated user
        $existingContractLetter = ContractLetter::where('agent_id', $data['agent_id'])
            ->where('demand_letter_id', $data['demand_letter_id']) // Check for the specific demand letter
            ->first();

        if ($existingContractLetter) {
            return response()->json(['message' => 'Contract letter already created for this demand letter.'], 409); // Conflict status code
        }

        // Create a new ContractLetter
        $contractLetter = ContractLetter::create($data);

        return response()->json($contractLetter, 201); // Return the created contract letter with a success status code
    }


    public function already_send_varify(Request $request)
    {
        // Get all incoming request data
        $data = $request->all();

        // Convert demand_letter_id to an integer
        $data['demand_letter_id'] = (int) $data['demand_letter_id'];

        // Automatically set agent_id from authenticated user
        $data['agent_id'] = auth()->user()->id;

        Log::info('hello' , ['data'=>$data]);

        // Check if a contract letter already exists for the authenticated user
        $existingContractLetter = ContractLetter::where('agent_id', $data['agent_id'])
            ->where('demand_letter_id', $data['demand_letter_id']) // Check for the specific demand letter
            ->first();

        // Return response based on whether the contract letter exists
        if ($existingContractLetter) {
            return response()->json(['exists' => true], 200);
        }

        return response()->json(['exists' => false], 200);
    }

    public function agentApprove($id)
    {
        $contract = ContractLetter::where('demand_letter_id', $id)->first();

        if ($contract) {
            $contract->update([
                'agency_agree' => now(),
            ]);
            return response()->json(['message' => 'Agent approved successfully'], 200);
        } else {
            return response()->json(['message' => 'Contract not found'], 404);
        }
    }

    public function agentReject($id)
{
    $contract = ContractLetter::where('demand_letter_id', $id)->first();

    if ($contract) {
        $contract->update([
            'agency_reject' => now(),
        ]);
        return response()->json(['message' => 'Agent rejected successfully'], 200);
    } else {
        return response()->json(['message' => 'Contract not found'], 404);
    }
}


    public function agentShow()
    {
        $contracts = ContractLetter::whereNotNull('admin_approve')->get();
        return response()->json($contracts);
    }

    public function adminShow()
    {
        // Eager load users, demand letter issues, and pre-demand letters related to the contracts
        $contracts = ContractLetter::with([
                'agent',
                'agent.partner',
                'demandLetterIssue',
                'demandLetterIssue.preDemandLetter', // BelongsTo relationship

            ])
            ->whereNotNull('primary_candidates')
            ->whereNull('admin_approve')
            ->get();

        return response()->json($contracts);
    }

    public function adminShowApproved()
    {
        // Eager load users, demand letter issues, and pre-demand letters related to the contracts
        $contracts = ContractLetter::with([
                'agent',
                'agent.partner',
                'demandLetterIssue',
                'demandLetterIssue.preDemandLetter', // BelongsTo relationship

            ])
            ->whereNotNull('primary_candidates')
            ->whereNotNull('admin_approve')
            ->get();

        return response()->json($contracts);
    }


public function adminApprove($id)
{
    $contract = ContractLetter::find($id);

    if ($contract) {
        $contract->update([
            'admin_approve' => now(),
        ]);
        return response()->json(['message' => 'Admin approved successfully'], 200);
    } else {
        return response()->json(['message' => 'Contract not found'], 404);
    }
}

public function adminReject($id)
{
    $contract = ContractLetter::where('demand_letter_id', $id)->first();

    if ($contract) {
        $contract->update([
            'admin_reject' => now(),
        ]);
        return response()->json(['message' => 'Admin rejected successfully'], 200);
    } else {
        return response()->json(['message' => 'Contract not found'], 404);
    }
}



    public function show(ContractLetter $contractLetter)
    {
        return view('contract_letters.show', compact('contractLetter'));
    }

    public function edit(ContractLetter $contractLetter)
    {
        return view('contract_letters.edit', compact('contractLetter'));
    }

    public function update(Request $request, ContractLetter $contractLetter)
    {
        $data = $request->validate([
            'primary_candidates' => 'required|array',
            'confirmed_candidates' => 'required|array',
            'agent_id' => 'required|exists:users,id',
            'demand_letter_id' => 'required|exists:demand_letters,id',
            'agency_agreed' => 'nullable|date',
            'agency_reject' => 'nullable|date',
            'admin_approve' => 'nullable|date',
            'admin_reject' => 'nullable|date',
            'custom_message' => 'nullable|array',
        ]);

        $contractLetter->update($data);

        return redirect()->route('contract_letters.index')->with('success', 'Contract Letter updated successfully.');
    }

    public function destroy(ContractLetter $contractLetter)
    {
        $contractLetter->delete();

        return redirect()->route('contract_letters.index')->with('success', 'Contract Letter deleted successfully.');
    }
}
