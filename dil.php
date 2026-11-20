<?php
// Hafıza motorunu başlat (Eğer başlatılmamışsa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PHP Çerez (Cookie) Kullanımı Kriteri (30 Günlük Kayıt)
if (isset($_GET['lang'])) {
    $secili_dil = $_GET['lang'];
    setcookie("bazzkila_dil", $secili_dil, time() + (86400 * 30), "/"); 
    $_SESSION['bazzkila_lang'] = $secili_dil;
} else {
    // Çerezde dil varsa onu al, yoksa session'a bak, o da yoksa TR yap
    $secili_dil = isset($_COOKIE['bazzkila_dil']) ? $_COOKIE['bazzkila_dil'] : (isset($_SESSION['bazzkila_lang']) ? $_SESSION['bazzkila_lang'] : 'tr');
}

$i18n = [
    'tr' => [
        'slogan' => 'KENDİ LÜKSÜNÜ GİY.',
        'iletisim' => 'İLETİŞİM',
        'cikis' => 'ÇIKIŞ',
        'incele' => 'İNCELE',
        'urun_bulunamadi' => 'ÜRÜN BULUNAMADI.',
        'filtre_hepsi' => 'HEPSİ',
        'filtre_kisiye' => 'KİŞİYE ÖZEL',
        'filtre_nakis' => 'NAKIŞLILAR',
        'filtre_hazir' => 'HAZIR BASKI',
        'sira_yeni' => 'EN YENİLER',
        'sira_ucuz' => 'UCUZDAN PAHALIYA',
        'sira_pahali' => 'PAHALIDAN UCUZA',
        'detay_don' => '← MAĞAZAYA DÖN',
        'detay_renk' => 'RENK SEÇİMİ',
        'detay_beden' => 'BEDEN (OVERSIZE)',
        'detay_sepete' => 'SEPETE EKLE & FATURA OLUŞTUR',
        'renk_siyah' => 'Siyah (Black)',
        'renk_beyaz' => 'Beyaz (White)'
    ],
    'en' => [
        'slogan' => 'WEAR YOUR OWN LUXURY.',
        'iletisim' => 'CONTACT',
        'cikis' => 'LOGOUT',
        'incele' => 'VIEW DETAILS',
        'urun_bulunamadi' => 'NO PRODUCTS FOUND.',
        'filtre_hepsi' => 'ALL',
        'filtre_kisiye' => 'CUSTOM',
        'filtre_nakis' => 'EMBROIDERY',
        'filtre_hazir' => 'READY PRINT',
        'sira_yeni' => 'NEWEST',
        'sira_ucuz' => 'PRICE: LOW TO HIGH',
        'sira_pahali' => 'PRICE: HIGH TO LOW',
        'detay_don' => '← BACK TO STORE',
        'detay_renk' => 'COLOR SELECTION',
        'detay_beden' => 'SIZE (OVERSIZE)',
        'detay_sepete' => 'ADD TO CART & INVOICE',
        'renk_siyah' => 'Black',
        'renk_beyaz' => 'White'
    ]
];
?>