# FastAPI Microservice Test Task

Микросервис реализован по ТЗ:

- FastAPI
- один endpoint `POST /api/v1/messages`
- трехуровневая архитектура: `routes -> services -> repositories/database`
- сохранение всех запросов и ответов в PostgreSQL
- работа с базой только через ORM (`SQLAlchemy`)
- запуск приложения и базы одной командой через Docker Compose

## Project Structure

```text
app/
  api/routes/messages.py
  core/config.py
  db/base.py
  db/session.py
  models/message_log.py
  repositories/message_repository.py
  schemas/message.py
  services/message_service.py
  main.py
Dockerfile
docker-compose.yml
requirements.txt
.env.example
```

## Run

```bash
docker compose up --build
```

После запуска сервис будет доступен по адресу:

```text
http://localhost:8000/api/v1/messages
```

## Request Example

```json
{
  "id": "request-001",
  "text": "Привет, сервис",
  "context": {
    "source": "test-task",
    "priority": "high"
  }
}
```

## Response Example

```json
{
  "id": "request-001",
  "status": "success",
  "response_text": "Request request-001 processed successfully",
  "context": {
    "source": "test-task",
    "priority": "high"
  },
  "created_at": "2026-04-02T12:00:00.000000Z"
}
```

## cURL Example

```bash
curl -X POST http://localhost:8000/api/v1/messages \
  -H "Content-Type: application/json" \
  -d '{
    "id": "request-001",
    "text": "Привет, сервис",
    "context": {
      "source": "test-task",
      "priority": "high"
    }
  }'
```

## Database

В таблицу `message_logs` сохраняются:

- `request_id`
- `request_text`
- `request_context`
- `request_payload`
- `response_payload`
- `created_at`
