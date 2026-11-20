<?php
// TCMB (Merkez Bankası) Harici XML API Entegrasyonu
$api_url = "https://www.tcmb.gov.tr/kurlar/today.xml";
$kur_verisi = @simplexml_load_file($api_url);

$usd = $kur_verisi ? $kur_verisi->Currency[0]->BanknoteSelling : 'Veri Cekilemedi';
$eur = $kur_verisi ? $kur_verisi->Currency[3]->BanknoteSelling : 'Veri Cekilemedi';

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'api_kaynagi' => 'TCMB Harici API',
    'kurlar' => [
        'USD_TRY' => (string)$usd,
        'EUR_TRY' => (string)$eur
    ]
]);
?>