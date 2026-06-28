-- BANCO FEITO PELO SGBD POSTGREE
DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS usuarios;


-- USUÁRIOS

CREATE TABLE usuarios (
    id        SERIAL PRIMARY KEY,
    nome      VARCHAR(120) NOT NULL,
    email     VARCHAR(120) NOT NULL UNIQUE,
    senha     VARCHAR(255) NOT NULL,          -- guardada com password_hash()
    is_admin  BOOLEAN      NOT NULL DEFAULT FALSE
);


-- PRODUTOS 

CREATE TABLE produtos (
    id         SERIAL PRIMARY KEY,
    nome       VARCHAR(120)  NOT NULL,
    descricao  TEXT,
    preco      NUMERIC(10,2) NOT NULL,
    tipo       VARCHAR(20)   NOT NULL          -- 'pizza' ou 'bebida'
);

-- PEDIDOS

CREATE TABLE pedidos (
    id           SERIAL PRIMARY KEY,
    id_usuario   INTEGER       NOT NULL REFERENCES usuarios(id),
    data_pedido  TIMESTAMP     NOT NULL DEFAULT NOW(),
    valor_frete  NUMERIC(10,2) NOT NULL DEFAULT 0,
    valor_total  NUMERIC(10,2) NOT NULL DEFAULT 0,
    status       VARCHAR(30)   NOT NULL DEFAULT 'recebido',
    
    cep          VARCHAR(9),
    rua          VARCHAR(150),
    numero       VARCHAR(20),
    complemento  VARCHAR(100),
    bairro       VARCHAR(100),
    cidade       VARCHAR(100),
    estado       VARCHAR(2)
);


-- ITENS DO PEDIDO

CREATE TABLE itens_pedido (
    id              SERIAL PRIMARY KEY,
    id_pedido       INTEGER       NOT NULL REFERENCES pedidos(id),
    id_produto      INTEGER       NOT NULL REFERENCES produtos(id),
    quantidade      INTEGER       NOT NULL,
    preco_unitario  NUMERIC(10,2) NOT NULL
);

--  DADOS INICIAIS

INSERT INTO usuarios (nome, email, senha, is_admin) VALUES
('Administrador', 'admin@bellamassa.com', '$2y$10$bCOgCec9SWnW1U.rojd7hOFpLSVCusE9a7Y3K1jPmkdXZtlqPFD2y', TRUE),
('Cliente Teste', 'cliente@teste.com',    '$2y$10$bcWswOHikQGzHG2t9GgTJeG39px7lGK/A6XC/AM8NZgtEkgCiZXYm', FALSE);


INSERT INTO produtos (nome, descricao, preco, tipo) VALUES
('Pizza de Mussarela',  'Molho de tomate, mussarela e orégano.',              39.90, 'pizza'),
('Pizza de Calabresa',  'Molho de tomate, calabresa, cebola e orégano.',      42.90, 'pizza'),
('Pizza Quatro Queijos','Mussarela, provolone, parmesão e catupiry.',         46.90, 'pizza'),
('Pizza Portuguesa',    'Presunto, ovos, cebola, ervilha e mussarela.',       44.90, 'pizza'),
('Refrigerante Cola 2L','Garrafa de 2 litros, bem gelada.',                   12.00, 'bebida'),
('Suco de Laranja 500ml','Suco natural, sem conservantes.',                    9.00, 'bebida'),
('Água Mineral 500ml',  'Sem gás.',                                            5.00, 'bebida');
