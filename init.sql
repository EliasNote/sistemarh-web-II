CREATE DATABASE IF NOT EXISTS sistema_rh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_rh;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Cargos
CREATE TABLE IF NOT EXISTS cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    salario_base DECIMAL(10, 2) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo_id INT NOT NULL,
    setor VARCHAR(100) NOT NULL,
    data_contratacao DATE NOT NULL,
    status ENUM('Ativo', 'Férias', 'Inativo') DEFAULT 'Ativo',
    FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserts
INSERT INTO usuarios (usuario, senha) VALUES ('admin', 'admin') ON DUPLICATE KEY UPDATE usuario='admin';

INSERT INTO cargos (nome, salario_base, descricao) VALUES 
('Analista de RH', 3500.00, 'Responsavel pelo recrutamento e selecao.'), 
('Desenvolvedor Full Stack', 6000.00, 'Atua no desenvolvimento web.') 
ON DUPLICATE KEY UPDATE id=id;