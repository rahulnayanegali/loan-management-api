#!/bin/bash
#chmod +x setup.sh 

# Build and start Docker containers
docker-compose up -d --build

# Wait for the database to be ready
echo "Waiting for database to be ready..."
sleep 10

# Install Composer dependencies
docker-compose exec app composer install

# Run migrations
docker-compose exec app php artisan migrate

# Run tests
docker-compose exec app php artisan test

echo "Setup complete! The application is ready to use."