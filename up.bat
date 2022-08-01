if "%1" == "prod" (
  docker-compose up -d nginx
) else (
  docker-compose up -d
)