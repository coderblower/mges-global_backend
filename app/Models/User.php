<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'created_by',
        'agency_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * JWT Identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Custom JWT Claims
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relationships

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'id', 'user_id');
    }

    public function report()
    {
        return $this->belongsTo(CandidateMedicalTest::class, 'id', 'user_id');
    }

    public function preskilled()
    {
        return $this->belongsTo(PreSkilledTest::class, 'id', 'user_id');
    }

    public function skill()
    {
        return $this->belongsTo(SkillTest::class, 'id', 'user_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'id', 'user_id');
    }

    public function medicalCenter()
    {
        return $this->belongsTo(User::class, 'medical_center_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function child()
    {
        return $this->hasMany(User::class, 'created_by', 'id');
    }

    // Correct demandLetterIssues relation using belongsToMany for pivot table
    public function demandLetterIssues()
    {
        return $this->belongsToMany(DemandLetterIssue::class, 'demand_letter_issue_user', 'user_id', 'demand_letter_issue_id');
    }
    // In App\Models\User
  // In App\Models\User


}
