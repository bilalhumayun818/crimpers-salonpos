<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\Invoice;

use App\Traits\BelongsToBranch;

class Customer extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'name',
        'image_path',
        'phone',
        'social_media',
        'email',
        'birthday',
        'preferences',
        'notes',
        'pending_balance',
    ];

    protected $attributes = [
        'prepaid_credit' => 0.00,
    ];

    protected $casts = [
        'name' => \App\Casts\FlexibleEncryption::class,
        'phone' => \App\Casts\FlexibleEncryption::class,
        'email' => \App\Casts\FlexibleEncryption::class,
        'notes' => \App\Casts\FlexibleEncryption::class,
        'social_media' => 'array',
        'birthday' => 'date',
        'last_visit_at' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getMembershipStatusAttribute()
    {
        if (!$this->membership_type) {
            return 'None';
        }

        if ($this->membership_expires && $this->membership_expires->isFuture()) {
            return 'Active';
        }

        return 'Expired';
    }
}
