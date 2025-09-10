-- Script de inicialização do MySQL para CKO Framework

-- Criar banco de dados se não existir
CREATE DATABASE IF NOT EXISTS cko_framework CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE cko_framework;

-- Criar tabela de exemplo (pode ser removida depois)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário de exemplo
INSERT IGNORE INTO users (name, email) VALUES 
('Admin', 'admin@example.com'),
('Usuário Teste', 'teste@example.com');

-- Garantir que o usuário cko_user tenha acesso ao banco
GRANT ALL PRIVILEGES ON cko_framework.* TO 'cko_user'@'%';
FLUSH PRIVILEGES;
