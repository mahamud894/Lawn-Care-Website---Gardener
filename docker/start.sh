#!/usr/bin/env bash
# Runs every time the container starts on Render.
set -e
cd /var/www/html

# --- Apache listens on the port Render gives us ---
PORT="${PORT:-10000}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
export PORT

# --- APP_KEY: Render's "generate value" makes a plain base64 string; Laravel wants "base64:" in front ---
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set — generating a temporary one (logins reset on every restart)."
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
elif [[ "$APP_KEY" != base64:* ]]; then
    export APP_KEY="base64:${APP_KEY}"
fi

# --- Where data lives ---
# With a Render persistent disk mounted at /var/data, the database and uploaded
# photos survive restarts. Without one (free plan) they live inside the container
# and are reset whenever the service restarts or sleeps.
if [ -d /var/data ] && [ -w /var/data ]; then
    DATA_DIR=/var/data
    echo "Using persistent disk at /var/data"
else
    DATA_DIR=/var/www/html/storage/app/data
    echo "No persistent disk — data will reset when the service restarts (fine for a demo)."
fi
mkdir -p "$DATA_DIR"

# SQLite database (default). For MySQL/Postgres set DB_CONNECTION and DB_URL instead.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    export DB_CONNECTION=sqlite
    export DB_DATABASE="${DB_DATABASE:-$DATA_DIR/database.sqlite}"
    touch "$DB_DATABASE"
fi

# Uploaded photos: keep them on the data folder, starting with the images in the repo
if [ "$DATA_DIR" = /var/data ]; then
    mkdir -p /var/data/uploads
    if [ -d public/uploads ] && [ ! -L public/uploads ]; then
        cp -rn public/uploads/. /var/data/uploads/ 2>/dev/null || true
        rm -rf public/uploads
    fi
    ln -sfn /var/data/uploads public/uploads
fi

chown -R www-data:www-data "$DATA_DIR" storage bootstrap/cache
[ -e public/uploads ] && chown -R www-data:www-data public/uploads/ || true

# --- Database tables, admin account, caches ---
php artisan migrate --force

# Create the main admin only once (when there are no users yet)
USERS=$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n1 || echo 0)
if [ "${USERS:-0}" = "0" ]; then
    php artisan db:seed --force
    if [ "${DEMO_DATA:-false}" = "true" ]; then
        php artisan db:seed --class=Database\\Seeders\\DemoSeeder --force
    fi
fi

php artisan optimize

# Commands above ran as root; give the web server back ownership of everything it writes
chown -R www-data:www-data "$DATA_DIR" storage bootstrap/cache

exec apache2-foreground
