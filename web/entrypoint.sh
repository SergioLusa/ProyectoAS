#!/bin/bash
# Ejecuta el script Python para cargar los datos
python3 /scripts/cargar_datos.py

composer install
# Luego, inicia Apache en primer plano
apache2-foreground

