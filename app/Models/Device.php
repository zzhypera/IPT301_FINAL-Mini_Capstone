<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $primaryKey = 'device_id';

    protected $fillable = ['customer_id', 'brand', 'model', 'serial_number', 'accessories'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function repairJobs(): HasMany
    {
        return $this->hasMany(RepairJob::class, 'device_id', 'device_id');
    }

    public function getLabelAttribute(): string
    {
        return trim($this->brand.' '.$this->model);
    }
}
