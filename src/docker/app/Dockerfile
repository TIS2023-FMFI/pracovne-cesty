# Local development runtime used by Laravel Sail (service "laravel.test").
# Replaces Sail's stock Ubuntu runtime, whose PHP packages come from the
# ondrej PPA on Launchpad (unreachable from some networks). Built on the
# official Debian-based PHP image instead; only Debian + Docker Hub needed.
FROM php:8.3-cli-bookworm

ARG WWWGROUP=1000
ARG WWWUSER=1000

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1

# System tools, Node (for Vite), pdflatex (laratex PDF export) and pdftk.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip zip curl ca-certificates gosu default-mysql-client \
        libzip-dev libicu-dev libpng-dev libonig-dev \
        nodejs npm \
        texlive-latex-base texlive-latex-recommended texlive-latex-extra \
        texlive-fonts-recommended lmodern \
        pdftk-java \
    && docker-php-ext-install pdo_mysql zip intl gd bcmath mbstring exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Sail executes commands inside the container as user "sail".
RUN groupadd --force -g ${WWWGROUP} sail \
    && useradd -ms /bin/bash --no-user-group -g ${WWWGROUP} -u ${WWWUSER} sail

WORKDIR /var/www/html
EXPOSE 80

USER sail
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
