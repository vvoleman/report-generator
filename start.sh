#!/bin/bash

# Quick Start Script for Toggl Report Generator
# This script helps you get the application up and running quickly

set -e

echo "🚀 Toggl Report Generator - Quick Start"
echo "========================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first:"
    echo "   https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first:"
    echo "   https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✅ Docker and Docker Compose are installed"
echo ""

# Check if .env.local exists
if [ ! -f .env.local ]; then
    echo "📝 Creating .env.local file..."
    cp .env .env.local
    echo ""
    echo "⚠️  Please edit .env.local and add your Toggl OAuth credentials:"
    echo "   OAUTH_TOGGL_CLIENT_ID=your_client_id"
    echo "   OAUTH_TOGGL_CLIENT_SECRET=your_client_secret"
    echo ""
    echo "   See OAUTH_SETUP.md for instructions on obtaining these credentials."
    echo ""
    read -p "Press Enter to continue after updating .env.local..."
fi

echo "🐳 Starting Docker containers..."
docker-compose up -d

echo ""
echo "⏳ Waiting for database to be ready..."
sleep 5

echo ""
echo "📦 Installing Composer dependencies..."
docker-compose exec php composer install --no-interaction

echo ""
echo "🗄️  Running database migrations..."
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

echo ""
echo "✅ Application is ready!"
echo ""
echo "📍 Access the application at: http://localhost:8080"
echo ""
echo "Next steps:"
echo "1. Open http://localhost:8080 in your browser"
echo "2. Register a new account"
echo "3. Connect your Toggl account"
echo "4. Generate your first report!"
echo ""
echo "To stop the application: docker-compose down"
echo "To view logs: docker-compose logs -f"
echo ""
