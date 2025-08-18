FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Create a non-root user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Change ownership of working directory
RUN chown -R www:www /app

# Switch to non-root user
USER www

# Expose port (if needed for examples)
EXPOSE 8000

# Default command
CMD ["php", "-v"]