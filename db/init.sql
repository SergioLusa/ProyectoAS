CREATE DATABASE IF NOT EXISTS peliculas;
USE peliculas;

CREATE TABLE IF NOT EXISTS peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    genre VARCHAR(255),
    premiere DATE,
    duracion INT,
    score FLOAT,
    language VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS comentario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255)
);
