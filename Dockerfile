FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql pdo_sqlite \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data storage \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/app.conf \
    && a2enconf app

ENV DB_DRIVER=sqlite
EXPOSE 80
