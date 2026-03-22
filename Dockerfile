FROM php:8.2-apache

# Instalar extensões necessárias para o Dompdf (GD, mbstring, etc)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Instalar dependências do PHP
RUN composer install --no-interaction --optimize-autoloader

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html
