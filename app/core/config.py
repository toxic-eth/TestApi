from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    app_name: str = "FastAPI Test Task Service"
    database_url: str = (
        "postgresql+psycopg://fastapi_user:fastapi_password@db:5432/fastapi_service"
    )

    model_config = SettingsConfigDict(
        env_file=".env",
        env_file_encoding="utf-8",
        extra="ignore",
    )


settings = Settings()
