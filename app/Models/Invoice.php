<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;
use App\Models\Customer;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'invoice_no', 'user_id', 'customer_id', 'customer_name', 'total_amount', 'tax', 
        'discount', 'payable_amount', 'payment_method', 'status',
        'cash_received', 'change_returned', 'staff_id', 'staff_names', 'bank_name', 'pending_amount', 'split_bank'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function staffCommissions()
    {
        return $this->hasMany(InvoiceStaffCommission::class);
    }

    public function totalCost()
    {
        return $this->items->sum(function($item) {
            return ($item->itemizable->cost_price ?? 0) * $item->quantity;
        });
    }
}
