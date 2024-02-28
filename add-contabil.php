<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>FormDen Example</title>

    <!-- formden.js communicates with FormDen server to validate fields and submit via AJAX -->
    <script type="text/javascript" src="https://formden.com/static/cdn/formden.js"></script>

    <!-- Special version of Bootstrap that is isolated to content wrapped in .bootstrap-iso -->
    <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />

    <!-- Font Awesome (added because you use icons in your prepend/append) -->
    <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

    <!-- Inline CSS based on choices in "Settings" tab -->
    <style>
        .bootstrap-iso .formden_header h2,
        .bootstrap-iso .formden_header p,
        .bootstrap-iso form {
            font-family: Arial, Helvetica, sans-serif;
            color: black
        }

        .bootstrap-iso form button,
        .bootstrap-iso form button:hover {
            color: white !important;
        }

        .asteriskField {
            color: red;
        }
    </style>

    <!-- Include jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" />

    <script>
        $(document).ready(function() {
            var date_input = $('input[name="date"]'); //our date input has the name "date"
            var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
                format: 'dd/mm/yyyy',
                container: container,
                todayHighlight: true,
                autoclose: true,
            })
        })
    </script>
</head>

<body>

    <?php
    session_start();

    include('includes/header.php');
    include('../middleware/adminMiddleware.php');

    // Consulta para obter o total de registros
    $sqlFornecedores = "SELECT id, RazaoSocial FROM tbfornecedor GROUP BY RazaoSocial";
    $resultFornecedores = $con->query($sqlFornecedores);
    // $result = $con->query($sql);
    $sqlCCusto = "SELECT tipo FROM tbl_centrocusto";
    $resultCCusto = $con->query($sqlCCusto);

    // Consulta para obter o total de registros
    $sqlCCustoContabil = "SELECT CCusto, Descricao_Ccusto FROM tb_ccusto GROUP BY Descricao_CCusto";
    $resultCCustoContabil = $con->query($sqlCCustoContabil);
    // $result = $con->query($sql);
    // $sqlCusto = "SELECT tipo FROM tb_ccusto";
    // $resultCusto = $con->query($sqlCusto);

    // Consulta para obter o total de registros
    $sqlCentroCusto = "SELECT CodCategoria, Descricao FROM tbl_centrocusto";
    $resultCentroCusto = $con->query($sqlCentroCusto);
    // $result = $con->query($sql);
    //   $sqlDescricao = "SELECT Descricao FROM tbl_centrocusto";
    //   $resultDescricao = $con->query($sqlDescricao);

    // Consulta para obter o total de registros
    $sqlTipoContabil = "SELECT id_tipo, Descr_tipo FROM tbl_tipo_contabil GROUP BY Descr_tipo";
    $resultTipoContabil = $con->query($sqlTipoContabil);
    // $result = $con->query($sql);
    //  $sqltipo = "SELECT id_tipo FROM tbl_tipo_contabil";
    //  $resulttipo = $con->query($sqlCusto);
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h4>Registro Cliente</h4>
                            <a class="bi bi-x" style="font-size: 25px;" href="index.php" class="close"></a>                            
                        </div>
                    </div>
                    
                        
  
                    <div class="card-body">
                        <form action="code.php" method="POST" enctype="multipart/form-data">
                            <div class="row ">
                                <?php if ($resultFornecedores->num_rows > 0) : ?>
                                    <div class="col-md-2 mt-1">
                                        <label class="">Tipo Contabil</label>
                                        <select name="idcontabil" class="form-select form-select-padding-md" aria-label="Default select example">
                                            <option>Selecione Opção</option>
                                            <?php while ($rowTipoContabil = $resultTipoContabil->fetch_assoc()) : ?>
                                                <option value="<?php echo $rowTipoContabil["id_tipo"]; ?>">
                                                    <?php echo $rowTipoContabil["Descr_tipo"]; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                        <div class="col-md-10 mt-1">
                                            <label class="">Nome / Razao Social</label>
                                            <select name="IdEntidade" class="form-select form-select-padding-md" aria-label="Default select example">
                                                <option>Selecione Opção</option>
                                                <?php while ($rowFornecedores = $resultFornecedores->fetch_assoc()) : ?>
                                                    <option value="<?php echo $rowFornecedores["id"]; ?>">
                                                        <?php echo $rowFornecedores["RazaoSocial"]; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    
                                    <div class="col-md-3 mt-3">
                                        <label class="">Centro de Custo</label>
                                        <select name="idCentroCusto" id="id_CCusto" class="form-select form-select-padding-md" aria-label="Default select example">
                                            <option>Selecione Opção</option>
                                            <?php while ($rowCentroCusto = $resultCCustoContabil->fetch_assoc()) : ?>
                                                <option value="<?php echo $rowCentroCusto["CCusto"]; ?>">
                                                    <?php echo $rowCentroCusto["Descricao_Ccusto"]; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-9 mt-3">
                                        <label class="">Descrição</label>
                                        <select name="idSubCentroCusto" id="id_descricao" class="form-control form-select form-select-padding-md" <?php echo $subcategoria_id ?> aria-label="Default select example" required>
                                            <option value="">Selecione uma categoria</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="">Número Documento</label>
                                        <input type="text" name="NumDocumento" placeholder="Numero do documento" class="form-control">
                                    </div>
                                    <div class="col-md-3 mt-3 text-center">
                                        <label class="">Parcela</label>
                                        <select name="numParcelas" id="num_parcelas" class="form-select mb-1 text-center" aria-label="Default select example">
                                            <!-- Loop para gerar as opções -->
                                            <script>
                                                for (var i = 1; i <= 48; i++) {
                                                    document.write('<option>' + i + '</option>');
                                                }
                                            </script>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-3 text-center">
                                        <label class="">Valor da Parcela</label>
                                        <input type="text" name="valorParcela" id="valor_parcela" placeholder="Valor Total" class="form-control text-center" onkeypress="return(moeda(this,',','.',event))">
                                    </div>
                                    <div class="col-md-3 mt-3 text-center">
                                        <label class="">Data de Vencimento:</label>
                                        <input type="date" id="data_vencimento" name="dataVencimento" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-info mt-3" name="add_LancContabil_btn">Salvar</button>
                                    </div>
                                <?php else : ?>
                                    <p>Sem fornecedores disponíveis.</p>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/custom.js"></script>
    <script src="assets/js/drop.js"></script>

    <?php include('includes/footer.php'); ?>
</body>

</html>
