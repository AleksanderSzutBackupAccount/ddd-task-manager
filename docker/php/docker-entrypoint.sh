#!/bin/sh
set -e

# Wait for MySQL to be ready
until bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do
  echo "Waiting for database..."
  sleep 2
done

echo "Database is ready, running migrations..."

# Run migrations for dev environment
bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Run migrations for test environment
DATABASE_URL=mysql://symfony:symfony@mysql:3306/app_test bin/console doctrine:migrations:migrate --env=test --no-interaction --allow-no-migration

echo "Migrations completed."

exec "$@"
