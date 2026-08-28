<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStatusHistory extends Model
{
    protected $fillable = ['device_id', 'status', 'notes', 'changed_by'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeForUser($query, ?User $user = null)
    {
        return $query->whereHas('device', function ($q) use ($user) {
            $q->forUser($user);
        });
    }
}

