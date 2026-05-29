#!/bin/bash

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env file created from .env.example"
fi

# Update .env for Docker and Local MySQL
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=host.docker.internal/g' .env
sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=asset_management/g' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=root/g' .env
# Note: User will need to set their DB_PASSWORD manually in .env

echo "Docker configuration applied to .env"
echo "Note: Ensure your local MySQL is configured to accept connections from the Docker gateway (usually 172.17.0.1 or similar)."
echo "You may need to update 'bind-address' in your my.cnf to '0.0.0.0' or specific bridge IP."

# Build and start containers
docker-compose up -d --build

echo "Containers are starting. Access the app at http://localhost:8080"
