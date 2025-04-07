# Usa a imagem oficial do PHP com extensões necessárias
FROM php:8.2-fpm

# Instala extensões e dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala dependências para MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto para o container
COPY . .

# Instala as dependências PHP
RUN composer install --no-interaction

# Dá permissão para o storage e cache
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta 9000 do PHP-FPM
EXPOSE 9000

# Comando padrão do container
CMD php artisan serve --host=0.0.0.0 --port=8000