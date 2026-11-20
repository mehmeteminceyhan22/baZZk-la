<?php
require 'dil.php';
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$urun = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$urun) {
    die($i18n[$secili_dil]['urun_bulunamadi']);
}

$gosterilen_kategori = mb_strtoupper($urun['kategori'], 'UTF-8');
if ($secili_dil == 'en') {
    if ($urun['kategori'] == 'Kişiye Özel') $gosterilen_kategori = 'CUSTOM';
    if ($urun['kategori'] == 'Nakış') $gosterilen_kategori = 'EMBROIDERY';
    if ($urun['kategori'] == 'Hazır Baskı') $gosterilen_kategori = 'PRINTED';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $urun['urun_adi']; ?> - baZZkıla</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; padding: 0; }
        .header { padding: 20px 50px; background: #111; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: #fff; text-decoration: none; font-weight: bold; font-size: 20px; letter-spacing: 2px;}
        .back-link { font-size: 12px !important; color: #aaa !important; font-weight: normal !important; transition: 0.3s;}
        .back-link:hover { color: #ff4444 !important; }
        .product-container { max-width: 1200px; margin: 60px auto; padding: 20px; display: flex; gap: 60px; }
        .image-section { flex: 1; position: relative; overflow: hidden; border-radius: 8px; background: #111; border: 1px solid #333; }
        .image-section img { width: 100%; display: block; transition: transform 0.5s ease; cursor: crosshair; }
        .image-section:hover img { transform: scale(1.6); }
        .details-section { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .category-badge { display: inline-block; background: #ff4444; color: #fff; padding: 6px 15px; border-radius: 30px; font-size: 10px; font-weight: bold; margin-bottom: 20px; width: fit-content; text-transform: uppercase; letter-spacing: 1px; }
        .details-section h1 { font-size: 2.2rem; margin: 0 0 10px 0; font-weight: 800; line-height: 1.2; }
        .details-section .price { font-size: 1.8rem; color: #aaa; margin-bottom: 30px; font-weight: 300; }
        .description { line-height: 1.8; color: #ccc; margin-bottom: 30px; font-size: 14px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .description ul { padding-left: 20px; color: #aaa; font-size: 13px; margin-top: 10px; }
        .option-group { margin-bottom: 25px; display: flex; gap: 20px; }
        .option-box { flex: 1; }
        .option-box label { display: block; font-size: 10px; color: #888; margin-bottom: 8px; letter-spacing: 1px; }
        .option-box select { width: 100%; padding: 12px; background: #1a1a1a; color: #fff; border: 1px solid #444; border-radius: 4px; font-size: 12px; cursor: pointer; outline: none; }
        .btn-add { background: #fff; color: #000; border: none; padding: 16px; font-size: 14px; font-weight: 900; border-radius: 4px; cursor: pointer; transition: background 0.3s; margin-top: 20px; letter-spacing: 1px; }
        .btn-add:hover { background: #ff4444; color: #fff; }
        @media (max-width: 900px) { .product-container { flex-direction: column; gap: 30px; } }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">baZZkıla</a>
        <a href="index.php" class="back-link"><?php echo $i18n[$secili_dil]['detay_don']; ?></a>
    </div>
    <div class="product-container">
        <div class="image-section">
            <img src="<?php echo $urun['resim_yolu']; ?>" alt="<?php echo $urun['urun_adi']; ?>" onerror="this.src='https://via.placeholder.com/800x800.png?text=baZZkila'">
        </div>
        <div class="details-section">
            <div class="category-badge"><?php echo $gosterilen_kategori; ?></div>
            <h1><?php echo $urun['urun_adi']; ?></h1>
            <div class="price"><?php echo number_format($urun['fiyat'], 2, ',', '.'); ?> ₺</div>
            <div class="description">
                <p><?php echo $secili_dil == 'tr' ? 'baZZkıla kalitesiyle üretilmiş premium sokak modası ürünü. Nefes alabilen yapısı ve rahat kalıbı ile kendi lüksünüzü hissedin.' : 'Premium streetwear produced with baZZkıla quality. Feel your own luxury with its breathable structure and comfortable fit.'; ?></p>
                <ul>
                    <li><?php echo $secili_dil == 'tr' ? '100% Premium Organik Pamuk' : '100% Premium Organic Cotton'; ?></li>
                    <li><?php echo $secili_dil == 'tr' ? 'Sokak modasına uygun oversize kalıp' : 'Oversize fit suitable for streetwear'; ?></li>
                    <li><?php echo $secili_dil == 'tr' ? 'Yüksek çözünürlüklü premium doku' : 'High resolution premium texture'; ?></li>
                </ul>
            </div>
            <div class="option-group">
                <div class="option-box">
                    <label><?php echo $i18n[$secili_dil]['detay_renk']; ?></label>
                    <select id="secili_renk">
                        <option value="Siyah"><?php echo $i18n[$secili_dil]['renk_siyah']; ?></option>
                        <option value="Beyaz"><?php echo $i18n[$secili_dil]['renk_beyaz']; ?></option>
                    </select>
                </div>
                <div class="option-box">
                    <label><?php echo $i18n[$secili_dil]['detay_beden']; ?></label>
                    <select id="secili_beden">
                        <option value="S">S - (160-170cm)</option>
                        <option value="M">M - (170-180cm)</option>
                        <option value="L">L - (180-188cm)</option>
                        <option value="XL">XL - (188cm+)</option>
                    </select>
                </div>
            </div>
            <button class="btn-add" onclick="sepeteEkle()"><?php echo $i18n[$secili_dil]['detay_sepete']; ?></button>
        </div>
    </div>
    <script>
        function sepeteEkle() {
            const urunAdi = "<?php echo addslashes($urun['urun_adi']); ?>";
            const fiyat = "<?php echo $urun['fiyat']; ?>";
            const renk = document.getElementById('secili_renk').value;
            const beden = document.getElementById('secili_beden').value;
            
            const url = `fatura.php?urun_adi=${encodeURIComponent(urunAdi)}&fiyat=${fiyat}&renk=${renk}&beden=${beden}`;
            window.location.href = url;
        }
    </script>
</body>
</html>