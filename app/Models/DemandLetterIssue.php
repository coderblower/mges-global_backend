<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandLetterIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'predemand_letter_id',
        'agency_verify',
        'admin_verify',
        'catagory_customize',
    ];

    protected $casts = [
        'catagory_customize' => 'array',
    ];

    protected $dates = [
        'agency_verify',
        'admin_verify',
    ];



    public function preDemandLetter()
    {
        return $this->belongsTo(PreDemandLetter::class, 'predemand_letter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'user_id', 'user_id');
    }



}
