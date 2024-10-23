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
    ];

    protected $casts = [
        'primary_candidates' => 'array',
        'confirmed_candidates' => 'array',
        'custom_message' => 'array',
    ];

    // Many-to-Many relationship with User
    public function users()
    {
        return $this->belongsToMany(User::class, 'contract_letter_user'); // Define pivot table if necessary
    }

    // One-to-One relationship with DemandLetterIssue
    public function demandLetterIssue()
    {
        return $this->belongsTo(DemandLetterIssue::class, 'demand_letter_id');
    }
}
