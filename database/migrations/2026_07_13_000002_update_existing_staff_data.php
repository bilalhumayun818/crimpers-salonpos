<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Staff;

return new class extends Migration
{
    public function up(): void
    {
        // Update all existing staff records with default values for new fields
        Staff::whereNull('date_of_birth')->update(['date_of_birth' => now()->subYears(30)->toDateString()]);
        Staff::whereNull('emergency_number')->update(['emergency_number' => 'Not provided']);
        
        // Ensure all employees have all services assigned
        $services = \App\Models\Service::pluck('id')->toArray();
        if (!empty($services)) {
            foreach (Staff::all() as $staff) {
                $staff->services()->sync($services, false);
            }
        }
    }

    public function down(): void
    {
        // This migration doesn't modify schema, just data, so down() can be empty
    }
};
