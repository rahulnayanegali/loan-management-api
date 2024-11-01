# Docker Setup Documentation

## Environment Configuration

We use Docker to create a consistent development environment for our Laravel-based loan management application. Our setup includes three main services: PHP (app), Nginx (webserver), and MariaDB (db).

## Services

### PHP (app)

- **Base Image**: PHP 8.2-FPM
- **Purpose**: Runs the Laravel application
- **Key Configurations**:
  - Installs necessary PHP extensions for Laravel
  - Includes Composer for dependency management
  - Mounts the local project directory to `/var/www` in the container

### Nginx (webserver)

- **Image**: nginx:alpine
- **Purpose**: Serves as the web server, handling HTTP requests
- **Key Configurations**:
  - Configured to work with PHP-FPM
  - Exposes port 8080 on the host, mapped to port 80 in the container

### MariaDB (db)

- **Image**: mariadb:10.6
- **Purpose**: Provides the database for the application
- **Key Configurations**:
  - Uses environment variables for database name, user, and password
  - Data persisted using a named volume

## Environment Variables

We use a `.env` file to manage environment-specific configurations. Key variables include:

- `DB_CONNECTION`: Set to 'mysql' (Laravel uses the same driver for MySQL and MariaDB)
- `DB_HOST`: Set to 'db' (the service name in docker-compose)
- `DB_PORT`: Default MariaDB port (3306)
- `DB_DATABASE`: Name of the database
- `DB_USERNAME`: Database user
- `DB_PASSWORD`: Database password

## Network

All services are connected through a bridge network named `loan_network`, allowing inter-container communication.

## Volumes

- A named volume `mariadb_data` is used to persist the database data
- The local project directory is mounted to `/var/www` in the PHP and Nginx containers

## Why This Configuration?

1. **PHP-FPM**: Chosen for its performance and ability to handle concurrent requests efficiently.
2. **Nginx**: Lightweight and high-performance web server, ideal for serving Laravel applications.
3. **MariaDB**: Fork of MySQL, chosen for its compatibility and performance. We use version 10.6 for stability.
4. **Environment Variables**: Allows for easy configuration changes without modifying Docker files.
5. **Named Volumes**: Ensures data persistence across container restarts and rebuilds.

## Testing the Setup

A `db_test.php` script is provided in the project root to verify database connectivity. Run it using:

```bash
docker-compose exec app php /var/www/db_test.php
```
- This script attempts to connect to the database and list all tables, confirming that the environment is set up correctly.

## Potential Issues and Solutions
- If environment variables are not being passed correctly, ensure the .env file is in the same directory as docker-compose.yml.
- For connection issues, check that the service names in docker-compose.yml match the hostnames used in the application configuration.


## Setting Up and Running the Docker Environment

Follow these steps to set up and run the Docker environment for this project:

1. **Prerequisites**
   - Ensure Docker and Docker Compose are installed on your system.
   - Clone this repository to your local machine.

2. **Environment Configuration**
   - Copy the `.env.example` file to `.env`:
     ```
     cp .env.example .env
     ```
   - Open the `.env` file and set the database credentials and other environment-specific variables.

3. **Building and Starting the Containers**
   - Open a terminal in the project root directory.
   -  Run the following command to build containers:
     ```
     docker-compose build
     ```
     Run the following command to Laravel:
     ```
     docker-compose run --rm app composer create-project laravel/laravel .
     ```
   - Run the following command to build and start the containers:
     ```
     docker-compose up -d --build
     ```
   - This command builds the images (if not built already) and starts the containers in detached mode.

4. **Accessing the Application**
   - The application should now be accessible at `http://localhost:8080`

5. **Stopping the Containers**
   - To stop the containers, run:
     ```
     docker-compose down
     ```

6. **Viewing Logs**
   - To view logs from all containers:
     ```
     docker-compose logs
     ```
   - To view logs from a specific service (e.g., app):
     ```
     docker-compose logs app
     ```

7. **Running Tests**
   - To run tests within the Docker environment:
     ```
     docker-compose exec app php artisan test
     ```

Remember to run the database connectivity test as mentioned in the "Testing the Setup" section to ensure everything is configured correctly.