#!/bin/sh
set -e

cd /var/www/html

# Check if .env exists
if [ ! -f .env ]; then
    echo "[backend] .env file not found. Initializing from .env.docker..."
    if [ -f .env.docker ]; then
        cp .env.docker .env
    else
        cp .env.example .env
    fi
fi

# Install Composer dependencies if vendor does not exist
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "[backend] Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Ensure application key is set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "[backend] Generating application key..."
    php artisan key:generate --force
fi

# Wait for MySQL database
echo "[backend] Waiting for MySQL database (${DB_HOST:-db}:${DB_PORT:-3306})..."
while ! nc -z "${DB_HOST:-db}" "${DB_PORT:-3306}"; do
    sleep 1
done

# Wait until PDO connection actually succeeds
echo "[backend] Checking database authentication & readiness..."
until php -r '
try {
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "3306";
    $database = getenv("DB_DATABASE") ?: "portofolio";
    $user = getenv("DB_USERNAME") ?: "portofolio";
    $pass = getenv("DB_PASSWORD") ?: "password";
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2
    ]);
    exit(0);
} catch (Throwable $e) {
    exit(1);
}'; do
    sleep 2
done
echo "[backend] Database connected successfully!"

# Run migrations
echo "[backend] Running database migrations..."
php artisan migrate --force

# Run initial seeders if profiles table is empty
NEED_SEED=$(php -r '
try {
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "3306";
    $database = getenv("DB_DATABASE") ?: "portofolio";
    $user = getenv("DB_USERNAME") ?: "portofolio";
    $pass = getenv("DB_PASSWORD") ?: "password";
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $user, $pass);
    $stmt = $pdo->query("SELECT COUNT(*) FROM profiles");
    echo ($stmt && $stmt->fetchColumn() > 0) ? "0" : "1";
} catch (Throwable $e) {
    echo "1";
}
')

if [ "$NEED_SEED" = "1" ]; then
    echo "[backend] Seeding database with initial portfolio data..."
    php artisan db:seed --class=PortfolioSeeder --force || true
    php artisan db:seed --class=ContohProyekSeeder --force || true
    php artisan db:seed --class=AdminUserSeeder --force || true
fi

# Ensure storage link
if [ ! -L "public/storage" ]; then
    echo "[backend] Creating storage symlink..."
    php artisan storage:link || true
fi

# Ensure storage and bootstrap/cache permissions
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

echo "[backend] Ready! Starting server..."
exec "$@"
