FROM registry.gitlab.com/just-pthai-it/docker-phps/php8.1-alpine
WORKDIR /app
RUN apk add curl
COPY . .
RUN addgroup -g 1000 appgroup
RUN adduser -D -u 1000 appuser -G appgroup
RUN chown -R appuser:appgroup /app
USER appuser
RUN composer i
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]