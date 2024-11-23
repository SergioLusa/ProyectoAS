# Usar una imagen base de PHP 7.4 con Apache
FROM php:7.4-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    python3 python3-pip libmariadb-dev-compat \
    git curl \
    libbz2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli bcmath sockets \
    && apt-get clean

# Instalar dependencias de Python necesarias (pandas, mysql-connector-python, kaggle)
RUN pip3 install --no-cache-dir pandas mysql-connector-python kaggle


# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar los archivos del proyecto al contenedor
COPY ./scripts /scripts/
COPY ./html /var/www/html/
COPY ./config/kaggle.json /root/.config/kaggle/kaggle.json

# Exponer el puerto 80
EXPOSE 80

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el script entrypoint.sh al contenedor y darle permisos de ejecución
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Establecer el script entrypoint.sh como el punto de entrada del contenedor
ENTRYPOINT ["/entrypoint.sh"]

