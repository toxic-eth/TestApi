from typing import Any

from app.repositories.message_repository import MessageRepository
from app.schemas.message import MessageRequest, MessageResponse


class MessageService:
    def __init__(self, repository: MessageRepository) -> None:
        self.repository = repository

    def process_message(self, payload: MessageRequest) -> MessageResponse:
        message_log = self.repository.create(
            request_id=payload.id,
            request_text=payload.text,
            request_context=payload.context,
            request_payload=payload.model_dump(),
            response_payload={},
        )

        response_payload: dict[str, Any] = {
            "id": payload.id,
            "status": "success",
            "response_text": f"Request {payload.id} processed successfully",
            "context": payload.context,
            "created_at": message_log.created_at.isoformat(),
        }
        self.repository.update_response_payload(message_log, response_payload)

        return MessageResponse(
            id=payload.id,
            status="success",
            response_text=response_payload["response_text"],
            context=payload.context,
            created_at=message_log.created_at,
        )
