<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceStaffCommission extends Model
{
    protected $fillable = [
        'invoice_id', 'staff_id', 'staff_name',
        'allocated_amount', 'commission_rate', 'commission_earned',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
