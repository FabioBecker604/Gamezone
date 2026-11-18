CREATE DATABASE IF NOT EXISTS gamezone
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gamezone;

-- Categorias dos jogos
CREATE TABLE IF NOT EXISTS categorias (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Produtos da loja
CREATE TABLE IF NOT EXISTS produtos (
    id INT NOT NULL AUTO_INCREMENT,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    em_promocao TINYINT(1) NOT NULL DEFAULT 0,
    preco_promocional DECIMAL(10,2) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_produtos_categoria (categoria_id),
    CONSTRAINT fk_produtos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Usuarios cadastrados na loja
CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'cliente',
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

-- Pedidos realizados
CREATE TABLE IF NOT EXISTS pedidos (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pendente',
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forma_pagamento VARCHAR(30) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_pedidos_usuario (usuario_id),
    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Produtos que pertencem a cada pedido
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT NOT NULL AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_itens_pedido (pedido_id),
    KEY idx_itens_produto (produto_id),
    CONSTRAINT fk_itens_pedido_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_itens_pedido_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Mensagens enviadas pelo formulario de contato
CREATE TABLE IF NOT EXISTS mensagens (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Categorias iniciais
INSERT INTO categorias (nome) VALUES
('Ação'),
('Aventura'),
('Terror'),
('RPG'),
('Luta'),
('Corrida'),
('Esportes'),
('Tiro');
