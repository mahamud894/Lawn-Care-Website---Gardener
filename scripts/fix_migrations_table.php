<?php
// Script to attempt to drop a corrupted `migrations` table so artisan can recreate it.
// Usage: php scripts/fix_migrations_table.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Dropping migrations table if it exists...\n";
try {
    DB::statement('DROP TABLE IF EXISTS `migrations`');
    echo "Dropped `migrations` table (if present).\n";
    echo "Now run: php artisan migrate\n";
} catch (\Exception $e) {
    echo "Error while dropping migrations table: " . $e->getMessage() . "\n";
    echo "If this fails, please drop the `migrations` table manually via your DB client.\n";
}
