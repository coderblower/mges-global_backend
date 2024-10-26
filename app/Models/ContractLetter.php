<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractLetter extends Model
{
    use HasFactory;


    protected $fillable = [
        'primary_candidates',
        'confirmed_candidates',
        'agent_id',
        'demand_letter_id',
        'agency_agree',
        'agency_reject',
        'admin_approve',
        'admin_reject',
        'custom_message',
        'admin_sent_agent'
    ];

    protected $casts = [
        'primary_candidates' => 'array',
        'confirmed_candidates' => 'array',
        'custom_message' => 'array',
        'admin_sent_agent' => 'date',
    ];

    // Define relationship to the User model (agent_id holds user_id)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Define the relationship with DemandLetterIssue
    public function demandLetterIssue()
    {
        return $this->belongsTo(DemandLetterIssue::class, 'demand_letter_id');
    }

    public function form()
    {
        return $this->hasOne(ContractLetterForm::class);
    }


}
