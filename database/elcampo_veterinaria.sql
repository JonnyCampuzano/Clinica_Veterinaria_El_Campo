CREATE DATABASE IF NOT EXISTS elcampo_veterinaria
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE elcampo_veterinaria;

DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
    id_rol INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

INSERT INTO roles (nombre_rol) VALUES
('Administrador'),
('Veterinario'),
('Recepcionista');

-- Contraseña para todos: Campo2026*
INSERT INTO usuarios (nombre, apellido, correo, contrasena, estado, id_rol) VALUES
('Admin', 'Sistema', 'admin@elcampo.com', '$2y$10$1vfDfL7ip4Oe0LXEOOymxeOmJUUdylotlaJ8K4PPcGWtcdAvk5pwa', 'Activo', 1),
('Carlos', 'Veterinario', 'veterinario@elcampo.com', '$2y$10$1vfDfL7ip4Oe0LXEOOymxeOmJUUdylotlaJ8K4PPcGWtcdAvk5pwa', 'Activo', 2),
('Ana', 'Recepcionista', 'recepcion@elcampo.com', '$2y$10$1vfDfL7ip4Oe0LXEOOymxeOmJUUdylotlaJ8K4PPcGWtcdAvk5pwa', 'Activo', 3);
