#!/bin/bash

# Vercel Build Script for Laravel + Vue.js
echo "🚀 Building Fleet Fox for Vercel..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm install

# Build frontend assets
npm run build

# Generate application key
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Build complete!"

