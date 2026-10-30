#!/bin/sh
#
# Runs every time the container starts.
#
# Everything that must survive a redeploy — uploaded photos, the SQLite
# database, sessions and logs — is kept in $DATA_DIR (a mounted disk) and
# linked back into the app. Without a disk the app still runs, but that
# data is lost whenever the container restarts.
set -e

APP_DIR="${APP_DIR:-/var/www/html}"
DATA_DIR="${DATA_DIR:-/data}"
cd "$APP_DIR"

echo "entrypoint: starting Gardener (data dir: $DATA_DIR)"
mkdir -p "$DATA_DIR"

# ---------------------------------------------------------------------------
# Uploaded images  →  $DATA_DIR/uploads
# ---------------------------------------------------------------------------
if [ ! -d "$DATA_DIR/uploads" ]; then
    mkdir -p "$DATA_DIR/uploads"
    # First boot: keep the images that ship with the repository
    if [ -d "$APP_DIR/public/uploads" ]; then
        cp -R "$APP_DIR/public/uploads/." "$DATA_DIR/uploads/" 2>/dev/null || true
    fi
fi
for folder in services services/icons team projects posts bookings; do
    mkdir -p "$DATA_DIR/uploads/$folder"
done
if [ ! -L "$APP_DIR/public/uploads" ]; then
    rm -rf "$APP_DIR/public/uploads"
    ln -s "$DATA_DIR/uploads" "$APP_DIR/public/uploads"
fi

# ---------------------------------------------------------------------------
# Sessions, logs and caches  →  $DATA_DIR/storage
# (keeps people logged in across deploys)
# ---------------------------------------------------------------------------
if [ ! -d "$DATA_DIR/storage" ] && [ -d "$APP_DIR/storage" ]; then
    cp -R "$APP_DIR/storage" "$DATA_DIR/storage"
fi
for folder in app/public framework/cache/data framework/sessions framework/views logs; do
    mkdir -p "$DATA_DIR/storage/$folder"
done
if [ ! -L "$APP_DIR/storage" ]; then
    rm -rf "$APP_DIR/storage"
    ln -s "$DATA_DIR/storage" "$APP_DIR/storage"
fi

# ---------------------------------------------------------------------------
# APP_KEY — set it in the host's environment variables. If it is missing,
# one is generated once and kept on the disk, so logins and encrypted
# values stay valid between deploys.
# ---------------------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    if [ -f "$DATA_DIR/app_key" ]; then
        APP_KEY="$(cat "$DATA_DIR/app_key")"
    else
        APP_KEY="$(php artisan key:generate --show)"
        echo "$APP_KEY" > "$DATA_DIR/app_key"
        echo "entrypoint: no APP_KEY was set — generated one and saved it to $DATA_DIR/app_key"
    fi
    export APP_KEY
fi

# ---------------------------------------------------------------------------
# Database (SQLite on the disk by default; set DB_CONNECTION=mysql or pgsql
# in the host's environment variables to use a managed database instead)
# ---------------------------------------------------------------------------
DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_CONNECTION
if [ "$DB_CONNECTION" = "sqlite" ]; then
    DB_DATABASE="${DB_DATABASE:-$DATA_DIR/database.sqlite}"
    export DB_DATABASE
    [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE"
fi

# The web server runs as www-data and must be able to write all of this
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data "$DATA_DIR" 2>/dev/null || true
fi

# ---------------------------------------------------------------------------
# Database tables, first-run admin account, and caches
# ---------------------------------------------------------------------------
php artisan migrate --force

if [ ! -f "$DATA_DIR/.seeded" ]; then
    echo "entrypoint: first run — creating the main admin account"
    php artisan db:seed --force

    # Optional sample services, team, projects, FAQ and a blog post,
    # so a brand-new site is not empty. Skipped once real content exists.
    if [ "${SEED_DEMO_CONTENT:-false}" = "true" ]; then
        echo "entrypoint: adding demo content"
        php artisan db:seed --class="Database\\Seeders\\DemoSeeder" --force
    fi

    touch "$DATA_DIR/.seeded"
fi

php artisan optimize

# ---------------------------------------------------------------------------
# Listen on the port the host asks for
# ---------------------------------------------------------------------------
PORT="${PORT:-8080}"
if [ -f /etc/apache2/ports.conf ]; then
    sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
fi
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
    sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

echo "entrypoint: ready on port ${PORT}"
exec "$@"
