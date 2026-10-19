<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairJob extends Model
{
    use HasFactory;

    protected $table = 'repair_jobs';

    protected $primaryKey = 'job_id';

    protected $fillable = ['device_id', 'technician_id', 'issue_description', 'status', 'diagnosis', 'labor_cost', 'parts_cost', 'received_date', 'completed_date'];

    protected $casts = ['received_date' => 'date', 'completed_date' => 'date', 'labor_cost' => 'decimal:2', 'parts_cost' => 'decimal:2'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id', 'technician_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'job_id', 'job_id');
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->labor_cost + (float) $this->parts_cost;
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) ($this->payments_sum_amount_paid ?? $this->payments()->sum('amount_paid'));
    }

    public function getBalanceRemainingAttribute(): float
    {
        return $this->total_cost - $this->total_paid;
    }

    public function getLabelAttribute(): string
    {
        return 'Job #'.$this->job_id.' — '.($this->device?->label ?? 'Device');
    }
}
