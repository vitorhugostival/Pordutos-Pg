<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<body>
    

<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $cod_produto = $_POST['NOVOcod_produto'];
    $nome_produto = $_POST['NOVOnome_produto'];
    $tipo_produto = $_POST['NOVOtipo_produto'];
    $cod_barras = $_POST['NOVOcod_barras'];
    $preco_custo = $_POST['NOVOpreco_custo'];
    $preco_venda = $_POST['NOVOpreco_venda'];
    $grupo = $_POST['NOVOgrupo'];
    $sub_grupo = $_POST['NOVOsub_grupo'];
    $observacao = $_POST['NOVOobservacao'];

    // Verifica se uma nova imagem foi enviada
    if (isset($_FILES['NOVOimagem']) && $_FILES['NOVOimagem']['error'] == 0) {
        // Converte a imagem para binário
        $imagem = pg_escape_bytea(file_get_contents($_FILES['NOVOimagem']['tmp_name']));

        // Monta a query de atualização com a imagem
        $sql = "UPDATE produto 
                SET imagem = $1, nome_produto = $2, tipo_produto = $3, cod_barras = $4, preco_custo = $5, preco_venda = $6, grupo = $7, sub_grupo = $8, observacao = $9
                WHERE cod_produto = $10";
        $stmt = pg_prepare($conexao, "update_produto", $sql);
        $result = pg_execute($conexao, "update_produto", array($imagem, $nome_produto, $tipo_produto, $cod_barras, $preco_custo, $preco_venda, $grupo, $sub_grupo, $observacao, $cod_produto));
    } else {
        // Monta a query de atualização sem alterar a imagem
        $sql = "UPDATE produto 
                SET nome_produto = $1, tipo_produto = $2, cod_barras = $3, preco_custo = $4, preco_venda = $5, grupo = $6, sub_grupo = $7, observacao = $8
                WHERE cod_produto = $9";
        $stmt = pg_prepare($conexao, "update_produto", $sql);
        $result = pg_execute($conexao, "update_produto", array($nome_produto, $tipo_produto, $cod_barras, $preco_custo, $preco_venda, $grupo, $sub_grupo, $observacao, $cod_produto));
    }

    // Verifica se a execução da query foi bem-sucedida
    if ($result) {
        echo "Dados atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados: " . pg_last_error($conexao);
    }

    // Fecha a conexão
    pg_close($conexao);
}
?>

</body>
</html>
