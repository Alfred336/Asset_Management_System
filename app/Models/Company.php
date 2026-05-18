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
