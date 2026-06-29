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
        if (!Schema::hasColumn('bookings', 'preferred_date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->date('preferred_date')->nullable()->after('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'preferred_date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('preferred_date');
            });
        }
    }
};
