<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the columns the site searches and filters on most.
 * Without them every slot check and list page reads the whole table,
 * which gets slow once there are thousands of bookings.
 */
return new class extends Migration
{
    private array $indexes = [
        'bookings' => [
            'bookings_slot_lookup' => ['preferred_date', 'time_slot', 'status'],
            'bookings_user_id_index' => ['user_id'],
            'bookings_team_member_index' => ['team_member_id', 'status'],
            'bookings_status_index' => ['status'],
            'bookings_payment_status_index' => ['payment_status'],
            'bookings_parent_index' => ['parent_booking_id'],
            'bookings_created_at_index' => ['created_at'],
        ],
        'services' => ['services_active_index' => ['isActive']],
        'teams' => ['teams_active_index' => ['isActive']],
        'projects' => ['projects_active_index' => ['isActive']],
        'reviews' => ['reviews_approved_index' => ['isApproved']],
        'posts' => ['posts_published_index' => ['isPublished', 'published_at']],
        'faqs' => ['faqs_order_index' => ['isActive', 'sort_order']],
        'contact_messages' => ['contact_messages_read_index' => ['isRead']],
        'payments' => ['payments_booking_index' => ['booking_id']],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $list) {
            foreach ($list as $name => $columns) {
                if (Schema::hasTable($table) && ! Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $list) {
            foreach (array_keys($list) as $name) {
                if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
                }
            }
        }
    }
};
