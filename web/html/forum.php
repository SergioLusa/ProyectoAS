<?php
require_once 'vendor/autoload.php'; // Autoload de Composer

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Configuración de RabbitMQ
$hostname = 'rabbitmq'; // Nombre del contenedor RabbitMQ en Docker
$port = 5672;
$username = 'guest';
$password = 'guest';
$queue_name = 'forum_queue';

// Configuración de la base de datos
$dbHost = 'db'; // Nombre del contenedor de la base de datos en Docker
$dbUser = 'root'; // Usuario de la base de datos
$dbPass = 'rootpassword'; // Contraseña de la base de datos
$dbName = 'peliculas'; // Nombre de la base de datos

$messages = []; // Almacenará los mensajes obtenidos de RabbitMQ
$dbMessages = []; // Almacenará los mensajes obtenidos de la base de datos

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener mensajes existentes de la base de datos
    $stmt = $pdo->query("SELECT message FROM comentario");
    $dbMessages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Asegúrate de que usamos PDO::FETCH_ASSOC para obtener un array asociativo

    // Conectar con RabbitMQ
    $connection = new AMQPStreamConnection($hostname, $port, $username, $password);
    $channel = $connection->channel();

    // Declarar la cola (solo lectura, no la crea si no existe)
    list($queue, $messageCount, $consumerCount) = $channel->queue_declare($queue_name, true);

    // Consumir los mensajes de la cola
    $callback = function ($msg) use (&$messages, $pdo) {
        $messages[] = $msg->body; // Añadir mensaje al array

        // Insertar el mensaje en la base de datos
        $stmt = $pdo->prepare("INSERT INTO comentario (message) VALUES (:message)");
        $stmt->bindParam(':message', $msg->body, PDO::PARAM_STR);
        $stmt->execute();
    };

    if ($messageCount > 0) {
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            try {
                $channel->wait(null, false, 10); // Aumenta a 10 segundos o más
            } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
                // Si se excede el tiempo de espera, salir del bucle
                break;
            }
        }
    } else {
        echo "<p>No hay mensajes en la cola.</p>";
    }

    // Cerrar conexiones
    $channel->close();
    $connection->close();
} catch (\PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    echo "<p>Error al conectar con la base de datos. Inténtalo de nuevo más tarde.</p>";
} catch (\Exception $e) {
    error_log("Error al conectar con RabbitMQ: " . $e->getMessage());
    echo "<p>Error al cargar los mensajes. Inténtalo de nuevo más tarde.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro Netflix</title>
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

        .message-box {
            margin-top: 20px;
        }

        .message-box p {
            font-size: 18px;
            margin-bottom: 15px;
            background-color: #333;
            padding: 10px;
            border-radius: 5px;
            color: #e50914;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
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
        Foro Netflix
    </header>
    <div class="container">
        <h2>Mensajes del Foro</h2>
        <div class="message-box">
            <?php
                // Mostrar los mensajes de la base de datos
                if (!empty($dbMessages)) {
                    echo "<h3>Mensajes desde la Base de Datos:</h3>";
                    foreach ($dbMessages as $dbMessage) {
                        // Mostrar los mensajes con estilo consistente
                        echo "<p>" . htmlspecialchars($dbMessage['message']) . "</p>";
                    }
                }

                // Mostrar los mensajes de RabbitMQ
                if (!empty($messages)) {
                    echo "<h3>Mensajes desde RabbitMQ:</h3>";
                    foreach ($messages as $message) {
                        echo "<p>" . htmlspecialchars($message) . "</p>";
                    }
                }

                // Si no hay mensajes en ambos casos
                if (empty($dbMessages) && empty($messages)) {
                    echo "<p>No hay mensajes en el foro.</p>";
                }
            ?>
        </div>

        <div>
            <button onclick="location.href='index.php'">Inicio</button>
            <button onclick="location.href='peliculas.php'">Películas</button>
            <button onclick="location.href='busqueda.php'">Busqueda</button>
            <button onclick="location.href='enviar_mensaje.php'">Escribir un comentario</button>
        </div>
    </div>
    <footer>
        <p>© 2024 Foro Netflix. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

