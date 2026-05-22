FROM alpine:latest

RUN apk add --no-cache netcat-openbsd

COPY whisper_daemon.sh /whisper_daemon.sh
RUN chmod +x /whisper_daemon.sh

EXPOSE 6500
CMD ["/whisper_daemon.sh"]
