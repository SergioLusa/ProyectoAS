# Usar la imagen oficial de RabbitMQ con la interfaz de administración
FROM rabbitmq:management

# Puedes añadir configuraciones personalizadas aquí si es necesario
# Por ejemplo, si quieres habilitar plugins o cambiar configuraciones

# Habilitar un plugin específico si lo necesitas
RUN rabbitmq-plugins enable --offline rabbitmq_management

# Configuración personalizada del archivo rabbitmq.conf
# Si tienes un archivo rabbitmq.conf, puedes copiarlo al contenedor
# COPY rabbitmq.conf /etc/rabbitmq/

# Exponer el puerto de RabbitMQ (5672) y el puerto de la interfaz de administración (15672)
EXPOSE 5672 15672

