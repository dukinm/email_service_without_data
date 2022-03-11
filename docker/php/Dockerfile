# Для начала указываем исходный образ, он будет использован как основа
FROM php:8.1.3-fpm
# Необязательная строка с указанием автора образа
MAINTAINER dukinm <dukinm@gmail.com>

#ARG DEBIAN_FRONTEND=noninteractive
# RUN выполняет идущую за ней команду в контексте нашего образа.
# В данном случае мы установим некоторые зависимости и модули PHP.
# Для установки модулей используем команду docker-php-ext-install.
# На каждый RUN создается новый слой в образе, поэтому рекомендуется объединять команды.
RUN apt-get update && apt-get install -y \
        cron \
        nano \
        curl \
        wget \
        git \
	libonig-dev \
	libzip-dev \
	libmcrypt-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip



# Куда же без composer'а.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Добавим свой php.ini, можем в нем определять свои значения конфига
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Указываем рабочую директорию для PHP
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www
RUN echo "* * * * * root php /var/www/email.localhost/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Запускаем контейнер
# Из документации: The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable, 
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
CMD ["php-fpm"]
