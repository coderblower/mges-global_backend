<?php

namespace App\Http\Controllers;

use App\Models\ContractLetterForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContractLetterFormController extends Controller
{
    public function index()
    {
        $forms = ContractLetterForm::all();
        return response()->json($forms);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'contract_title' => 'required',
            'employers_title' => 'required',
            'work_address' => 'required',
            'employer_phone' => 'required',
            'email' => 'required|email',
            'description' => 'required',
            'issued_date' => 'required|date',
            'contract_letter_id' => 'required|exists:contract_letters,id', // ensure the contract_letter_id exists
        ]);
    
        // Create a new ContractLetterForm
        $contractLetterForm = new ContractLetterForm();
        $contractLetterForm->contract_title = $request->contract_title;
        $contractLetterForm->employers_title = $request->employers_title;
        $contractLetterForm->work_address = $request->work_address;
        $contractLetterForm->employer_phone = $request->employer_phone;
        $contractLetterForm->email = $request->email;
        $contractLetterForm->description = $request->description;
        $contractLetterForm->issued_date = $request->issued_date;
        $contractLetterForm->contract_letter_id = $request->contract_letter_id; // Ensure this line is present
    
        // Save the model
        $contractLetterForm->save();
    
        return response()->json(['message' => 'Contract Letter Form created successfully!', 'data' => $contractLetterForm], 201);
    }
    
    public function show($contractLetterId)
    {
        // Use where to find the form by contract_letter_id
        $form = ContractLetterForm::where('contract_letter_id', $contractLetterId)->first();
    
        if (!$form) {
            return response()->json(['message' => 'Contract Letter Form not found.'], 404);
        }
    
        return response()->json($form);
    }
    

    public function update(Request $request, $contractLetterId)
    {
        $form = ContractLetterForm::where('contract_letter_id', $contractLetterId)->first();

        // $validated = $request->validate([
        //     'contract_title' => 'sometimes|required|string|max:255',
        //     'employers_title' => 'sometimes|required|string|max:255',
        //     'work_address' => 'sometimes|required|string|max:255',
        //     'employer_phone' => 'sometimes|required|string|max:20',
        //     'email' => 'sometimes|required|email',
        //     'description' => 'sometimes|required|string',
        //     'issued_date' => 'sometimes|required|date',
        //     'contract_letter_id' => 'sometimes|required|exists:contract_letters,id',
        // ]);

        $form->update($request->all());

        return response()->json($form);
    }

    public function destroy($id)
    {
        $form = ContractLetterForm::findOrFail($id);
        $form->delete();

        return response()->json(null, 204);
    }
}
