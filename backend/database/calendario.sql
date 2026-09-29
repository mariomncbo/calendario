DROP SCHEMA IF EXISTS calendario_bd;

CREATE SCHEMA calendario_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE calendario_bd;

CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    
    access_token TEXT NOT NULL,
    refresh_token TEXT, 
    
    token_expires_at DATETIME,
    
    -- Trazabilidad básica
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Dispositivos con sesión recordada ("recuérdame").
--
-- La cookie que viaja al navegador NO es el token de Google, sino un valor
-- aleatorio opaco. Aquí solo se guarda su hash SHA-256, de modo que si alguien
-- lee la base de datos no puede suplantar la sesión de ningún dispositivo.
--
-- El borrado en cascada permite que "Borrar cuenta" (que elimina la fila de
-- users) limpie también los dispositivos sin una segunda consulta.
--
-- ENGINE=InnoDB es explícito porque la clave foránea solo funciona con este motor.
CREATE TABLE dispositivos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,

    -- Trazabilidad básica
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_uso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_dispositivos_usuario
        FOREIGN KEY (google_id) REFERENCES users(google_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;
