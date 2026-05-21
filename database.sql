CREATE DATABASE IF NOT EXISTS intranet_generica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE intranet_generica;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ramais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidade VARCHAR(60) NOT NULL,
    ramal VARCHAR(20) NOT NULL,
    setor VARCHAR(120) NOT NULL,
    responsavel VARCHAR(120) DEFAULT NULL,
    observacao VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidade VARCHAR(60) NOT NULL,
    setor VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    grupo_email VARCHAR(255) DEFAULT NULL,
    responsavel VARCHAR(120) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS treinamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(80) DEFAULT NULL,
    descricao TEXT DEFAULT NULL,
    link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS links_uteis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    categoria VARCHAR(80) DEFAULT NULL,
    url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS popups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) DEFAULT NULL,
    message TEXT NOT NULL,
    style_type VARCHAR(30) NOT NULL DEFAULT 'info',
    button_label VARCHAR(80) DEFAULT NULL,
    button_url VARCHAR(255) DEFAULT NULL,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Banco genérico sem dados reais.
-- Para criar o primeiro administrador, gere um hash com password_hash() no PHP
-- e insira manualmente na tabela admins.
