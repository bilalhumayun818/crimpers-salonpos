<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_staff_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('staff_name');
            $table->decimal('allocated_amount', 10, 2)->default(0); // service amount given to this staff
            $table->decimal('commission_rate', 5, 2)->default(0);   // % rate at time of sale
            $table->decimal('commission_earned', 10, 2)->default(0); // actual PKR commission
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_staff_commissions');
    }
};
