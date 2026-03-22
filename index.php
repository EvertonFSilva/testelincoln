<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $descriptions = $_POST['description'] ?? [];
    $prices = $_POST['price'] ?? [];
    $totalBudget = 0;
    $tableContent = "";

    foreach ($descriptions as $index => $description) {
        if (trim($description) === '') continue;
        $cleanPrice = str_replace(',', '.', str_replace('.', '', $prices[$index]));
        $numericValue = floatval($cleanPrice);
        $totalBudget += $numericValue;

        $tableContent .= "
        <tr>
            <td class='td-desc'>" . htmlspecialchars($description) . "</td>
            <td class='td-price'>R$ " . number_format($numericValue, 2, ',', '.') . "</td>
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
    <html>
    <head>
    <style>
        @page { 
            margin: 0px; 
            background-color: #eeede9; 
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #000;
            margin: 0;
            padding: 40px 50px;
            background-color: #eeede9;
        }
        .header-table { width: 100%; border: none; margin-bottom: 20px; }
        .logo-image { width: 180px; }
        .company-info { text-align: right; font-size: 11px; line-height: 1.4; }
        .company-name { font-size: 19px; font-weight: bold; }
        
        .document-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 40px 0;
            letter-spacing: 6px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-bottom: 30px;
        }
        .main-table th { 
            background-color: #d1cfc5; border: 2px solid #000; 
            padding: 12px; text-align: left; font-size: 14px; text-transform: uppercase;
        }
        .td-desc { 
            padding: 12px; border: 2px solid #000; font-size: 15px; 
            width: 75%; word-wrap: break-word;
        }
        .td-price { 
            padding: 12px; border: 2px solid #000; font-size: 15px; 
            text-align: right; width: 25%; 
        }
        .total-row td {
            font-weight: bold; border: 2px solid #000; padding: 15px;
            font-size: 18px; background-color: #d1cfc5;
        }
        .footer-container {
            margin-top: 50px;
            text-align: center;
            width: 100%;
            page-break-inside: avoid;
        }
        .representative { font-size: 18px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .date-text { 
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
    </head>
    <body>
        <table class='header-table'>
            <tr>
                <td style='border:none; vertical-align: middle;'>
                    <img src='$logoBase64' class='logo-image'>
                </td>
                <td class='company-info' style='border:none; vertical-align: middle;'>
                    <div class='company-name'>CL VIDRAÇARIA E SERRALHERIA</div>
                    RUA SALIM SELEM BICHARA, 120, CENTRO, CARAPEBUS - RJ<br>
                    (22) 99727-9683 | CNPJ: 42.762.160/0001-18
                </td>
            </tr>
        </table>
        
        <div class='document-title'>Orçamento</div>
        
        <table class='main-table'>
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
                    <td style='text-align: right;'>R$ " . number_format($totalBudget, 2, ',', '.') . "</td>
                </tr>
            </tbody>
        </table>

        <div class='footer-container'>
            <div class='representative'>Lincoln Fernandes Santos</div>
            <div class='date-text'>Emitido em: $formattedDate</div>
        </div>
    </body>
    </html>
    ";

    $dompdf = new Dompdf(['isRemoteEnabled' => true, 'defaultFont' => 'Helvetica']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CL Vidraçaria - Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeede9; font-family: 'Segoe UI', sans-serif; color: #000; overflow-x: hidden; }
        .navbar { background-color: #000; border-bottom: 2px solid #d1cfc5; }
        .container-main { max-width: 1000px; margin: 20px auto; padding: 0 10px; }
        .table-clean { width: 100%; border: 2px solid #000; border-collapse: collapse; background: #eeede9; }
        .table-clean th { background: #d1cfc5; border: 2px solid #000; padding: 15px; font-weight: 900; text-transform: uppercase; font-size: 13px; }
        .table-clean td { border: 2px solid #000; padding: 0; position: relative; }
        .input-ghost {
            width: 100%; border: none !important; background: transparent !important; 
            padding: 18px; font-size: 16px; font-weight: 600; color: #000; outline: none !important;
            box-shadow: none !important;
        }
        .btn-action { background: #000; color: #fff; border: none; padding: 16px; font-weight: bold; border-radius: 4px; transition: 0.3s; width: 100%; }
        .btn-action:hover { background: #333; }
        .btn-del { background: #dc3545; color: #fff; border: none; width: 100%; height: 100%; min-height: 60px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .box-total { background: #d1cfc5; border: 2px solid #000; padding: 25px; text-align: right; margin: 25px 0; }
        .total-v { font-size: 2.3rem; font-weight: 900; display: block; }

        @media (max-width: 768px) {
            .table-clean { border: none; }
            .table-clean thead { display: none; }
            .table-clean tr { display: block; margin-bottom: 20px; border: 2px solid #000; background: #eeede9; }
            .table-clean td { display: block; width: 100%; border: none !important; border-bottom: 1px solid #000 !important; padding-top: 32px !important; }
            .table-clean td:last-child { border-bottom: none !important; padding-top: 0 !important; }
            .table-clean td::before { content: attr(data-label); position: absolute; top: 10px; left: 15px; font-size: 10px; font-weight: 900; text-transform: uppercase; color: #666; }
        }
    </style>
</head>
<body>
<nav class="navbar"><div class="container justify-content-center"><span class="navbar-brand text-white fw-bold">CL VIDRAÇARIA</span></div></nav>
<div class="container-main">
    <form method="POST">
        <table class="table-clean">
            <thead>
                <tr>
                    <th>Descrição do Serviço / Produto</th>
                    <th style="width: 220px;">Valor Unitário</th>
                    <th id="th-act" style="width: 100px; display: none;">Ação</th>
                </tr>
            </thead>
            <tbody id="list"></tbody>
        </table>
        <button type="button" class="btn-action mt-3" onclick="newRow()">+ ADICIONAR NOVO ITEM</button>
        <div class="box-total">
            <span class="small fw-bold">VALOR TOTAL DO ORÇAMENTO</span>
            <span class="total-v">R$ <span id="v-total">0,00</span></span>
        </div>
        <button type="submit" class="btn-action btn-lg py-4 mb-5 shadow">GERAR PDF PROFISSIONAL</button>
    </form>
</div>
<script>
function newRow() {
    const list = document.getElementById('list');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td data-label="Descrição do Serviço / Produto">
            <input type="text" name="description[]" class="input-ghost" placeholder="Digite a descrição..." required>
        </td>
        <td data-label="Valor Unitário">
            <div class="d-flex align-items-center px-3">
                <span class="fw-bold">R$</span>
                <input type="text" name="price[]" class="input-ghost text-end" placeholder="0,00" oninput="m(this); calc();" required>
            </div>
        </td>
        <td class="act-td" style="display: none;">
            <button type="button" class="btn-del" onclick="this.closest('tr').remove(); calc(); check();">REMOVER</button>
        </td>
    `;
    list.appendChild(tr);
    check();
}
function check() {
    const rows = document.querySelectorAll('#list tr');
    const th = document.getElementById('th-act');
    const show = rows.length > 1;
    th.style.display = show ? 'table-cell' : 'none';
    document.querySelectorAll('.act-td').forEach(td => td.style.display = show ? 'table-cell' : 'none');
}
function m(i) {
    let v = i.value.replace(/\D/g, '');
    v = (v/100).toFixed(2).replace('.', ',');
    v = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    i.value = v;
}
function calc() {
    let s = 0;
    document.querySelectorAll('input[name="price[]"]').forEach(i => {
        let v = i.value.replace(/\./g, '').replace(',', '.');
        s += parseFloat(v) || 0;
    });
    document.getElementById('v-total').innerText = s.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}
newRow();
</script>
</body>
</html>