<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'hepsi';
$siralama = isset($_GET['siralama']) ? $_GET['siralama'] : 'yeni';

$sql = "SELECT * FROM products";
$params = [];

if ($kategori !== 'hepsi') {
    $sql .= " WHERE kategori = :kategori";
    $params[':kategori'] = $kategori;
}

if ($siralama === 'yeni') {
    $sql .= " ORDER BY id DESC";
} elseif ($siralama === 'ucuz') {
    $sql .= " ORDER BY fiyat ASC";
} elseif ($siralama === 'pahali') {
    $sql .= " ORDER BY fiyat DESC";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($urunler);
?>