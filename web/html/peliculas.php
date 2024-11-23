<?php
// Configuración de la base de datos
$servername = "db";
$username = "root";  // Cambia este valor si es necesario
$password = "rootpassword";  // Cambia este valor si es necesario
$dbname = "peliculas";  // Nombre de tu base de datos

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    // Redirigir al inicio si no hay conexión
    header("Location: index.php");
    exit();
}

// Consulta SQL para obtener una película aleatoria
$sql = "SELECT * FROM peliculas ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

$movie = null;
if ($result->num_rows > 0) {
    // Obtener la primera fila de resultados
    $movie = $result->fetch_assoc();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Aleatorio</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #141414;
            color: #fff;
        }

        .container {
            text-align: center;
            margin: 50px auto;
            padding: 20px;
            max-width: 800px;
            background: #1f1f1f;
            border-radius: 8px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 36px;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 20px;
        }

        .movie {
            margin: 20px auto;
            text-align: left;
            background: #2c2c2c;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.3);
        }

        .movie h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #fff;
        }

        .movie p {
            font-size: 18px;
            margin: 5px 0;
            color: #b3b3b3;
        }

        .btn {
            display: inline-block;
            margin: 20px 10px 0;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            color: #fff;
            background: #e50914;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #f40612;
        }

        .btn.secondary {
            background: #333;
        }

        .btn.secondary:hover {
            background: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¿Qué ver en Netflix?</h1>
        
        <?php if ($movie): ?>
            <div class="movie">
                <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
                <p><strong>Género:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                <p><strong>Estreno:</strong> <?php echo date("d F Y", strtotime($movie['premiere'])); ?></p>
                <p><strong>Duración:</strong> <?php echo $movie['duracion']; ?> minutos</p>
                <p><strong>Calificación IMDb:</strong> <?php echo $movie['score']; ?></p>
                <p><strong>Idioma:</strong> <?php echo htmlspecialchars($movie['language']); ?></p>
            </div>
        <?php else: ?>
            <p>No se pudo obtener una película aleatoria. Por favor, inténtalo de nuevo.</p>
        <?php endif; ?>

        <a href="peliculas.php" class="btn">Generar otra película</a>
        <a href="busqueda.php" class="btn secondary">Filtrado de películas</a>
        <a href="forum.php" class="btn secondary">Comentarios</a>
        <a href="enviar_mensaje.php" class="btn secondary">Escribir un comentario</a>
        <a href="index.php" class="btn secondary">Volver al Inicio</a>
    </div>
</body>
</html>

