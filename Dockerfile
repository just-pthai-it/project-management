FROM registry.gitlab.com/just-pthai-it/docker-phps/php8.1-alpine

ARG userid=1000
ARG groupid=1000

WORKDIR /app

RUN apk add curl
COPY . .
RUN addgroup -g $userid appgroup
RUN adduser -D -u $groupid appuser -G appgroup
RUN chown -R appuser:appgroup /app

USER appuser

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]