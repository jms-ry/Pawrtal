#!/bin/bash
set -e

echo "================================"
echo "🚀 Starting Deployment Process"
echo "================================"

echo ""
echo "📦 Running migrations..."
php artisan migrate --force
echo "✅ Migrations completed!"

echo ""
echo "🌱 Seeding database..."
php artisan db:seed --force
echo "✅ Database seeded!"

echo ""
echo "🔗 Linking storage..."
php artisan storage:link
echo "✅ Storage linked!"

echo ""
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Caching completed!"

echo ""
echo "🎯 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=$PORT