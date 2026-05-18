<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

use App\Traits\BelongsToBranch;

class Appointment extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'staff_id',
        'service_id',
        'service_package_id',
        'customer_id',
        'appointment_date',
        'start_time',
        'end_time',
        'customer_name',
        'customer_phone',
        'notes',
        'status',
    ];

    protected $casts = [
        'customer_name' => \App\Casts\FlexibleEncryption::class,
        'customer_phone' => \App\Casts\FlexibleEncryption::class,
        'notes' => \App\Casts\FlexibleEncryption::class,
        'appointment_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function servicePackage()
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Auto-cancel appointments that are 10 minutes late
     */
    public static function autoCancelLate()
    {
        $now = now();
        $today = $now->format('Y-m-d');
        $tenMinsAgo = $now->copy()->subMinutes(10)->format('H:i:s');

        self::whereIn('status', ['scheduled', 'confirmed'])
            ->where(function($q) use ($today, $tenMinsAgo) {
                $q->where('appointment_date', '<', $today)
                  ->orWhere(function($sq) use ($today, $tenMinsAgo) {
                      $sq->where('appointment_date', $today)
                         ->where('start_time', '<', $tenMinsAgo);
                  });
            })
            ->update([
                'status' => 'cancelled',
                'notes' => DB::raw("CONCAT(COALESCE(notes,''), ' [System: Auto-discarded after 10 min delay]')")
            ]);
    }
}