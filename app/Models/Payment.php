<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';

    protected $fillable = ['job_id', 'payment_date', 'amount_paid', 'payment_method', 'reference_number'];

    protected $casts = ['payment_date' => 'date', 'amount_paid' => 'decimal:2'];

    public function repairJob(): BelongsTo
    {
        return $this->belongsTo(RepairJob::class, 'job_id', 'job_id');
    }
}
