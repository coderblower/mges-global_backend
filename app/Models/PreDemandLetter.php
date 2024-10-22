<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDemandLetter extends Model
{
    use HasFactory;


    protected $guarded = ['id'];

    protected $casts = [
        'positions' => 'array',
        'bd_agency_agree' => 'array',
        'approved_agency_list'=>'array',
        'admin_approved_pre_demand'=>'array'

    ];

    protected $attributes = [
        'positions' => '[]',          // default empty array
             // Setting a default empty array for the attribute
    ];

    public function demandLetterIssues()
    {
        return $this->hasMany(DemandLetterIssue::class, 'predemand_letter_id');
    }
    // In App\Models\PreDemandLetter

}
