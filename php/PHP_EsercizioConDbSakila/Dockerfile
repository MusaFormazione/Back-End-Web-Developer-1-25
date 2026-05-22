# 1 Set master image
FROM php:8.2-apache

# 2 Set working directory
WORKDIR /var/www/html
# 3 Add and enable Extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo_mysql mysqli
# 4 Server settings
RUN echo "ServerName php-server" >> /etc/apache2/apache2.conf
# 5 Install Sendmail
RUN apt-get update && \
    apt-get install -y --no-install-recommends sendmail && \
    rm -rf /var/lib/apt/lists/*

RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

# RUN sudo echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts

RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

# 6 Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# 7 Remove Cache
RUN rm -rf /var/cache/apt/*
# 8 Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html
# 9 Enable mod rewrite
RUN a2enmod rewrite
# 10 Export port 80
EXPOSE 80
EXPOSE 25