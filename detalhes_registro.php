<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "emporium";
$dbname = "aut_coml";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar se o ID do registro foi fornecido via parâmetro GET
if(isset($_GET['id'])) {
    // ID do registro fornecido
    $id = $_GET['id'];

    // Query para selecionar os detalhes do registro com base no ID fornecido
    $sql = "SELECT  status, dt_pagamento, numParcelas, vr_pago, type_pag, id_usuario, nome_user FROM tbl_mov_contabil WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Retorna os detalhes do registro como JSON
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        // Se o registro não for encontrado, retorna um JSON vazio
        echo json_encode(array());
    }
} else {
    // Se o ID do registro não for fornecido, retorna um JSON vazio
    echo json_encode(array());
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
