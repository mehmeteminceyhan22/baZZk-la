<?php
session_start();
require 'db.php';
require 'dil.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

$mesaj = '';
$hata = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urun_adi = trim($_POST['urun_adi']);
    $fiyat = trim($_POST['fiyat']);
    $kategori = $_POST['kategori'];
    $resim_yolu = '';
    
    // PHP DOSYA YÜKLEME VE GD KÜTÜPHANESİ İLE RESİM İŞLEME KRİTERİ
    if(isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
        $hedef_klasor = "img/";
        // Dosya adını güvenli hale getirme
        $dosya_adi = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES["resim"]["name"]));
        $hedef_dosya = $hedef_klasor . $dosya_adi;
        $resimTuru = strtolower(pathinfo($hedef_dosya, PATHINFO_EXTENSION));
        
        if($resimTuru == "jpg" || $resimTuru == "jpeg" || $resimTuru == "png") {
            // GD Library ile Resmi okuma
            if ($resimTuru == "png") {
                $kaynak = imagecreatefrompng($_FILES["resim"]["tmp_name"]);
            } else {
                $kaynak = imagecreatefromjpeg($_FILES["resim"]["tmp_name"]);
            }
            
            $genislik = imagesx($kaynak);
            $yukseklik = imagesy($kaynak);
            
            // Resmi standart 800px genişliğe orantılı küçültme (Performans optimizasyonu)
            $yeni_genislik = 800;
            $yeni_yukseklik = floor($yukseklik * ($yeni_genislik / $genislik));
            
            $yeni_resim = imagecreatetruecolor($yeni_genislik, $yeni_yukseklik);
            
            if ($resimTuru == "png") {
                imagealphablending($yeni_resim, false);
                imagesavealpha($yeni_resim, true);
            }
            
            imagecopyresampled($yeni_resim, $kaynak, 0, 0, 0, 0, $yeni_genislik, $yeni_yukseklik, $genislik, $yukseklik);
            
            // Resmi kaydetme
            if ($resimTuru == "png") {
                imagepng($yeni_resim, $hedef_dosya, 8); 
            } else {
                imagejpeg($yeni_resim, $hedef_dosya, 80); // %80 Kalite ile sıkıştırma
            }
            
            imagedestroy($kaynak);
            imagedestroy($yeni_resim);
            
            $resim_yolu = $hedef_dosya;
        } else {
            $hata = $secili_dil == 'tr' ? 'Sadece JPG ve PNG yükleyebilirsiniz.' : 'Only JPG and PNG allowed.';
        }
    }

    if (empty($hata) && !empty($urun_adi) && !empty($fiyat) && !empty($resim_yolu)) {
        $stmt = $db->prepare("INSERT INTO products (urun_adi, fiyat, resim_yolu, kategori) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$urun_adi, $fiyat, $resim_yolu, $kategori])) {
            $mesaj = $secili_dil == 'tr' ? 'Ürün GD Kütüphanesi ile işlenip vitrine eklendi!' : 'Product processed and added!';
        }
    }
}

$page_title = $secili_dil == 'tr' ? 'Yeni Ürün Ekle' : 'Add New Product';
$txt_urun_adi = $secili_dil == 'tr' ? 'ÜRÜN ADI' : 'PRODUCT NAME';
$txt_fiyat = $secili_dil == 'tr' ? 'FİYAT (₺)' : 'PRICE (₺)';
$txt_kategori = $secili_dil == 'tr' ? 'KATEGORİ' : 'CATEGORY';
$txt_resim = $secili_dil == 'tr' ? 'RESİM YÜKLE (JPG/PNG)' : 'UPLOAD IMAGE (JPG/PNG)';
$txt_ekle_btn = $secili_dil == 'tr' ? 'ÜRÜNÜ YAYINLA' : 'PUBLISH PRODUCT';
$txt_siteye_git = $secili_dil == 'tr' ? 'VİTRİNE GİT' : 'GO TO STORE';
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baZZkıla - <?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --bg-color: #050505; --text-color: #ffffff; --card-bg: #111; --accent-color: #ff2a2a; --border-color: #222; --glass-bg: rgba(5, 5, 5, 0.85); }
        body.theme-light { --bg-color: #f8f8f8; --text-color: #0a0a0a; --card-bg: #fff; --accent-color: #d10000; --border-color: #e0e0e0; --glass-bg: rgba(248, 248, 248, 0.9); }
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: var(--bg-color); } ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        body { background-color: var(--bg-color); color: var(--text-color); font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; transition: background 0.3s ease, color 0.3s ease; overflow-x: hidden; }
        
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background: var(--glass-bg); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-color); position: fixed; width: 100%; top: 0; z-index: 1000; box-sizing: border-box; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 8px; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
        .header h2:hover { color: var(--accent-color); }
        .header-nav { display: flex; align-items: center; gap: 20px; }
        .header-nav a { color: var(--text-color); text-decoration: none; font-size: 13px; font-weight: 600; letter-spacing: 2px; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .header-nav a:hover, .header-nav a.active { color: var(--accent-color); transform: translateY(-2px); }
        .theme-toggle-btn { background: transparent; color: var(--text-color); border: none; cursor: pointer; font-size: 16px; transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        .theme-toggle-btn:hover { transform: rotate(180deg); color: var(--accent-color); }
        .menu-btn { font-size: 22px; cursor: pointer; color: var(--text-color); transition: 0.3s; background: none; border: none; outline: none; margin-left: 10px; }
        .menu-btn:hover { color: var(--accent-color); transform: scale(1.1); }

        .sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1001; opacity: 0; visibility: hidden; transition: 0.4s; }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        .sidebar { position: fixed; top: 0; right: -400px; width: 350px; height: 100vh; background: var(--bg-color); border-left: 1px solid var(--border-color); z-index: 1002; transition: 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); display: flex; flex-direction: column; padding: 80px 50px; box-sizing: border-box; box-shadow: -10px 0 30px rgba(0,0,0,0.5); }
        .sidebar.active { right: 0; }
        .sidebar .close-btn { position: absolute; top: 25px; right: 40px; font-size: 30px; cursor: pointer; color: var(--text-color); transition: 0.3s; background: none; border: none; }
        .sidebar .close-btn:hover { color: var(--accent-color); transform: rotate(90deg); }
        .sidebar-links { display: flex; flex-direction: column; gap: 30px; margin-top: 20px; }
        .sidebar-links a { color: var(--text-color); text-decoration: none; font-size: 14px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase; transition: 0.3s; display: flex; align-items: center; gap: 15px; }
        .sidebar-links a:hover { color: var(--accent-color); padding-left: 10px; }
        
        .sidebar-bottom { margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 30px; display: flex; flex-direction: column; gap: 20px; }
        .lang-switch { display: flex; gap: 20px; }
        .lang-switch a { color: #666; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 2px; transition: 0.3s; }
        .lang-switch a.active { color: var(--text-color); border-bottom: 2px solid var(--accent-color); padding-bottom: 5px; }

        .form-container { max-width: 600px; margin: 120px auto 50px auto; padding: 40px; background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h1 { font-size: 20px; font-weight: 800; letter-spacing: 4px; margin: 0; text-transform: uppercase; }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-size: 10px; font-weight: 700; letter-spacing: 2px; color: #888; margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 15px; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 500; outline: none; transition: 0.3s; box-sizing: border-box; }
        .form-group input[type="file"] { padding: 10px; cursor: pointer; }
        .form-group input:focus, .form-group select:focus { border-color: var(--accent-color); box-shadow: 0 0 10px rgba(255, 42, 42, 0.1); }
        
        .btn-submit { width: 100%; padding: 18px; background: var(--text-color); color: var(--bg-color); border: none; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 3px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: var(--accent-color); color: #fff; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 42, 42, 0.2); }

        .alert { padding: 15px; background: rgba(0, 200, 83, 0.1); color: #00c853; border: 1px solid #00c853; border-radius: 6px; margin-bottom: 25px; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-align: center; }
        .alert-hata { background: rgba(255, 68, 68, 0.1); color: #ff4444; border-color: #ff4444; }
        
        @media (max-width: 600px) { .form-container { margin: 100px 20px; padding: 30px 20px; } }
    </style>
</head>
<body>

    <div class="header" id="navbar">
        <h2 onclick="window.location='index.php'">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="add_product.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="add_product.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            <span style="color:#333; margin:0 5px;">|</span>
            <a href="index.php" title="<?php echo $txt_siteye_git; ?>"><i class="fa-solid fa-store"></i></a>
            <a href="login.php?cikis=1" title="<?php echo $i18n[$secili_dil]['cikis']; ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <span style="color:#333; margin:0 5px;">|</span>
            <button class="menu-btn" onclick="toggleMenu()"><i class="fa-solid fa-bars-staggered"></i></button>
        </div>
    </div>

    <div class="sidebar-overlay" id="menuOverlay" onclick="toggleMenu()"></div>
    <div class="sidebar" id="sideMenu">
        <button class="close-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></button>
        
        <div class="sidebar-links">
            <a href="kendi_tasarla.php"><i class="fa-solid fa-wand-magic"></i> <?php echo $secili_dil == 'tr' ? 'KENDİ TASARLA' : 'DESIGN YOUR OWN'; ?></a>
            <a href="index.php"><i class="fa-solid fa-house"></i> <?php echo $secili_dil == 'tr' ? 'ANA SAYFA' : 'HOME'; ?></a>
            <a href="ai_tasarim.php"><i class="fa-solid fa-wand-magic-sparkles"></i> AI TASARIM</a>
            <a href="admin.php"><i class="fa-solid fa-chart-pie"></i> <?php echo $secili_dil == 'tr' ? 'YÖNETİCİ' : 'ADMIN'; ?></a>
            <a href="add_product.php" style="color:var(--accent-color);"><i class="fa-solid fa-plus"></i> <?php echo $page_title; ?></a>
            <a href="login.php?cikis=1"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?php echo $i18n[$secili_dil]['cikis']; ?></a>
        </div>

        <div class="sidebar-bottom">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnSide" style="margin-bottom:10px;">
                <i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>
            </button>
            <div class="lang-switch">
                <a href="add_product.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="add_product.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <div style="font-size:30px; color:var(--accent-color); margin-bottom:15px;"><i class="fa-solid fa-cube"></i></div>
            <h1><?php echo $page_title; ?></h1>
        </div>

        <?php if ($mesaj): ?><div class="alert"><i class="fa-solid fa-check-circle"></i> <?php echo $mesaj; ?></div><?php endif; ?>
        <?php if ($hata): ?><div class="alert alert-hata"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $hata; ?></div><?php endif; ?>

        <!-- Form dosyaları gönderebilmesi için enctype="multipart/form-data" eklendi -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><?php echo $txt_urun_adi; ?></label>
                <input type="text" name="urun_adi" required placeholder="<?php echo $secili_dil == 'tr' ? 'Örn: Siyah Oversize Tişört' : 'Ex: Black Oversize T-Shirt'; ?>">
            </div>
            
            <div class="form-group">
                <label><?php echo $txt_fiyat; ?></label>
                <input type="number" step="0.01" name="fiyat" required placeholder="899.00">
            </div>

            <div class="form-group">
                <label><?php echo $txt_kategori; ?></label>
                <select name="kategori" required>
                    <option value="Hazır Baskı"><?php echo $secili_dil == 'tr' ? 'Hazır Baskı' : 'Printed'; ?></option>
                    <option value="Nakış"><?php echo $secili_dil == 'tr' ? 'Nakış' : 'Embroidery'; ?></option>
                    <option value="Kişiye Özel"><?php echo $secili_dil == 'tr' ? 'Kişiye Özel' : 'Custom'; ?></option>
                </select>
            </div>

            <!-- Eski text input'u yerine gerçek dosya yükleme butonu (file upload) eklendi -->
            <div class="form-group">
                <label><?php echo $txt_resim; ?></label>
                <input type="file" name="resim" accept="image/jpeg, image/png" required>
            </div>

            <button type="submit" class="btn-submit"><?php echo $txt_ekle_btn; ?></button>
        </form>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sideMenu').classList.toggle('active');
            document.getElementById('menuOverlay').classList.toggle('active');
        }

        let isLightMode = localStorage.getItem('bazzkila_theme') === 'light';
        const themeBtnTop = document.getElementById('themeBtnTop');
        const themeBtnSide = document.getElementById('themeBtnSide');

        function updateThemeIcon() {
            if(isLightMode) { 
                document.body.classList.add('theme-light'); 
                if(themeBtnSide) themeBtnSide.innerHTML = '<i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>'; 
                if(themeBtnTop) themeBtnTop.innerHTML = '<i class="fa-solid fa-moon"></i>';
            } else { 
                document.body.classList.remove('theme-light'); 
                if(themeBtnSide) themeBtnSide.innerHTML = '<i class="fa-solid fa-sun"></i> <?php echo $secili_dil == 'tr' ? 'Aydınlık Tema' : 'Light Mode'; ?>'; 
                if(themeBtnTop) themeBtnTop.innerHTML = '<i class="fa-solid fa-sun"></i>';
            }
        }

        function toggleTheme() {
            isLightMode = !isLightMode;
            localStorage.setItem('bazzkila_theme', isLightMode ? 'light' : 'dark');
            updateThemeIcon();
        }

        updateThemeIcon();
    </script>
</body>
</html>