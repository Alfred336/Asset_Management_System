<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'staff_id',
        'asset_tag',
        'serial_number',
        'model',
        'manufacturer',
        'device_type',
        'operating_system',
        'os_version',
        'processor',
        'ram_gb',
        'storage_gb',
        'storage_type',
        'ip_address',
        'mac_address',
        'hostname',
        'location',
        'status',
        'purchase_date',
        'purchase_cost',
        'warranty_expiry',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
        'ram_gb' => 'integer',
        'storage_gb' => 'integer',
        'status' => 'string',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(DeviceStatusHistory::class)->orderBy('created_at', 'desc');
    }
}
