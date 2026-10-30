<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Columns for: gardener logins, Google login, price estimates,
 * recurring bookings, customer photos, payments and service pages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isGardener')->default(false)->after('isAdmin');
            $table->string('google_id')->nullable()->unique()->after('email');
        });

        // Google sign-ups don't give us a phone number
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });

        Schema::table('teams', function (Blueprint $table) {
            // The user account this gardener logs in with
            $table->unsignedBigInteger('user_id')->nullable()->unique()->after('id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->text('long_description')->nullable()->after('description');
            // Extra charge for every 100 sq ft of lawn (optional)
            $table->decimal('price_per_100_sqft', 10, 2)->nullable()->after('price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->unsignedInteger('lawn_size')->nullable()->after('address'); // sq ft
            $table->decimal('estimated_price', 10, 2)->nullable()->after('lawn_size');
            $table->json('photos')->nullable()->after('message');
            // none | weekly | biweekly | monthly
            $table->string('recurrence', 20)->default('none')->after('time_slot');
            $table->unsignedBigInteger('parent_booking_id')->nullable()->after('recurrence');
            // unpaid | paid
            $table->string('payment_status', 20)->default('unpaid')->after('status');
            $table->string('payment_method', 30)->nullable()->after('payment_status');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('amount_paid');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('tran_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            // pending | paid | failed | cancelled
            $table->string('status', 20)->default('pending');
            $table->string('method', 50)->nullable(); // bKash, VISA, ...
            $table->string('val_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 50);
            $table->string('description');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->string('image_url')->nullable();
            $table->boolean('isPublished')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Give existing services a URL slug
        foreach (DB::table('services')->get() as $service) {
            $base = Str::slug($service->title) ?: 'service';
            $slug = $base;
            $i = 2;
            while (DB::table('services')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }
            DB::table('services')->where('id', $service->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('payments');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'lawn_size', 'estimated_price', 'photos', 'recurrence', 'parent_booking_id',
                'payment_status', 'payment_method', 'amount_paid', 'paid_at',
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'long_description', 'price_per_100_sqft']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['isGardener', 'google_id']);
        });
    }
};
