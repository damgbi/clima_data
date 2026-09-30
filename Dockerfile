# Usa a imagem oficial do PHP 8.3 com Apache
FROM php:8.3-apache

# Instala dependências do sistema e extensões necessárias para o Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Ativa o módulo mod_rewrite do Apache para as rotas do Laravel
RUN a2enmod rewrite

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho no container
WORKDIR /var/www/html

# Copia os arquivos do seu projeto
COPY . .

# Altera a pasta pública do Apache para apontar para /public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Instala as dependências do Composer para produção
RUN composer install --no-dev --optimize-autoloader

# Dá permissão para o Apache gravar nas pastas de cache e armazenamento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta 80
EXPOSE 80