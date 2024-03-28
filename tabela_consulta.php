<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table Example</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
     <!-- formden.js communicates with FormDen server to validate fields and submit via AJAX -->
     <script type="text/javascript" src="https://formden.com/static/cdn/formden.js"></script>

    <style>
        
        element.style {
            position: relative;
            overflow: auto;

        }

        /* Estilo para a tabela */
        table {
            background-color: white;
            border: 1px solid #E0E0E0;
            width: 99%;
            line-height: 1.6;
            text-align: center;
            height: 50px;
        }

        tr {
            height: 40px;
        }

        th {
            padding: 8px;
            border-bottom: 1px solid #E0E0E0;
            text-align: left;
            background-color: #333;
            border-collapse: collapse;
            color: #BABABA;
            justify-content: space-evenly;
            position: sticky;


        }

        /* Add your custom styles here */
        div.container {
            width: 90%;
        }

        th {
            background-color: black;
        }

        tbody {
            position: relative;
            max-height: 600vh;

        }

        tbody::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 0px;
            /* Largura da barra lateral */
            background-color: #ccc;
            /* Cor da barra lateral */
            max-width: 50vh;
        }

        /* Estilo para o formulário de popup */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            z-index: 9999;
            border-radius: 8px;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        /* Estilo para o botão de fechar popup */
        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        /* Outros estilos */

        /* Estilo para o corpo da tabela */
        body {
            overflow-x: hidden;
            overflow-y: hidden;

        }

        

        .notification-icon {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 9999;
        }
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .highlight {
            background-color: #ffcc00 !important; /* cor amarela para destacar */
        }

        
             

        
    </style>
</head>

<body>
    <?php
    //Includes 
    session_start();

    include('includes/header.php');
    include('../middleware/adminMiddleware.php');

    ?>

    <span class="notification-icon">🔔<span class="notification-count" id="notificationCount">0</span></span>

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

    
    // Definir o número de registros por página
    $recordsPerPage = isset($_GET['recordsPerPage']) ? $_GET['recordsPerPage'] : 100;
    // Definir a página atual
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    // Calcular o índice de início da busca na query
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Query para selecionar os dados da tabela com paginação
    $sql = "SELECT tmc.id,  tmc.idcontabil, ttc.Descr_tipo,  tc.CCusto, tc.Descricao_Ccusto,tc2.Descricao , tmc.idSubCentroCusto, tmc.NumDocumento, tmc.numParcelas, tmc.valorParcela, tmc.dataVencimento, tmc.status, tmc.dt_pagamento, tmc.vr_pago, tmc.type_pag, tmc.id_usuario, tmc.nome_user      
FROM tbl_mov_contabil tmc, tbl_tipo_contabil ttc, tb_ccusto tc, tbl_centrocusto tc2 
where  tmc.idcontabil  = ttc.id_tipo and tmc.idCentroCusto = tc.CCusto and tmc.idSubCentroCusto = tc2.Seq and tmc.dataVencimento and tmc.dt_pagamento is null  group  by tmc.id 


 LIMIT $offset, $recordsPerPage";






    $result = $conn->query($sql);

    // Query para contar o número total de registros
    $totalRecordsQuery = "SELECT COUNT(*) AS total FROM tbl_mov_contabil";
    $totalRecordsResult = $conn->query($totalRecordsQuery);
    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
    // Calcular o número total de páginas
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Inicializa o contador de notificações
    $registrosProximos = 0;
    ?>
<div class="mt-5">
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Id</th>
                <th>Tipo</th>
                <th>Descricao</th>
                <th>Id_Centro</th>
                <th>Centro</th>
                <th>Id_Cat</th>
                <th>Categoria</th>
                <th>Nº_Doc</th>
                <th>Nº_Parc</th>
                <th>Vr_Parc</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th>Pagamento</th>
                <th>Pago</th>
                <th>Forma_Pag</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop para exibir os dados da tabela
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dataVencimento = strtotime($row["dataVencimento"]);
                    $diasRestantes = floor(($dataVencimento - time()) / (60 * 60 * 24)); // Usar floor para obter o número inteiro de dias
                    $class = ($diasRestantes <= 7) ? "highlight" : "";
                    echo "<tr class='$class' ondblclick='mostrarRegistro(" . $row["id"] . ")'>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["idcontabil"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Descr_tipo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["CCusto"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Descricao_Ccusto"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["idSubCentroCusto"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Descricao"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["NumDocumento"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["numParcelas"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["valorParcela"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["dataVencimento"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["dt_pagamento"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["vr_pago"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["type_pag"]) . "</td>";
                    echo "</tr>";

                    if ($class == "highlight") {
                        $registrosProximos++;
                    }
                }
            } else {
                echo "<tr><td colspan='15'>Nenhum resultado encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
    <div class="popup col-md-6" id="editForm">
        <span class="popup-close" onclick="fecharPopup()">&times;</span>
        <h2>Editar Registro</h2>
        <form action="code.php" id="editRecordForm" method="post" enctype="multipart/form-data">
            <div class="row mt-3">
                <!-- Campos de edição -->
                <div class="col-md-2" style="text-align:center">
                    <label for="editid">Código:</label>
                    <input type="text" id="editId" name="id" class="form-control" style="text-align: center;" readonly>
                </div>  
                <div class="col-md-2" style="text-align:center">
                    <!-- <label for="editStatus">Status:</label> -->
                    <!-- <input type="text" id="editStatus" name="status" class="form-control"><br><br>  -->
                    <label for="editNumParc">Nº Parcela:</label>
                    <input type="text" id="editNumParc" disabled name="NumParc" class="form-control" style="text-align:center" readonly>
                </div>
                <div class="col-md-3" style="text-align:center">
                    <label for="editvalorParcela">Valor Parcela:</label>
                    <input type="text" id="editvalorParcela" disabled name="valorParcela" class="form-control" style="text-align: center;" readonly>
                </div>
                <div class="col-md-5">
                    <label for="editIdPagamento">Id Pagamento:</label>
                    <input type="text" id="editIdPagamento" name="idPagamento" class="form-control" style="text-align: center;" >
                </div>
                <div class="mt-3">
                    <label for="editEnt">Entidade Contabil:</label>
                    <input type="text" id="editEnt" disabled name="Entidade" class="form-control" readonly>
                </div>
                <div class="mt-3">
                    <label for="editDescricao">Descrição Despesa:</label>
                    <input type="text" id="editDescricao" disabled name="DescricaoDesp" class="form-control" readonly>
                </div>

            </div>
            <div class="row mt-3">
                <div class="col-md-3" style="text-align:center">
                    <label for="editDtPagamento">Data de Pagamento:</label>
                    <input type="date" id="editDtPagamento" name="dt_pagamento" class="form-control" style="text-align: center;"><br><br>
                </div>
                <div class="col-md-6">

                    <label for="editTypePag">Selecione opção Pagamento:</label>
                    <select id="editTypePag" name="type_pag" class="form-control">>
                        <option value="Pix">Pix</option>
                        <option value="Credito">Cartão de Crédito</option>
                        <option value="Debito">Cartão de Débito</option>
                        <option value="Dinheiro">Dinheiro</option>
                    </select><br><br>
                </div>
                <div class="col-md-3" style="text-align:center">
                    <label for="editvr_pago">Valor Pago:</label>
                    <input type="text" id="editvr_pago" name="vr_pago" class="form-control" style="text-align: center; font-weight: bold;" onkeypress="return(moeda(this,',','.',event))"><br><br>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success" name="btn_quitacao" value="Salvar" >Baixa</button>
                </div>
            </div>
    </div>
    </form>
    </div>

    <!-- Fundo escurecido -->
    <div class="popup-overlay" id="popupOverlay" onclick="fecharPopup()"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>



    <script>
        function mostrarRegistro(id) {
            document.getElementById("editForm").style.display = "block";
            document.getElementById("popupOverlay").style.display = "block";

            document.getElementById("editId").value = id;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var dados = JSON.parse(this.responseText);
                    // document.getElementById("editStatus").value = dados.status;
                    document.getElementById("editDtPagamento").value = dados.dt_pagamento;
                    document.getElementById("editNumParc").value = dados.numParcelas;
                    document.getElementById("editvalorParcela").value = dados.valorParcela;
                    document.getElementById("editIdPagamento").value = dados.idPagamento;
                    document.getElementById("editEnt").value = dados.RazaoSocial;
                    document.getElementById("editDescricao").value = dados.Descricao;
                    document.getElementById("editTypePag").value = dados.type_pag;
                    document.getElementById("editvr_pago").value = dados.vr_pago;
                    // document.getElementById("editIdUsuario").value = dados.id_usuario;
                    // document.getElementById("editNomeUser").value = dados.nome_user;
                }
            };
            xhttp.open("GET", "detalhes_despesas_pagar.php?id=" + id, true);
            xhttp.send();
        }

        function fecharPopup() {
            document.getElementById("editForm").style.display = "none";
            document.getElementById("popupOverlay").style.display = "none";
        }




        // Atualiza o contador de notificações quando a página é carregada
        window.onload = function() {
            document.getElementById("notificationCount").innerText = "<?php echo $registrosProximos; ?>";
        }
    </script>
    
    <script>
        new DataTable('#example', {
            paging: true,
            scrollCollapse: true,
            scrollY: '50vh'
        });
    </script>
    <?php
// Fechar a conexão com o banco de dados
if ($conn) {
    $conn->close();
}
?>
 <?php include('includes/footer.php'); ?>

</body>

</html>
