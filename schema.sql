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

-- Produtos iniciais para teste
INSERT INTO produtos
(categoria_id, nome, descricao, preco, imagem, estoque)
VALUES
(8, 'Call of Duty Black Ops 7',
 'Jogo de tiro e ação da franquia Call of Duty.',
 299.90, 'codblackops7.jpg', 10),

(4, 'Cyberpunk 2077',
 'RPG de mundo aberto em um futuro tecnológico.',
 199.90, 'cyberpunk2077.jpg', 10),

(7, 'EA Sports FC 27',
 'Jogo de futebol com clubes e competições.',
 299.90, 'eafc27.jpg', 10),

(4, 'Elden Ring',
 'RPG de fantasia em um vasto mundo aberto.',
 249.90, 'eldenring.jpg', 10),

(1, 'God of War Ragnarök',
 'Aventura de ação na mitologia nórdica.',
 249.90, 'godofwar.jpg', 10),

(6, 'Gran Turismo 7',
 'Simulador de corrida com diversos carros.',
 249.90, 'granturismo7.jpg', 10),

(5, 'Mortal Kombat 1',
 'Jogo de luta com personagens clássicos.',
 199.90, 'mk1.jpg', 10),

(7, 'NBA 2K27',
 'Simulador de basquete.',
 299.90, 'nba2k27.jpg', 10),

(2, 'Red Dead Redemption 2',
 'Aventura de mundo aberto no Velho Oeste.',
 199.90, 'rdr2.jpg', 10),

(3, 'Resident Evil 4 Remake',
 'Jogo de terror e sobrevivência.',
 179.90, 're4.jpg', 10),

(3, 'Resident Evil Requiem',
 'Nova experiência de terror e sobrevivência.',
 299.90, 'requiem.jpg', 10),

(1, 'Marvel Spider-Man',
 'Aventura de ação com o Homem-Aranha.',
 199.90, 'spiderman.jpg', 10),

(5, 'Street Fighter 6',
 'Jogo de luta da franquia Street Fighter.',
 179.90, 'streetfighter6.jpg', 10),

(3, 'The Last of Us Part I',
 'Aventura de sobrevivência em um mundo pós-apocalíptico.',
 199.90, 'thelastofus1.jpg', 10),

(3, 'The Last of Us Part II',
 'Continuação da história de sobrevivência.',
 199.90, 'thelastofus2.jpg', 10),

(4, 'The Witcher 3',
 'RPG de fantasia com exploração e aventuras.',
 149.90, 'witcher3.jpg', 10);