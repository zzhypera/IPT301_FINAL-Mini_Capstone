<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    protected $fillable = ['first_name', 'middle_name', 'last_name', 'phone', 'email', 'street', 'city', 'province'];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'customer_id', 'customer_id');
    }

    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])->filter()->implode(' ');
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->street, $this->city, $this->province])->filter()->implode(', ');
    }
}
