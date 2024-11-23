# Usa una imagen oficial de MariaDB como base
FROM mariadb:10.11

# Copia un script de inicialización que cree las tablas necesarias
COPY ./init.sql /docker-entrypoint-initdb.d/

# Exponer el puerto por defecto de MariaDB
EXPOSE 3306

