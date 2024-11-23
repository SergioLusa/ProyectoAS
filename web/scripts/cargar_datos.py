import os
import time
import pandas as pd
import mysql.connector
from kaggle.api.kaggle_api_extended import KaggleApi

# Configuración de Kaggle API
os.environ['KAGGLE_CONFIG_DIR'] = '/kaggle'  # Asegúrate de que el archivo kaggle.json esté en esta ubicación

# Crear una instancia de la API de Kaggle y autenticar
api = KaggleApi()
api.authenticate()

# Nombre del dataset de Kaggle y el archivo a descargar
dataset = 'luiscorter/netflix-original-films-imdb-scores'  # Cambié a tu dataset correcto
file_name = 'NetflixOriginals.csv'  # Cambié al nombre del archivo CSV correcto

# Descargar el archivo de Kaggle
api.dataset_download_file(dataset, file_name, path='/data')  # Asegúrate de que /data esté accesible en el contenedor

# Verificar si el archivo fue descargado
if not os.path.exists('/data/' + file_name):
    print(f"Error: No se descargó el archivo {file_name}")
else:
    print(f"Archivo {file_name} descargado correctamente.")

# Cargar el CSV descargado a un DataFrame de Pandas
df = pd.read_csv('/data/' + file_name, encoding='ISO-8859-1')  # O usa 'latin1'

time.sleep(3)
# Conectar con MariaDB (usando el contenedor de Docker)
conn = mysql.connector.connect(
    host='db',  # Nombre del contenedor de MariaDB
    user='user',
    password='userpassword',
    database='peliculas'  # Nombre de la base de datos
)

# Verificar la conexión
if conn.is_connected():
    print("Conexión a MariaDB exitosa")
else:
    print("Error: No se pudo conectar a la base de datos")

cursor = conn.cursor()
cursor.execute("""
	CREATE TABLE IF NOT EXISTS comentario (
	    id INT AUTO_INCREMENT PRIMARY KEY,
	    message VARCHAR(255)
	)
""")
# Crear la tabla 'peliculas' solo si no existe
cursor.execute("""
    CREATE TABLE IF NOT EXISTS peliculas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        genre VARCHAR(255),
        premiere DATETIME,
        duracion INT,
        score FLOAT,
        language VARCHAR(255)
    )
""")

# Convertir las fechas a formato datetime antes de insertarlas
df['Premiere'] = pd.to_datetime(df['Premiere'], errors='coerce').dt.strftime('%Y-%m-%d')

# Ahora intentamos insertar los datos. Si ya existen, simplemente se insertarán de nuevo.
for index, row in df.iterrows():
    try:
        # Verificar si la película ya existe por título
        cursor.execute("SELECT id FROM peliculas WHERE title = %s", (row['Title'],))
        result = cursor.fetchall()
        
        # Si no existe, insertar los datos
        if not result:
            cursor.execute("""
                INSERT INTO peliculas (title, genre, premiere, duracion, score, language)
                VALUES (%s, %s, %s, %s, %s, %s)
            """, (row['Title'], row['Genre'], row['Premiere'], row['Runtime'], row['IMDB Score'], row['Language']))
    except mysql.connector.Error as err:
        continue

# Commit y cerrar conexión
conn.commit()
cursor.close()
conn.close()

print("Datos importados correctamente a MariaDB")

