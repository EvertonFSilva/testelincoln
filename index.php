<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $descriptions = $_POST['description'];
    $prices = $_POST['price'];

    $totalBudget = 0;
    $tableContent = "";

    foreach ($descriptions as $index => $description) {
        if (trim($description) === '') continue;

        $numericValue = floatval(str_replace(',', '.', str_replace('.', '', $prices[$index])));
        $totalBudget += $numericValue;

        $tableContent .= "
        <tr>
            <td style='padding: 15px; border: 2px solid #000; font-size: 18px; text-align: left;'>$description</td>
            <td style='padding: 15px; border: 2px solid #000; font-size: 18px; text-align: right; width: 200px;'>
                R$ " . number_format($numericValue, 2, ',', '.') . "
            </td>
        </tr>";
    }

    $formattedDate = date("d/m/Y");
    $fileNameDate = date("d-m-Y");

    $logoPath = realpath("logo.png");
    $logoBase64 = "";
    if ($logoPath && file_exists($logoPath)) {
        $logoBinary = file_get_contents($logoPath);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoBinary);
    }

    $html = "
    <style>
        @page { margin: 0px; }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #000;
            margin: 0;
            padding: 50px; 
            background-color: #eeede9;
        }
        .header-container { width: 100%; margin-bottom: 20px; }
        .logo-image { width: 220px; }
        .company-info { text-align: right; font-size: 13px; line-height: 1.6; }
        .company-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .document-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 40px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .document-title span {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        table { width: 100%; border-collapse: collapse; }
        th { 
            background-color: #d1cfc5; 
            color: #000;
            border: 2px solid #000; 
            padding: 15px; 
            text-align: left; 
            font-size: 18px;
            text-transform: uppercase;
        }
        .total-row td {
            font-weight: bold;
            border: 2px solid #000;
            padding: 15px;
            font-size: 20px;
        }
        .footer-fixed {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .representative {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .date-label {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
    <table class='header-container'>
        <tr>
            <td><img src='$logoBase64' class='logo-image'></td>
            <td class='company-info'>
                <div class='company-name'>CL VIDRAÇARIA E SERRALHERIA</div>
                RUA SALIM SELEM BICHARA, 120, CENTRO, CARAPEBUS - RJ<br>
                TELEFONE: (22) 99727-9683 | CNPJ: 42.762.160/0001-18
            </td>
        </tr>
    </table>
    <div class='document-title'><span>ORÇAMENTO</span></div>
    <table>
        <thead>
            <tr>
                <th>Descrição do Serviço / Produto</th>
                <th style='text-align: right;'>Valor Unitário</th>
            </tr>
        </thead>
        <tbody>
            $tableContent
            <tr class='total-row'>
                <td style='text-align: right;'>VALOR TOTAL</td>
                <td style='text-align: right;'>
                    R$ " . number_format($totalBudget, 2, ',', '.') . "
                </td>
            </tr>
        </tbody>
    </table>
    <div class='footer-fixed'>
        <div class='representative'>LINCOLN FERNANDES SANTOS</div>
        <div class='date-label'>$formattedDate</div>
    </div>
    ";

    $pdfOptions = new Options();
    $pdfOptions->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4");
    $dompdf->render();
    $dompdf->stream("Orcamento_CL_Vidracaria_$fileNameDate.pdf", ["Attachment" => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador CL Vidraçaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeede9; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .navbar { background-color: #000; padding: 1.2rem; }
        .navbar-brand { font-size: 1.1rem; white-space: normal; text-align: center; }
        
        .app-card { 
            max-width: 1000px; margin: 20px auto; padding: 10px;
        }
        
        .table { border: 2px solid #000; background-color: #eeede9; }
        .table thead th { 
            background-color: #d1cfc5; border: 2px solid #000;
            color: #000; font-weight: bold; padding: 12px; text-transform: uppercase;
        }
        .table tbody td { border: 2px solid #000; padding: 0; vertical-align: middle; }
        
        .input-cell {
            width: 100%; border: none; background: transparent; padding: 12px; font-size: 16px; font-weight: 500;
        }
        .input-cell:focus { outline: none; background-color: #ffffff; }
        
        .price-wrapper { display: flex; align-items: center; width: 100%; }
        .currency-symbol { padding-left: 12px; font-weight: bold; }
        
        .btn-new-item { 
            background-color: #000; color: #fff; border: none; 
            padding: 12px 25px; font-weight: bold; width: 100%; margin-bottom: 20px;
        }
        .btn-remove { 
            background-color: #dc3545; color: white; border: none; 
            padding: 12px; font-weight: bold; width: 100%;
        }
        .btn-submit { 
            background-color: #000; color: white; padding: 15px; 
            font-weight: bold; font-size: 1.1rem; width: 100%; border: none;
        }
        
        .total-display { 
            background: #d1cfc5; padding: 20px; text-align: right; border: 2px solid #000;
        }
        .total-text { font-size: 2rem; font-weight: 900; color: #000; }

        /* AJUSTES PARA CELULAR */
        @media (max-width: 768px) {
            .app-card { margin: 10px auto; }
            
            /* Transforma a tabela em lista */
            .table thead { display: none; } /* Esconde o cabeçalho na lista */
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            
            .table tr { margin-bottom: 15px; border: 2px solid #000; }
            .table td { border: none; border-bottom: 1px solid #ccc; position: relative; }
            .table td:last-child { border-bottom: none; }

            /* Identificadores visuais para os campos no mobile */
            .table td::before {
                content: attr(data-label);
                display: block;
                font-size: 10px;
                font-weight: bold;
                text-transform: uppercase;
                padding: 5px 12px 0;
                color: #666;
            }

            .total-text { font-size: 1.6rem; }
            .btn-new-item { padding: 15px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark shadow">
    <div class="container justify-content-center">
        <span class="navbar-brand fw-bold">CL VIDRAÇARIA E SERRALHERIA</span>
    </div>
</nav>

<div class="container">
    <div class="app-card">
        <form method="POST">
            <table class="table align-middle mb-4">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th style="width: 200px;">Valor</th>
                        <th id="actionHeader" style="width: 100px; text-align: center; display: none;">Ação</th>
                    </tr>
                </thead>
                <tbody id="itemList">
                    </tbody>
            </table>

            <button type="button" class="btn btn-new-item shadow-sm" onclick="addItem()">
                + ADICIONAR ITEM
            </button>

            <div class="total-display mb-4">
                <div class="small fw-bold text-uppercase">Total do Orçamento</div>
                <div class="total-text">R$ <span id="grandTotal">0,00</span></div>
            </div>

            <button type="submit" class="btn btn-submit shadow mb-5">
                GERAR PDF AGORA
            </button>
        </form>
    </div>
</div>

<script>
function addItem() {
    const list = document.getElementById('itemList');
    const row = document.createElement('tr');
    
    // Adicionei "data-label" para aparecer o nome do campo no mobile
    row.innerHTML = `
        <td data-label="Descrição do Serviço">
            <input type="text" name="description[]" class="input-cell" placeholder="Ex: Janela de Vidro..." required>
        </td>
        <td data-label="Valor (R$)">
            <div class="price-wrapper">
                <span class="currency-symbol">R$</span>
                <input type="text" name="price[]" class="input-cell text-end" placeholder="0,00" oninput="maskPrice(this); calculate();" required>
            </div>
        </td>
        <td class="p-0 action-cell" style="display: none;">
            <button type="button" class="btn-remove" onclick="removeRow(this)">REMOVER ITEM</button>
        </td>
    `;
    
    list.appendChild(row);
    toggleRemoveButtons();
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calculate();
    toggleRemoveButtons();
}

function toggleRemoveButtons() {
    const rows = document.querySelectorAll('#itemList tr');
    const actionHeader = document.getElementById('actionHeader');
    const shouldShow = rows.length > 1;

    actionHeader.style.display = shouldShow ? 'table-cell' : 'none';

    rows.forEach(row => {
        const cell = row.querySelector('.action-cell');
        if (cell) cell.style.display = shouldShow ? 'table-cell' : 'none';
    });
}

function maskPrice(el) {
    let val = el.value.replace(/\D/g, '');
    val = (val / 100).toFixed(2).replace('.', ',');
    val = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    el.value = val;
}

function calculate() {
    let sum = 0;
    document.querySelectorAll('input[name="price[]"]').forEach(input => {
        let val = input.value.replace(/\./g, '').replace(',', '.');
        sum += parseFloat(val) || 0;
    });
    document.getElementById('grandTotal').innerText = sum.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

// Inicia com um item
addItem();
</script>
</body>
</html>