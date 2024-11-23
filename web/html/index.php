<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Helper</title>
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
        Netflix Helper
    </header>
    <div class="container">
        <h2>¡Bienvenido!</h2>
        <p>¿No sabes qué ver en Netflix? Nosotros te ayudamos. Haz clic en el botón de películas y descubre una opción aleatoria para disfrutar.</p>
        <button onclick="location.href='peliculas.php'">Película aleatoria</button>
        <button onclick="location.href='busqueda.php'">Filtrado de películas</button>
        <button onclick="location.href='enviar_mensaje.php'">Escribir un comentario</button>
        <button onclick="location.href='forum.php'">Comentarios</button>
    </div>
</body>
</html>

