-- Schema para CKO Framework
-- Tabela de usuários

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir alguns usuários de exemplo
INSERT OR IGNORE INTO users (name, email) VALUES 
('Admin', 'admin@example.com'),
('Usuário Teste', 'teste@example.com'),
('João Silva', 'joao@example.com');
