<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'hire_date',
        'salary',
        'employment_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'employment_type' => 'string',
        'status' => 'string',
    ];

    /**
     * Scope a query to only include staff belonging to companies accessible by the given user.
     */
    public function scopeForUser($query, ?User $user = null)
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->canAccessAllCompanies()) {
            return $query;
        }

        return $query->whereIn('company_id', $user->accessibleCompanyIds());
    }

    /**
     * Get the company that the staff member belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the devices assigned to the staff member.
     */
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get the full name of the staff member.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
