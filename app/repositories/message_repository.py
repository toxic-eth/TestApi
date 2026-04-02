from typing import Any

from sqlalchemy.orm import Session

from app.models.message_log import MessageLog


class MessageRepository:
    def __init__(self, db: Session) -> None:
        self.db = db

    def create(
        self,
        request_id: str,
        request_text: str,
        request_context: dict[str, Any],
        request_payload: dict[str, Any],
        response_payload: dict[str, Any],
    ) -> MessageLog:
        message_log = MessageLog(
            request_id=request_id,
            request_text=request_text,
            request_context=request_context,
            request_payload=request_payload,
            response_payload=response_payload,
        )
        self.db.add(message_log)
        self.db.commit()
        self.db.refresh(message_log)
        return message_log

    def update_response_payload(
        self,
        message_log: MessageLog,
        response_payload: dict[str, Any],
    ) -> MessageLog:
        message_log.response_payload = response_payload
        self.db.add(message_log)
        self.db.commit()
        self.db.refresh(message_log)
        return message_log
