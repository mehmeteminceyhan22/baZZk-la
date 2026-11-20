<?php
session_start();
require 'dil.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$urun_adi = isset($_GET['urun_adi']) ? htmlspecialchars($_GET['urun_adi']) : 'Bilinmeyen Ürün';
$fiyat = isset($_GET['fiyat']) ? htmlspecialchars($_GET['fiyat']) : '0';$renk = isset($_GET['renk']) ? htmlspecialchars($_GET['renk']) : '-';
$beden = isset($_GET['beden']) ? htmlspecialchars($_GET['beden']) : '-';$siparis_no = "BZKL-" . rand(100000, 999999);
$tarih = date("d.m.Y H:i");

// Fatura Çevirileri
$txt_fatura_baslik =$secili_dil == 'tr' ? 'Sipariş Faturası' : 'Order Invoice';
$txt_musteri =$secili_dil == 'tr' ? 'Müşteri:' : 'Customer:';
$txt_tarih =$secili_dil == 'tr' ? 'Tarih:' : 'Date:';
$txt_siparis_no =$secili_dil == 'tr' ? 'Sipariş No:' : 'Order No:';
$txt_odendi =$secili_dil == 'tr' ? 'ÖDENDİ' : 'PAID';
$txt_urun_aciklama =$secili_dil == 'tr' ? 'Ürün Açıklaması' : 'Product Description';
$txt_varyasyon =$secili_dil == 'tr' ? 'Varyasyon' : 'Variation';
$txt_tutar =$secili_dil == 'tr' ? 'Tutar' : 'Amount';
$txt_renk =$secili_dil == 'tr' ? 'Renk:' : 'Color:';
$txt_beden =$secili_dil == 'tr' ? 'Beden:' : 'Size:';
$txt_genel_toplam =$secili_dil == 'tr' ? 'GENEL TOPLAM:' : 'TOTAL AMOUNT:';
$txt_tesekkur =$secili_dil == 'tr' ? 'Bizi tercih ettiğiniz için teşekkür ederiz. Kendi lüksünüzü giymeye devam edin.' : 'Thank you for choosing us. Keep wearing your own luxury.';
$txt_indir_btn =$secili_dil == 'tr' ? '📄 Faturayı PDF Olarak İndir' : '📄 Download Invoice as PDF';
$txt_magaza_don =$secili_dil == 'tr' ? '← Mağazaya Geri Dön' : '← Back to Store';
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <title>baZZkıla - <?php echo $txt_fatura_baslik; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f4f4; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; padding: 40px 20px; margin: 0; }
        .fatura-kabi { background: #fff; width: 100%; max-width: 600px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 8px; color: #000; }
        .baslik { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .baslik h1 { margin: 0; font-size: 28px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .detay-satiri { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; }
        .urun-tablosu { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .urun-tablosu th, .urun-tablosu td { border-bottom: 1px solid #ddd; padding: 15px 0; text-align: left; font-size: 13px; }
        .urun-tablosu th { font-weight: bold; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .toplam { text-align: right; font-size: 18px; font-weight: 900; margin-top: 30px; border-top: 2px solid #000; padding-top: 15px; }
        .btn-indir { background: #000; color: #fff; border: none; padding: 15px 30px; font-size: 14px; font-weight: 700; cursor: pointer; border-radius: 5px; margin-bottom: 20px; transition: 0.3s; letter-spacing: 1px; }
        .btn-indir:hover { background: #ff2a2a; }
        .geri-link { color: #888; text-decoration: none; margin-top: 20px; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s; }
        .geri-link:hover { color: #000; }
    </style>
</head>
<body>
    <button class="btn-indir" onclick="pdfIndir()"><?php echo $txt_indir_btn; ?></button>
    
    <div class="fatura-kabi" id="pdf-alani">
        <div class="baslik">
            <h1>baZZkıla</h1>
            <div style="text-align: right; color: #666; font-size: 12px;">
                <strong><?php echo $txt_musteri; ?></strong> <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?><br>
                <strong><?php echo $txt_tarih; ?></strong> <?php echo$tarih; ?>
            </div>
        </div>
        <div class="detay-satiri">
            <span><strong><?php echo $txt_siparis_no; ?></strong> <?php echo$siparis_no; ?></span>
            <span style="color: #4CAF50; font-weight: bold; letter-spacing: 1px;"><?php echo $txt_odendi; ?></span>
        </div>
        <table class="urun-tablosu">
            <thead>
                <tr>
                    <th><?php echo $txt_urun_aciklama; ?></th>
                    <th><?php echo $txt_varyasyon; ?></th>
                    <th style="text-align: right;"><?php echo $txt_tutar; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php echo $urun_adi; ?></strong></td>
                    <td><?php echo $txt_renk; ?> <?php echo$renk; ?><br><?php echo $txt_beden; ?> <?php echo$beden; ?></td>
                    <td style="text-align: right; font-weight: bold;"><?php echo $fiyat; ?> ₺</td>
                </tr>
            </tbody>
        </table>
        <div class="toplam">
            <?php echo $txt_genel_toplam; ?> <?php echo$fiyat; ?> ₺
        </div>
        <p style="text-align: center; color: #888; font-size: 11px; margin-top: 40px; font-weight: 500;">
            <?php echo $txt_tesekkur; ?>
        </p>
    </div>

    <!-- HATA ÇÖZÜLDÜ: Link artık doğru bir şekilde index.php'ye gidiyor -->
    <a href="index.php" class="geri-link"><?php echo $txt_magaza_don; ?></a>

    <script>
        function pdfIndir() {
            const element = document.getElementById('pdf-alani');
            const opt = {
                margin: 0.5,
                filename: '<?php echo $siparis_no; ?>_Fatura.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>