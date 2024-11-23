<?php
// Configuración de la base de datos
$servername = "db"; // Cambia según tu configuración
$username = "root";  // Cambia según tu configuración
$password = "rootpassword"; // Cambia según tu configuración
$dbname = "peliculas"; // Nombre de tu base de datos

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los géneros de la base de datos
$genres = [];
$genreQuery = "SELECT DISTINCT genre FROM peliculas ORDER BY genre ASC";
$genreResult = $conn->query($genreQuery);
if ($genreResult->num_rows > 0) {
    while ($row = $genreResult->fetch_assoc()) {
        $genres[] = $row['genre'];
    }
}

// Obtener los idiomas de la base de datos
$languages = [];
$languageQuery = "SELECT DISTINCT language FROM peliculas ORDER BY language ASC";
$languageResult = $conn->query($languageQuery);
if ($languageResult->num_rows > 0) {
    while ($row = $languageResult->fetch_assoc()) {
        $languages[] = $row['language'];
    }
}

// Manejar el envío del formulario y la búsqueda
$movies = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedGenre = $_POST['genre'] ?? '';
    $selectedLanguage = $_POST['language'] ?? '';
    
    // Construir consulta basada en los filtros seleccionados
    $query = "SELECT * FROM peliculas WHERE 1=1";
    if (!empty($selectedGenre)) {
        $query .= " AND genre = '" . $conn->real_escape_string($selectedGenre) . "'";
    }
    if (!empty($selectedLanguage)) {
        $query .= " AND language = '" . $conn->real_escape_string($selectedLanguage) . "'";
    }
    $query .= " GROUP BY id"; // Aseguramos que no se repiten películas

    // Ejecutar la consulta
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Helper - Búsqueda</title>
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
            max-width: 900px;
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

        p, label {
            font-size: 18px;
            color: #b3b3b3;
        }

        select, button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button {
            background-color: #e50914;
            font-weight: bold;
        }

        button:hover {
            background-color: #f40612;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #1f1f1f;
            color: #b3b3b3;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #333;
        }

        th {
            background-color: #e50914;
            color: white;
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
        Netflix Helper - Búsqueda de Películas
    </header>
    <div class="container">
        <h2>Encuentra Películas</h2>
        <form method="POST" action="">
            <label for="genre">Género:</label>
            <select name="genre" id="genre">
                <option value="">Todos</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo (isset($selectedGenre) && $selectedGenre == $genre) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="language">Idioma:</label>
            <select name="language" id="language">
                <option value="">Todos</option>
                <?php foreach ($languages as $language): ?>
                    <option value="<?php echo htmlspecialchars($language); ?>" <?php echo (isset($selectedLanguage) && $selectedLanguage == $language) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($language); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Buscar</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if (count($movies) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Género</th>
                            <th>Idioma</th>
                            <th>Duración</th>
                            <th>Calificación IMDb</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td><?php echo htmlspecialchars($movie['language']); ?></td>
                                <td><?php echo htmlspecialchars($movie['duracion']); ?> min</td>
                                <td><?php echo htmlspecialchars($movie['score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron resultados para los filtros seleccionados.</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <button onclick="location.href='index.php'">Volver al Inicio</button>
        <button onclick="location.href='peliculas.php'">Película Aleatoria</button>
    </div>
</body>
</html>

