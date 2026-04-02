from datetime import datetime
from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class MessageRequest(BaseModel):
    id: str = Field(..., min_length=1, max_length=255)
    text: str = Field(..., min_length=1)
    context: dict[str, Any] = Field(default_factory=dict)


class MessageResponse(BaseModel):
    id: str
    status: str
    response_text: str
    context: dict[str, Any]
    created_at: datetime

    model_config = ConfigDict(from_attributes=True)
