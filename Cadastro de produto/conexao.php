<?php
$servidor = "localhost";    // Ou IP do servidor PostgreSQL
$porta = "5432";              // Porta que o PostgreSQL está usando
$usuario = "postgres";      // Usuário do PostgreSQL
$senha = "wnvi1405";        // Senha do PostgreSQL
$dbname = "estoque";        // Nome do banco de dados no PostgreSQL

// Conexão usando pg_connect
$conexao = pg_connect("host=$servidor port=$porta dbname=$dbname user=$usuario password=$senha");

// Verifica se a conexão foi estabelecida
if (!$conexao) {
    die("Erro na conexão com o PostgreSQL: " . pg_last_error());
} else {
    echo "Conexão com PostgreSQL realizada com sucesso!";
}
?>

