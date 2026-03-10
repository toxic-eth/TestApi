# TestApi

Laravel API project for the backend technical task.

## Stack

- Laravel 12
- PHP 8.3
- MySQL 8.4
- Redis 7
- Docker Compose

## What is implemented

- `GET /api/v1/health-check`
- MySQL availability check
- Redis availability check
- `X-Owner` required header validation
- Rate limit: `60 requests / minute`
- Request logging to MySQL table `health_check_requests`
- Automated feature tests for core behavior

## Run

```bash
git clone https://github.com/toxic-eth/TestApi
cd TestApi
docker compose up -d
```

The application image is built with PHP dependencies during `docker compose up -d`, so the runtime container does not need to run `composer install` on each start.

API base URL:

```text
http://localhost:8000
```

## Endpoint

```text
GET /api/v1/health-check
```

Required header:

```text
X-Owner: {uuid}
```

Success response:

```json
{
  "db": true,
  "cache": true
}
```

Response rules:

- `200 OK` if MySQL and Redis are available
- `500 Internal Server Error` if at least one infrastructure service is unavailable
- `422 Unprocessable Content` if `X-Owner` is missing or invalid

## Verification In PowerShell

Start the project:

```powershell
cd W:\Code\TestApi
docker compose up -d
docker compose ps
```

Successful request:

```powershell
Invoke-WebRequest `
  -Uri "http://localhost:8000/api/v1/health-check" `
  -Headers @{ "X-Owner" = "123e4567-e89b-12d3-a456-426614174000" } `
  -UseBasicParsing
```

Expected result:

- `StatusCode : 200`
- `Content : {"db":true,"cache":true}`

Request without header:

```powershell
try {
  Invoke-WebRequest `
    -Uri "http://localhost:8000/api/v1/health-check" `
    -UseBasicParsing
} catch {
  $_.Exception.Response.StatusCode.value__
}
```

Expected result:

- `422`

Check that requests are saved in MySQL:

```powershell
docker compose exec -T mysql mysql -utest_api -ptest_api test_api -e "SELECT id, owner_uuid, db_ok, cache_ok, response_code, created_at FROM health_check_requests ORDER BY id DESC LIMIT 10;"
```

## Automated Tests

Run the feature tests inside the application container:

```powershell
docker compose exec -T app php artisan test
```

## Storage

Table: `health_check_requests`

Fields:

- `owner_uuid`
- `db_ok`
- `cache_ok`
- `response_code`
- `created_at`
- `updated_at`

## Implementation Notes

- MySQL is checked through Laravel database connection and a simple query
- Redis is checked through a write/read operation
- Requests are validated in middleware before controller execution
- Rate limiting is configured separately for the health-check endpoint
- If MySQL is unavailable, request logging cannot be completed, but the endpoint still returns the correct health-check status

## Design Decisions

- The health-check logic is isolated in a dedicated service, keeping the controller thin
- Request persistence is isolated in its own logger service to keep responsibilities separated
- `X-Owner` validation is implemented as route middleware because it is an HTTP contract concern
- The endpoint returns only the JSON shape required by the task, while internal code uses a typed result object for clarity and testability
- Docker is configured for reviewer convenience: clone the repository and run `docker compose up -d`
