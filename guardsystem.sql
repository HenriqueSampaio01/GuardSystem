CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE ocorrencias (
    id SERIAL PRIMARY KEY,
    
    numero_registro INTEGER,
    natureza VARCHAR(255) NOT NULL,
    local VARCHAR(255) NOT NULL,
    data DATE NOT NULL,
    hora VARCHAR(10),
    solicitante VARCHAR(255) NOT NULL,
    
    -- Dados da Mãe
    nome_mae VARCHAR(255) NOT NULL,
    rg_mae VARCHAR(20),
    cpf_mae VARCHAR(14),
    profissao_mae VARCHAR(255),
    
    -- Dados do Pai
    nome_pai VARCHAR(255),
    rg_pai VARCHAR(20),
    cpf_pai VARCHAR(14),
    profissao_pai VARCHAR(255),
    
    -- Dados do Responsável
    nome_responsavel VARCHAR(255),
    rg_responsavel VARCHAR(20),
    profissao_responsavel VARCHAR(255),
    
    -- Endereço Residencial
    endereco_residencial VARCHAR(255),
    numero_residencial VARCHAR(50),
    bairro_residencial VARCHAR(255),
    complemento_residencial VARCHAR(255),
    telefone_residencial VARCHAR(20),
    
    -- Endereço Comercial
    endereco_comercial VARCHAR(255),
    numero_comercial VARCHAR(50),
    bairro_comercial VARCHAR(255),
    complemento_comercial VARCHAR(255),
    telefone_comercial VARCHAR(20),
    
    historico TEXT NOT NULL,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE SET NULL

    -- Assinaturas (registradas via app, não editáveis pelo site)
    assinatura_conselheiro TEXT NOT NULL,
    assinatura_responsavel TEXT NOT NULL,
    assinatura_solicitante TEXT NOT NULL
);

-- Tabelas geridas pelo app (fora do escopo do site)
-- Documentadas aqui para manter o .sql fiel ao banco real do Supabase.
CREATE TABLE adolescente (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_nasc DATE,
    escola VARCHAR(255)
);

CREATE TABLE ocorrencia_adolescente (
    id SERIAL PRIMARY KEY,
    id_adolescente INTEGER REFERENCES adolescente(id),
    id_ocorrencia INTEGER REFERENCES ocorrencias(id)
);