<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns the code already uses but no migration ever created.
 * Every step is guarded, so it is safe on databases where the
 * columns were added by hand.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'isBlocked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('isBlocked')->default(false)->after('isAdminApproved');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'address')) {
                $table->string('address')->nullable()->after('email');
            }
            if (! Schema::hasColumn('bookings', 'team_member_id')) {
                $table->unsignedBigInteger('team_member_id')->nullable()->after('user_id');
            }
        });

        // Guests can book without logging in and the service is optional,
        // so these two must allow NULL.
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'team_member_id')) {
                $table->dropColumn('team_member_id');
            }
        });

        if (Schema::hasColumn('users', 'isBlocked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('isBlocked');
            });
        }
    }
};
