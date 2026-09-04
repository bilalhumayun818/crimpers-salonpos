<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Staff extends Model
{
    use BelongsToBranch;
    use HasFactory;

    protected $attributes = [
        'commission_per_service' => 10,
    ];

    protected $fillable = [
        'name',
        'date_of_birth',
        'email',
        'phone',
        'emergency_number',
        'address',
        'cnic',
        'position',
        'hourly_rate',
        'salary',
        'base_salary',
        'commission_per_customer',
        'commission_per_service',
        'total_earned_commission',
        'hiring_date',
        'status',
        'current_shift',
        'shift_start',
        'shift_end',
        'bio',
        'staff_role_id',
        'rating',
        'rating_total',
        'rating_count',
        'last_paid_at',
    ];

    protected $casts = [
        'name' => \App\Casts\FlexibleEncryption::class,
        'email' => \App\Casts\FlexibleEncryption::class,
        'phone' => \App\Casts\FlexibleEncryption::class,
        'emergency_number' => \App\Casts\FlexibleEncryption::class,
        'address' => \App\Casts\FlexibleEncryption::class,
        'cnic' => \App\Casts\FlexibleEncryption::class,
        'hourly_rate' => 'decimal:2',
        'salary' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'commission_per_customer' => 'decimal:2',
        'commission_per_service' => 'decimal:2',
        'total_earned_commission' => 'decimal:2',
        'date_of_birth' => 'date',
        'hiring_date' => 'date',
        'status' => 'boolean',
        'rating' => 'integer',
        'rating_total' => 'integer',
        'rating_count' => 'integer',
        'last_paid_at' => 'datetime',
    ];

    public function setSalaryAttribute($value)
    {
        $this->attributes['salary'] = $value;
        $this->attributes['base_salary'] = $value;
    }

    public function setBaseSalaryAttribute($value)
    {
        $this->attributes['base_salary'] = $value;
        $this->attributes['salary'] = $value;
    }

    public function getBaseSalaryAttribute($value)
    {
        return $value ?? $this->attributes['salary'] ?? 0;
    }

    public function getSalaryAttribute($value)
    {
        return $value ?? $this->attributes['base_salary'] ?? 0;
    }

    public function getDaysSinceLastPaymentAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->hiring_date ?? $this->created_at;
        if (!$startDate) {
            return 0;
        }
        try {
            $start = \Carbon\Carbon::parse($startDate);
            if ($start->isFuture()) {
                return 0;
            }
            return (int) $start->diffInDays(now());
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getIsOnShiftAttribute()
    {
        if (!$this->shift_start || !$this->shift_end)
            return false;

        $now = now();
        $currentTime = $now->format('H:i:s');

        if ($this->shift_start <= $this->shift_end) {
            return ($currentTime >= $this->shift_start && $currentTime <= $this->shift_end);
        } else {
            // Overnight shift (e.g. 22:00 to 02:00)
            return ($currentTime >= $this->shift_start || $currentTime <= $this->shift_end);
        }
    }

    public function getShiftDurationHoursAttribute()
    {
        if (!$this->shift_start || !$this->shift_end) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->shift_start);
        $end = \Carbon\Carbon::parse($this->shift_end);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end) / 60;
    }

    public function getIsPresentTodayAttribute()
    {
        $today = now()->toDateString();
        return $this->attendances()->where('attendance_date', $today)->where('status', 'present')->exists();
    }

    public function scopeAvailable($query)
    {
        $today = now()->toDateString();
        return $query->whereHas('attendances', function ($q) use ($today) {
            $q->where('attendance_date', $today)->where('status', 'present');
        });
    }

    public function getAverageRatingAttribute()
    {
        if ($this->rating_count == 0)
            return 0;
        return round($this->rating_total / $this->rating_count, 1);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getDailyBaseSalaryAttribute()
    {
        $base = (float) ($this->base_salary ?? 0);
        return round($base / 30, 2);
    }

    public function getCurrentCycleDailySalariesCountAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->created_at;
        return (int) $this->expenses()
            ->where('category', 'daily_salary')
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    public function getCurrentCycleDailyBaseSalariesAttribute()
    {
        $count = $this->current_cycle_daily_salaries_count;
        return round($count * $this->daily_base_salary, 2);
    }

    public function getCurrentCycleDailySalariesAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->created_at;
        return (float) $this->expenses()
            ->where('category', 'daily_salary')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
    }

    public function getCurrentCycleAbsentDaysAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->created_at;
        $startDateStr = \Carbon\Carbon::parse($startDate)->toDateString();
        return (int) $this->attendances()
            ->where('attendance_date', '>=', $startDateStr)
            ->where('status', 'absent')
            ->count();
    }

    public function getCurrentCycleAbsentDeductionsAttribute()
    {
        $absentDays = $this->current_cycle_absent_days;
        return round($absentDays * $this->daily_base_salary, 2);
    }

    public function getTodayEarnedCommissionAttribute()
    {
        $today = \Carbon\Carbon::today()->toDateString();
        return (float) \App\Models\InvoiceStaffCommission::where('staff_id', $this->id)
            ->whereDate('created_at', $today)
            ->sum('commission_earned');
    }

    public function getExpectedDailySalaryAttribute()
    {
        return round($this->daily_base_salary + $this->today_earned_commission, 2);
    }

    public function getCurrentCycleAdvancesAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->created_at;
        return (float) $this->expenses()
            ->whereIn('category', ['advance', 'salary_advance'])
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
    }

    public function getCurrentCycleDeductionsAttribute()
    {
        $startDate = $this->last_paid_at ?? $this->created_at;
        return (float) $this->expenses()
            ->where('category', 'deduction')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
    }

    public function getNetSalaryPayableAttribute()
    {
        $base          = (float) ($this->base_salary ?? 0);
        $comm          = (float) ($this->total_earned_commission ?? 0);
        $adv           = $this->current_cycle_advances;
        $ded           = $this->current_cycle_deductions;
        $dailyBasePaid = $this->current_cycle_daily_base_salaries;
        $absentDed     = $this->current_cycle_absent_deductions;
        
        return max(0, ($base + $comm) - ($adv + $ded + $dailyBasePaid + $absentDed));
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function shifts()
    {
        return $this->hasMany(StaffShift::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function upsellPerformance()
    {
        return $this->hasOne(UpsellPerformance::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_service');
    }

    public function role()
    {
        return $this->belongsTo(StaffRole::class, 'staff_role_id');
    }
}
