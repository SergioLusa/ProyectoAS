<?php
require_once 'vendor/autoload.php'; // Autoload de Composer

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Configuración de RabbitMQ
$hostname = 'rabbitmq'; // Nombre del contenedor RabbitMQ en Docker
$port = 5672;
$username = 'guest';
$password = 'guest';
$queue_name = 'forum_queue';

try {
    // Conectar con RabbitMQ
    $connection = new AMQPStreamConnection($hostname, $port, $username, $password);
    $channel = $connection->channel();

    // Declarar la cola como duradera (la cola persiste entre reinicios)
    $channel->queue_declare($queue_name, false, true, false, false);

    // Si se envió un mensaje a través del formulario
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        $messageText = $_POST['message'];
        $userName = !empty($_POST['username']) ? $_POST['username'] : "Anónimo"; // Si no hay nombre, es "Anónimo"
        
        // Crear el mensaje con tabulación entre el nombre del usuario y el mensaje
        $msg = "Usuario: " . $userName . "\tMensaje: " . $messageText;

        // Crear y publicar el mensaje con persistencia
        $message = new AMQPMessage($msg, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT, // Mensaje persistente
        ]);
        $channel->basic_publish($message, '', $queue_name);

        // Redirigir al inicio después de enviar el mensaje
        header("Location: index.php");
        exit; // Aseguramos que no se ejecute código adicional después de la redirección
    }

    // Cerrar conexiones
    $channel->close();
    $connection->close();
} catch (\Exception $e) {
    error_log("Error al conectar con RabbitMQ: " . $e->getMessage());
    echo "<p>Error al enviar el mensaje. Inténtalo de nuevo más tarde.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensaje</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #141414;
            color: #fff;
        }

        header {
            background-color: #e50914;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #1f1f1f;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.5);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #e50914;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #b3b3b3;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            background: #2a2a2a;
            color: #fff;
        }

        button {
            background-color: #e50914;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            margin: 10px;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #f40612;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        Enviar Mensaje al Foro
    </header>
    <div class="container">
        <h2>Escribe tu mensaje en el foro</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Escribe tu nombre (Deja vacío para anónimo)" /><br>
            <textarea name="message" placeholder="Escribe tu mensaje aquí..."></textarea><br>
            <button type="submit">Enviar</button>
        </form>
        <button onclick="location.href='index.php'">Volver al Inicio</button>
        <button onclick="location.href='peliculas.php'">Película Aleatoria</button>
	<button onclick="location.href='busqueda.php'">Filtrado de películas</button>
	<button onclick="location.href='forum.php'">Comentarios</button>
    </div>
</body>
</html>

