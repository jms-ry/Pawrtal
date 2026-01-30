#!/bin/bash
set -e

echo "================================"
echo "CUSTOM DEPLOY SCRIPT RUNNING"
echo "================================"

echo ""
echo "Running migrations..."
php artisan migrate --force

#TODO: Remove the seeding after the first deployment -- comment out "php artisan db:seed --force" line below. 

# echo ""
# echo "SEEDING DATABASE NOW..."
# php artisan db:seed --force
# echo "SEEDING COMPLETED!"

echo ""
echo "Clearing old caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "Rebuilding assets..."
npm run build

echo ""
echo "Caching fresh config..."
php artisan config:cache
php artisan route:cache  
php artisan view:cache

echo ""
echo "Starting server..."
exec php artisan serve --host=0.0.0.0 --port=$PORT