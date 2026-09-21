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
    
    onesignal_sub_id VARCHAR(255) DEFAULT NULL,
    
    daily_notifications_active TINYINT(1) NOT NULL DEFAULT 1,
    hora_notificacion TIME DEFAULT NULL,
    notificacion_pendiente_id VARCHAR(255) DEFAULT NULL,
    
    -- Trazabilidad básica
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);  