<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->date('report_date');
            
            // Other expenditures + Dbt / Crd
            $table->text('other_expenditures')->nullable();
            $table->text('dbt_crd')->nullable();
            
            // Lower Daily Details: Expenditures
            $table->string('food_amount')->nullable();
            $table->string('tea_amount')->nullable();
            $table->string('expenditures_amount')->nullable();
            
            // Lower Daily Details: Cosmetics
            $table->text('cosmetics_notes')->nullable();
            $table->decimal('cosmetics_total', 12, 2)->default(0);
            
            // Lower Daily Details: Staff monetary section
            $table->text('staff_notes')->nullable();
            $table->decimal('staff_total_monetary', 12, 2)->default(0);
            
            // Online Payments
            $table->json('easypaisa_entries')->nullable();
            $table->decimal('easypaisa_total', 12, 2)->default(0);
            $table->json('card_hbl_entries')->nullable();
            $table->decimal('card_hbl_total', 12, 2)->default(0);
            
            // Balance Box
            $table->text('balance_notes')->nullable();
            
            // Bottom Three Sections
            $table->json('ol_paid_entries')->nullable();
            $table->decimal('ol_paid_total', 12, 2)->default(0);
            $table->json('paid_from_c3_entries')->nullable();
            $table->decimal('paid_from_c3_total', 12, 2)->default(0);
            $table->json('personal_exp_entries')->nullable();
            $table->decimal('personal_exp_total', 12, 2)->default(0);
            
            // MZN Bank
            $table->decimal('mzn_bank_amount', 12, 2)->default(0);
            
            // Complex JSON Structures for grid inputs
            $table->json('staff_sales_data')->nullable(); // { fine: {}, time_ta: {}, comsn: {}, rows: {}, discounts: {} }
            $table->json('staff_status_data')->nullable(); // { staff_id: '11:38' or 'Leave' }
            $table->json('summary_data')->nullable(); // { sale, cos, dis, exp, ol, staff, prs, dbt_crd, other, c3, balnc }
            
            $table->timestamps();

            $table->unique(['branch_id', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
