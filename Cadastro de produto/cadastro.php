<?php
include("conexao.php"); // Inclua seu arquivo de conexão com PostgreSQL

// Função para converter imagem para binário
function converteParaBinario($caminhoArquivo) {
    $conteudoArquivo = file_get_contents($caminhoArquivo);
    return pg_escape_bytea($conteudoArquivo);
}

// Recebe os dados do formulário
$cod_produto = $_POST['cod_produto'];
$nome_produto = $_POST['nome_produto'];
$tipo_produto = $_POST['tipo_produto'];
$cod_barras = $_POST['cod_barras'];
$preco_custo = $_POST['preco_custo'];
$preco_venda = $_POST['preco_venda'];
$grupo = $_POST['grupo'];
$sub_grupo = $_POST['sub_grupo'];
$observacao = $_POST['observacao'];
$imagem = null; // Variável para armazenar o nome do arquivo

// Verifica se o arquivo foi enviado
if (isset($_FILES['imagem'])) {
    $imagem = $_FILES['imagem'];
    // Verifica se houve algum erro no upload do arquivo
    if ($imagem['error'] != 0) {
        die("Falha ao enviar o arquivo");
    }
    // Verifica o tamanho do arquivo (máx. 2MB)
    if ($imagem['size'] > 2097152) {
        die("Arquivo muito grande! Max 2MB");
    }
    // Diretório para salvar os arquivos
    $pasta = "imagem/"; // Certifique-se de que essa pasta exista

    // Cria a pasta se ela não existir
    if (!is_dir($pasta)) {
        if (!mkdir($pasta, 0777, true)) {
            die("Falha ao criar a pasta");
        }
    }

    // Nome único para o arquivo
    $novoNomeArquivo = uniqid() . '.' . strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
    // Define o caminho completo para salvar o arquivo
    $caminhoArquivo = $pasta . $novoNomeArquivo;
    // Move o arquivo para o diretório de destino
    $deu_certo = move_uploaded_file($imagem["tmp_name"], $caminhoArquivo);
    if (!$deu_certo) {
        die("Falha ao mover o arquivo para o diretório");
    }

    // Converte a imagem para binário
    $imagemBinaria = converteParaBinario($caminhoArquivo);

    echo "Arquivo enviado com sucesso!<br>";
}

// Verifica se o código do produto já existe na tabela 'produto'
$check_produto_sql = "SELECT cod_produto FROM produto WHERE cod_produto = $1";
$result_check_produto = pg_prepare($conexao, "check_produto", $check_produto_sql);
$result_check_produto = pg_execute($conexao, "check_produto", array($cod_produto));

// Verifica se o código de barras já existe na tabela 'produto'
$check_barras_sql = "SELECT cod_barras FROM produto WHERE cod_barras = $1";
$result_check_barras = pg_prepare($conexao, "check_barras", $check_barras_sql);
$result_check_barras = pg_execute($conexao, "check_barras", array($cod_barras));

// Condições para verificação de duplicidade
if (pg_num_rows($result_check_produto) > 0) {
    echo "Erro: Este código de produto já está cadastrado.";
} elseif (pg_num_rows($result_check_barras) > 0) {
    echo "Erro: Este código de barras já está cadastrado.";
} else {
    // SQL para inserir os dados no banco, incluindo o arquivo (se houver)
    $sql = "INSERT INTO produto (cod_produto, imagem, nome_produto, tipo_produto, cod_barras, preco_custo, preco_venda, grupo, sub_grupo, observacao) 
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";
    $stmt = pg_prepare($conexao, "insert_product", $sql);
    // Passa as variáveis para a função
    $result_insert = pg_execute($conexao, "insert_product", array($cod_produto, $imagemBinaria, $nome_produto, $tipo_produto, $cod_barras, $preco_custo, $preco_venda, $grupo, $sub_grupo, $observacao));
    // Verifica se a inserção foi bem-sucedida
    if ($result_insert) {
        echo "Produto cadastrado com sucesso";
    } else {
        echo "Erro: " . pg_last_error($conexao);
    }
}

// Fecha a conexão