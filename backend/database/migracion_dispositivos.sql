-- Migración: tabla "dispositivos" (sesión recordada / "recuérdame").
--
-- A diferencia de calendario.sql, este archivo NO borra el esquema: se puede
-- aplicar sobre una base de datos ya poblada (producción) sin perder los
-- usuarios existentes. Es idempotente: si la tabla ya existe, no hace nada.

-- La tabla de referencia debe ser InnoDB para que la clave foránea funcione.
-- Si la consulta siguiente no devuelve InnoDB, hay que migrarla antes:
--   ALTER TABLE users ENGINE=InnoDB;
SELECT ENGINE FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users';

CREATE TABLE IF NOT EXISTS dispositivos (
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
