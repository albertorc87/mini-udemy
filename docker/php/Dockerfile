FROM php:8.4-fpm

RUN apt-get update \
&& apt-get install -y git zip unzip libicu-dev libpq-dev libzip-dev librabbitmq-dev \
&& docker-php-ext-install intl opcache pdo pdo_pgsql zip \
&& pecl install amqp \
&& docker-php-ext-enable amqp


# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html

# Copiar archivos del proyecto (excluyendo lo que está en .dockerignore)
COPY . .

# Ejecutar composer install si no existe vendor (durante el build)
RUN if [ ! -d "vendor" ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi

# Script de entrada para ejecutar composer install al iniciar el contenedor
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Opcional: mejorar rendimiento de Symfony en dev
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini