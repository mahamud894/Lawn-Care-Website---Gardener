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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'icon_url')) {
                $table->string('icon_url')->nullable()->after('image_url');
            }
            if (!Schema::hasColumn('services', 'description')) {
                $table->text('description')->nullable()->after('icon_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'icon_url')) {
                $table->dropColumn('icon_url');
            }
            if (Schema::hasColumn('services', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
