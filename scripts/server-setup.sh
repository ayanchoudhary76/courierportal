#!/bin/bash
# ============================================================
# CourierPortal — EC2 Ubuntu 22.04 LTS Server Setup Script
# Run ONCE on a fresh EC2 instance as the ubuntu user
# Usage: bash scripts/server-setup.sh
# ============================================================

set -e  # Exit immediately on any error

echo "🚀 Starting CourierPortal server setup..."
echo "================================================"

# ── 1. System update ────────────────────────────────────────
echo ""
echo "📦 [1/10] Updating system packages..."
sudo apt-get update -y && sudo apt-get upgrade -y

# ── 2. PHP 8.3 + extensions ─────────────────────────────────
echo ""
echo "🐘 [2/10] Installing PHP 8.3 and extensions..."
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update -y
sudo apt-get install -y \
    php8.3 \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-curl \
    php8.3-gd \
    php8.3-zip \
    php8.3-tokenizer \
    php8.3-ctype \
    php8.3-intl \
    php8.3-redis \
    unzip

# ── 3. Nginx ─────────────────────────────────────────────────
echo ""
echo "🌐 [3/10] Installing Nginx..."
sudo apt-get install -y nginx
sudo systemctl enable nginx

# ── 4. Composer ──────────────────────────────────────────────
echo ""
echo "🎼 [4/10] Installing Composer..."
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/tmp --filename=composer
sudo mv /tmp/composer /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer --version

# ── 5. Node.js 20 ────────────────────────────────────────────
echo ""
echo "🟢 [5/10] Installing Node.js 20..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
node --version && npm --version

# ── 6. Supervisor ─────────────────────────────────────────────
echo ""
echo "👷 [6/10] Installing Supervisor..."
sudo apt-get install -y supervisor
sudo systemctl enable supervisor

# ── 7. MySQL client (DB is on RDS) ───────────────────────────
echo ""
echo "🗄️  [7/10] Installing MySQL client..."
sudo apt-get install -y mysql-client-8.0

# ── 8. Web directory & clone ─────────────────────────────────
echo ""
echo "📁 [8/10] Creating web directory and cloning repo..."
sudo mkdir -p /var/www/courierportal
sudo chown ubuntu:ubuntu /var/www/courierportal

cd /var/www
git clone https://github.com/ayanchoudhary76/courierportal.git courierportal
cd courierportal

# ── 9. Install dependencies & build ──────────────────────────
echo ""
echo "📦 [9/10] Installing dependencies and building assets..."
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build

# ── 10. Permissions ──────────────────────────────────────────
echo ""
echo "🔒 [10/10] Setting file permissions..."
sudo chown -R ubuntu:www-data /var/www/courierportal
sudo chmod -R 755 /var/www/courierportal
sudo chmod -R 775 /var/www/courierportal/storage
sudo chmod -R 775 /var/www/courierportal/bootstrap/cache

# ── Nginx config ──────────────────────────────────────────────
echo ""
echo "⚙️  Configuring Nginx..."
sudo cp /var/www/courierportal/nginx.conf /etc/nginx/sites-available/courierportal
sudo ln -sf /etc/nginx/sites-available/courierportal /etc/nginx/sites-enabled/courierportal
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx

# ── Supervisor config ─────────────────────────────────────────
echo ""
echo "⚙️  Configuring Supervisor..."
sudo cp /var/www/courierportal/supervisor.conf /etc/supervisor/conf.d/courierportal-worker.conf

# ── Done ─────────────────────────────────────────────────────
echo ""
echo "================================================"
echo "✅ Server setup complete!"
echo ""
echo "📋 NEXT STEPS (do these in order):"
echo ""
echo "  1. Copy your production .env:"
echo "     nano /var/www/courierportal/.env"
echo "     (use .env.production.example as a reference)"
echo ""
echo "  2. Generate storage symlink:"
echo "     cd /var/www/courierportal && php artisan storage:link"
echo ""
echo "  3. Run database migrations:"
echo "     php artisan migrate --force"
echo ""
echo "  4. Seed the admin user:"
echo "     php artisan db:seed --class=AdminSeeder"
echo ""
echo "  5. Cache everything:"
echo "     php artisan config:cache && php artisan route:cache && php artisan view:cache"
echo ""
echo "  6. Start Supervisor (queue worker):"
echo "     sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start all"
echo ""
echo "  7. Install SSL with Certbot:"
echo "     sudo apt-get install -y certbot python3-certbot-nginx"
echo "     sudo certbot --nginx -d yourcourierdomain.com -d www.yourcourierdomain.com"
echo ""
echo "  8. Point your domain DNS A record to this EC2 Elastic IP"
echo "================================================"
