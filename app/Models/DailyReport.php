<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'report_date',
        'other_expenditures',
        'dbt_crd',
        'expenditures_amount',
        'cosmetics_notes',
        'cosmetics_total',
        'staff_notes',
        'staff_total_monetary',
        // Bank
        'easypaisa_entries',
        'easypaisa_total',
        // Card
        'card_hbl_entries',
        'card_hbl_total',
        // Cash
        'cash_entries',
        'cash_total',
        // Split
        'split_entries',
        'split_total',
        // Other
        'ol_paid_entries',
        'ol_paid_total',
        'paid_from_c3_entries',
        'paid_from_c3_total',
        'personal_exp_total',
        'mzn_bank_amount',
        'staff_sales_data',
        'staff_status_data',
        'summary_data',
    ];

    protected $casts = [
        'report_date' => 'date',
        'cosmetics_total' => 'decimal:2',
        'staff_total_monetary' => 'decimal:2',
        // Bank
        'easypaisa_entries' => 'array',
        'easypaisa_total' => 'decimal:2',
        // Card
        'card_hbl_entries' => 'array',
        'card_hbl_total' => 'decimal:2',
        // Cash
        'cash_entries' => 'array',
        'cash_total' => 'decimal:2',
        // Split
        'split_entries' => 'array',
        'split_total' => 'decimal:2',
        // Other
        'ol_paid_entries' => 'array',
        'ol_paid_total' => 'decimal:2',
        'paid_from_c3_entries' => 'array',
        'paid_from_c3_total' => 'decimal:2',
        'personal_exp_total' => 'decimal:2',
        'mzn_bank_amount' => 'decimal:2',
        'staff_sales_data' => 'array',
        'staff_status_data' => 'array',
        'summary_data' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
