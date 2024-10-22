<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Estoque</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<body>

<?php
include('conexao.php'); // Certifique-se de que esta conexão é para o PostgreSQL

// Verifica se o formulário foi enviado
if (isset($_POST['deletar'])) {
    $cod_produto = $_POST['deletar'];

    // Verifica se o produto existe antes de tentar deletar
    $sql_verificar = "SELECT * FROM produto WHERE cod_produto = $1";
    $result_verificar = pg_prepare($conexao, "verificar_produto", $sql_verificar);
    $result_verificar = pg_execute($conexao, "verificar_produto", array($cod_produto));

    if (pg_num_rows($result_verificar) > 0) {
        // Se o produto existir, realiza a exclusão
        $sql = "DELETE FROM produto WHERE cod_produto = $1";
        $result_delete = pg_prepare($conexao, "deletar_produto", $sql);
        $result_delete = pg_execute($conexao, "deletar_produto", array($cod_produto));

        if ($result_delete) {
            echo "<h1>Produto excluído com sucesso</h1>";
        } else {
            echo "<h1>Erro ao excluir o produto: " . pg_last_error($conexao) . "</h1>";
        }
    } else {
        // Se o produto não existir, exibe mensagem de erro
        echo "<h1>Erro: Produto com código $cod_produto não encontrado</h1>";
    }
} else {
    echo "<h1>Nenhum produto especificado para exclusão</h1>";
}

// Fecha a conexão com o banco de dados
pg_close($conexao);
?>

</body>
</html>
