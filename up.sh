#!/bin/sh
if [ "$1" = "prod" ]; then
  docker-compose up -d nginx
else
  docker-compose up -d
fi