<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandLetterIssueUser extends Model
{
    use HasFactory;

    protected $table = 'demand_letter_issue_user';

    protected $fillable = [
        'user_id',
        'demand_letter_issue_id',
        'candidate_list'
    ];

    protected $casts = [
        'candidate_list' => 'array', // Cast the candidate_list as an array
    ];
}
