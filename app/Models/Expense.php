<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\FlexibleEncryption;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'expense_type',
        'category',
        'description',
        'amount',
        'deducted_from_drawer',
        'user_id',
        'staff_id'
    ];

    protected $casts = [
        'description' => FlexibleEncryption::class,
        'deducted_from_drawer' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
