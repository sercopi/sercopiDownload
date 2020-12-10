FROM sercopi/laravel:latest

#docker building
RUN mkdir ./sercopiDownload
WORKDIR /app/sercopiDownload
EXPOSE 3306
EXPOSE 8080
ADD . / /app/sercopiDownload/
RUN composer install
COPY .env.example ./.env
RUN php artisan key:generate
