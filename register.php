<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$hata = '';
$basari = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_soyad = trim($_POST['ad_soyad']);
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']); 

    if (!empty($ad_soyad) && !empty($email) && !empty($sifre)) {
        // E-posta daha önce alınmış mı kontrol et
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $hata = "Bu e-posta adresi zaten kayıtlı!";
        } else {
            // Yeni kullanıcıyı sisteme kaydet
            $kayit = $db->prepare("INSERT INTO users (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, 'musteri')");
            if($kayit->execute([$ad_soyad, $email, $sifre])) {
                $basari = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
                header("refresh:2;url=login.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baZZkıla - Kayıt Ol</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #050505; color: #fff; font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-container { background: #111; padding: 50px; border-radius: 12px; border: 1px solid #222; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; }
        .logo { font-size: 28px; font-weight: 900; letter-spacing: 4px; text-transform: uppercase; color: #fff; text-decoration: none; margin-bottom: 20px; display: block; }
        .form-group { margin-bottom: 25px; text-align: left; }
        .form-group label { display: block; font-size: 11px; color: #888; margin-bottom: 8px; letter-spacing: 1px; text-transform: uppercase; font-weight: bold; }
        .form-group input { width: 100%; padding: 15px; background: #0a0a0a; color: #fff; border: 1px solid #333; border-radius: 6px; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: #ff4444; }
        .btn-submit { background: #fff; color: #000; border: none; padding: 16px; font-size: 14px; font-weight: 900; border-radius: 6px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; }
        .btn-submit:hover { background: #ff4444; color: #fff; transform: translateY(-2px); }
        .alert { padding: 15px; background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; border-radius: 6px; margin-bottom: 20px; font-size: 13px; font-weight: bold; }
        .alert-success { background: rgba(0, 200, 83, 0.1); color: #00c853; border-color: #00c853; }
        .link-alt { display: block; margin-top: 20px; color: #888; font-size: 12px; text-decoration: none; transition: 0.3s; }
        .link-alt:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="index.php" class="logo">baZZkıla</a>
        <p style="color:#888; font-size:11px; margin-bottom:30px; letter-spacing:2px; font-weight:bold;">YENİ HESAP OLUŞTUR</p>
        
        <?php if($hata): ?><div class="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $hata; ?></div><?php endif; ?>
        <?php if($basari): ?><div class="alert alert-success"><i class="fa-solid fa-check"></i> <?php echo $basari; ?></div><?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="ad_soyad" required placeholder="Mehmet Emin Ceyhan">
            </div>
            <div class="form-group">
                <label>E-Posta Adresi</label>
                <input type="email" name="email" required placeholder="ornek@email.com">
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="sifre" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-submit">KAYIT OL</button>
        </form>
        <a href="login.php" class="link-alt">Zaten hesabın var mı? Giriş Yap</a>
    </div>
</body>
</html>