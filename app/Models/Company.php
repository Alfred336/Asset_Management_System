<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'address',
        'tax_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Scope a query to only include companies accessible by the given user.
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

        return $query->whereIn('id', $user->accessibleCompanyIds());
    }

    /**
     * Get the users assigned to the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')->withTimestamps();
    }

    /**
     * Get the staff members for the company.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Get the devices for the company.
     */
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
