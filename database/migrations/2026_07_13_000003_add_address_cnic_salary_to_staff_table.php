<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->text('address')->nullable()->after('emergency_number');
            $table->string('cnic')->nullable()->after('address');
            $table->decimal('salary', 10, 2)->default(0)->after('cnic');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['address', 'cnic', 'salary']);
        });
    }
};
