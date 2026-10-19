<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    use HasFactory;

    protected $primaryKey = 'technician_id';

    protected $fillable = ['first_name', 'middle_name', 'last_name', 'specialization', 'hire_date', 'phone'];

    protected $casts = ['hire_date' => 'date'];

    public function repairJobs(): HasMany
    {
        return $this->hasMany(RepairJob::class, 'technician_id', 'technician_id');
    }

    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])->filter()->implode(' ');
    }
}
