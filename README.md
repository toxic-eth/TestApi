# FastAPI Microservice Test Task

Микросервис реализован строго по ТЗ:

- FastAPI
- один endpoint `POST /api/v1/messages`
- трехуровневая архитектура: `routes -> services -> repositories/database`
- сохранение всех запросов и ответов в PostgreSQL
- работа с базой только через ORM (SQLAlchemy)
- запуск приложения и базы одной командой через Docker Compose

## Структура проекта

```text
test-task-fastapi/
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

## Формат запроса

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

## Формат ответа

```json
{
  "id": "request-001",
  "status": "success",
  "response_text": "Request request-001 processed successfully",
  "context": {
    "source": "test-task",
    "priority": "high"
  },
  "created_at": "2026-04-02T12:00:00.000000"
}
```

## Как запустить

1. Перейти в каталог сервиса:

   ```bash
   cd test-task-fastapi
   ```

2. Поднять приложение и PostgreSQL одной командой:

   ```bash
   docker compose up --build
   ```

3. После запуска сервис будет доступен по адресу:

   ```text
   http://localhost:8000/api/v1/messages
   ```

## Пример запроса

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

## Что сохраняется в базе

В таблицу `message_logs` записываются:

- входящий `id`
- исходный `text`
- входящий `context`
- полный JSON запроса
- полный JSON ответа
- время создания записи

## Переменные окружения

Файл `.env.example` приложен как пример, но для стандартного запуска он не обязателен: `docker-compose.yml` уже содержит безопасные значения по умолчанию.

## Ссылка на репозиторий

Сейчас проект подготовлен как отдельный локальный git-репозиторий по пути:

```text
W:\fastapi-test-task
```

Чтобы опубликовать его на GitHub, достаточно выполнить:

```bash
cd W:\fastapi-test-task
git remote add origin <YOUR_GITHUB_REPO_URL>
git push -u origin main
```
