# TestApi

A minimal Laravel API project for the backend technical task.

## Run

```bash
git clone https://github.com/toxic-eth/TestApi
cd TestApi
docker compose up -d
```

The API will be available at `http://localhost:8000`.

## Endpoint

`GET /api/v1/health-check`

Required header:

```text
X-Owner: {uuid}
```

Example request:

```bash
curl -i http://localhost:8000/api/v1/health-check \
  -H "X-Owner: 123e4567-e89b-12d3-a456-426614174000"
```

Success response:

```json
{
  "db": true,
  "cache": true
}
```

If at least one infrastructure service is unavailable, the endpoint returns `500`.

## Technical notes

- Laravel 12 API-only setup
- MySQL 8.4 for request logging
- Redis 7 for cache connectivity checks
- Rate limit: `60 requests / minute`
- Request header validation through middleware
- Every health-check request is attempted to be persisted in `health_check_requests`

## Storage schema

`health_check_requests` contains:

- `owner_uuid`
- `db_ok`
- `cache_ok`
- `response_code`
- timestamps

## Assumptions

- If `X-Owner` is missing or invalid, the API returns `422`.
- If MySQL is down, the request cannot be written to MySQL, so the endpoint still returns the correct health-check response.