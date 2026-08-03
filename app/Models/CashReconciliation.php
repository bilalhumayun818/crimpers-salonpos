<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class CashReconciliation extends Model
{
    /** @use HasFactory<\Database\Factories\CashReconciliationFactory> */
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'user_id', 'date', 'opening_balance', 'expected_cash', 
        'actual_cash', 'difference', 'notes', 'status', 'branch_id', 'is_closed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getCurrentBusinessDate()
    {
        $today = \Carbon\Carbon::today();
        $yesterday = \Carbon\Carbon::yesterday();

        $yesterdayRecon = self::where('date', $yesterday)->first();

        if (!$yesterdayRecon || !$yesterdayRecon->is_closed) {
            return $yesterday;
        }

        return $today;
    }
}
