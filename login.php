<?php
session_start();
require 'db.php';

if (isset($_GET['cikis']) && $_GET['cikis'] == '1') {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // trim() komutu başta veya sonda kazara kalmış boşlukları temizler
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($kullanici && trim($kullanici['sifre']) == $sifre) {
        $_SESSION['user_id'] = $kullanici['id'];
        $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
        $_SESSION['rol'] = $kullanici['rol'];
        
        header("Location: index.php");
        exit;
    } else {
        $hata = "E-posta veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baZZkıla - Giriş Yap</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #050505; color: #fff; font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-container { background: #111; padding: 50px; border-radius: 12px; border: 1px solid #222; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; }
        
        .logo { font-size: 28px; font-weight: 900; letter-spacing: 4px; text-transform: uppercase; color: #fff; text-decoration: none; margin-bottom: 20px; display: block; }
        
        .form-group { margin-bottom: 25px; text-align: left; position: relative; }
        .form-group label { display: block; font-size: 11px; color: #888; margin-bottom: 8px; letter-spacing: 1px; text-transform: uppercase; font-weight: bold; }
        .form-group input { width: 100%; padding: 15px; background: #0a0a0a; color: #fff; border: 1px solid #333; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: 0.3s; outline: none; }
        .form-group input:focus { border-color: #ff4444; }
        
        .password-toggle { position: absolute; right: 15px; top: 40px; color: #888; cursor: pointer; transition: 0.3s; font-size: 14px; padding: 5px; }
        .password-toggle:hover { color: #fff; }
        
        .btn-submit { background: #fff; color: #000; border: none; padding: 16px; font-size: 14px; font-weight: 900; border-radius: 6px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; }
        .btn-submit:hover { background: #ff4444; color: #fff; transform: translateY(-2px); }
        
        .alert { padding: 15px; background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; border-radius: 6px; margin-bottom: 20px; font-size: 13px; font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="#" class="logo">baZZkıla</a>
        <p style="color:#888; font-size:11px; margin-bottom:30px; letter-spacing:2px; font-weight:bold;">SİSTEME GİRİŞ YAPIN</p>
        
        <?php if($hata): ?>
            <div class="alert"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $hata; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>E-Posta Adresi</label>
                <input type="email" name="email" required placeholder="admin@bazzkila.com">
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" id="sifreInput" name="sifre" required placeholder="••••••••">
                <i class="fa-solid fa-eye password-toggle" id="togglePassword" onclick="sifreGoster()"></i>
            </div>
            <button type="submit" class="btn-submit">GİRİŞ YAP</button>
        </form>
    </div>

    <script>
        function sifreGoster() {
            const sifreInput = document.getElementById('sifreInput');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (sifreInput.type === 'password') {
                sifreInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.style.color = '#ff4444'; 
            } else {
                sifreInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleIcon.style.color = '#888';
            }
        }
    </script>
</body>
</html>