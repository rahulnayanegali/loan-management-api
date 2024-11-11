# Loan Management API

A RESTful backend service for creating, reading, updating, and deleting loans. This API allows users to manage loan data, including loan amounts, interest rates, and durations. Built with PHP, Laravel, and Docker for easy setup and deployment.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. Clone the repository:

``` bash
https://github.com/rahulnayanegali/loan-management-api.git
```

2. Copy the example environment file:
``` bash
cp .env.example .env
```


3. Run the setup script:
``` bash
chmod +x setup.sh
./setup.sh
```
This script will:
- Build and start Docker containers
- Run database migrations
- Execute tests

4. Once the setup is complete, the API will be available at `http://localhost:8080`

5. Access the API documentation at `http://localhost:8080/docs/index.html`  
## API Endpoints

- `GET /api/loans`: Retrieve all loans (paginated)
- `POST /api/loans`: Create a new loan
- `GET /api/loans/{id}`: Retrieve a specific loan
- `PUT /api/loans/{id}`: Update a specific loan
- `DELETE /api/loans/{id}`: Delete a specific loan
- `POST /api/loans/{id}/payments`: Add/Update payment to a specific loan
For detailed information on request/response formats, please refer to the API documentation.

## Testing

To run tests manually:
``` bash
docker-compose exec app php artisan test
```

## Stopping the Application

To stop the Docker containers:
``` bash 
docker-compose down
```

To start the application again:
``` bash 
docker-compose up -d
```


## Troubleshooting

If you encounter any issues:

1. Ensure all containers are running:
``` bash
docker-compose ps
```

2. Check container logs:
``` bash
docker-compose logs
```


3. Verify database connection:
``` bash
docker-compose exec app php /var/www/db_test.php
```

## Additional Information

For more detailed information about the Docker setup, please refer to our [Docker Setup Documentation](docker/DOCKER_SETUP.md).


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
