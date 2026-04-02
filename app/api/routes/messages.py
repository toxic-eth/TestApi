from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from app.db.session import get_db
from app.repositories.message_repository import MessageRepository
from app.schemas.message import MessageRequest, MessageResponse
from app.services.message_service import MessageService


router = APIRouter(prefix="/api/v1/messages", tags=["messages"])


@router.post("", response_model=MessageResponse)
def create_message(
    payload: MessageRequest,
    db: Session = Depends(get_db),
) -> MessageResponse:
    repository = MessageRepository(db)
    service = MessageService(repository)
    return service.process_message(payload)
