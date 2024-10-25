<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractLetterForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_title',
        'employers_title',
        'work_address',
        'employer_phone',
        'email',
        'description',
        'issued_date',
    ];
    public function contractLetter()
    {
        return $this->belongsTo(ContractLetter::class);
    }

}
