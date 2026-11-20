<?php
session_start();
require 'dil.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = $secili_dil == 'tr' ? 'Kendi Tişörtünü Tasarla' : 'Design Your Own T-Shirt';
$txt_renk_sec = $secili_dil == 'tr' ? '1. TİŞÖRT RENGİ' : '1. T-SHIRT COLOR';
$txt_resim_yukle = $secili_dil == 'tr' ? '2. TASARIM YÜKLE (PNG/JPG)' : '2. UPLOAD DESIGN (PNG/JPG)';
$txt_boyut = $secili_dil == 'tr' ? 'Baskı Boyutu' : 'Print Size';
$txt_yazi_ekle = $secili_dil == 'tr' ? '3. ÖZEL YAZI EKLE' : '3. ADD CUSTOM TEXT';
$txt_yazi_placeholder = $secili_dil == 'tr' ? 'Örn: baZZkıla 2026' : 'Ex: baZZkıla 2026';
$txt_yazi_rengi = $secili_dil == 'tr' ? 'Yazı Rengi' : 'Text Color';
$txt_beden = $secili_dil == 'tr' ? '4. BEDEN (OVERSIZE)' : '4. SIZE (OVERSIZE)';
$txt_siparis_ver = $secili_dil == 'tr' ? 'SEPETE EKLE & FATURA ÜRET' : 'ADD TO CART & CHECKOUT';
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baZZkıla - <?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --bg-color: #050505; --text-color: #ffffff; --card-bg: #111; --accent-color: #ff2a2a; --border-color: #222; --glass-bg: rgba(5, 5, 5, 0.85); --studio-bg: #0a0a0a; }
        body.theme-light { --bg-color: #f8f8f8; --text-color: #0a0a0a; --card-bg: #fff; --accent-color: #d10000; --border-color: #e0e0e0; --glass-bg: rgba(248, 248, 248, 0.9); --studio-bg: #e5e5e5; }
        
        body { background-color: var(--bg-color); color: var(--text-color); font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; transition: background 0.3s ease, color 0.3s ease; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: var(--bg-color); } ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        
        /* HEADER VE MENÜLER (DİĞER SAYFALARLA AYNI) */
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

        /* YÜZEN BUTONLAR */
        .whatsapp-float { position: fixed; bottom: 30px; right: 30px; background-color: #25D366; color: white; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 26px; box-shadow: 0 10px 20px rgba(0,0,0,0.4); z-index: 100; transition: 0.3s; text-decoration: none; }
        .whatsapp-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(37, 211, 102, 0.4); }
        .ai-float { position: fixed; bottom: 100px; right: 30px; background: linear-gradient(135deg, #ff4444, #8a2be2); color: white; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 22px; box-shadow: 0 10px 20px rgba(255,68,68,0.3); z-index: 100; transition: 0.3s; text-decoration: none; }
        .ai-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(138,43,226,0.5); }
        .custom-float { position: fixed; bottom: 170px; right: 30px; background: linear-gradient(135deg, #ff9100, #ffea00); color: #000; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 22px; box-shadow: 0 10px 20px rgba(255,145,0,0.3); z-index: 100; transition: 0.3s; text-decoration: none; }
        .custom-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(255,145,0,0.5); }

        /* TASARIM STÜDYOSU (PROFESYONEL UI) */
        .designer-grid { display: grid; grid-template-columns: 1.3fr 1fr; gap: 40px; max-width: 1300px; margin: 120px auto 50px auto; padding: 0 40px; }
        @media (max-width: 900px) { .designer-grid { grid-template-columns: 1fr; } }

        /* Önizleme (Manken) Paneli */
        .preview-box { background: var(--studio-bg); border-radius: 12px; border: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; position: relative; overflow: hidden; height: 600px; box-shadow: inset 0 0 50px rgba(0,0,0,0.1); }
        .tshirt-wrapper { position: relative; width: 100%; height: 100%; user-select: none; }
        
        /* Gerçek Manken Görseli */
        .tshirt-image { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; transition: opacity 0.4s ease; }
        
        /* Sürüklenebilir Baskı Alanı */
        .printable-area { position: absolute; top: 25%; left: 30%; width: 40%; height: 45%; border: 2px dashed transparent; display: flex; justify-content: center; align-items: center; z-index: 10; }
        
        /* Kumaşla Bütünleşen Grafik (Blend Mode Sihri) */
        .user-graphic { position: absolute; cursor: grab; transition: transform 0.05s ease; top: 10px; left: 50%; transform: translateX(-50%); width: 120px; z-index: 12; }
        .user-graphic:active { cursor: grabbing; }
        .user-graphic img { width: 100%; pointer-events: none; transition: 0.3s; }
        
        /* Kumaşla Bütünleşen Metin */
        .user-text { position: absolute; top: 70%; left: 50%; transform: translateX(-50%); font-size: 16px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; cursor: grab; z-index: 15; white-space: nowrap; font-family: 'Montserrat', sans-serif; text-shadow: 0px 2px 5px rgba(0,0,0,0.4); }
        .user-text:active { cursor: grabbing; }

        /* Kontrol Paneli Şıklığı */
        .controls-box { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .studio-header { font-size: 22px; font-weight: 900; letter-spacing: 2px; margin: 0 0 30px 0; text-transform: uppercase; color: var(--accent-color); display: flex; align-items: center; gap: 10px; }
        
        .control-group { margin-bottom: 30px; background: rgba(136,136,136,0.05); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); }
        .section-title { font-size: 11px; font-weight: 800; letter-spacing: 2px; color: #888; margin-bottom: 15px; text-transform: uppercase; }
        
        /* Renk Butonları */
        .color-options { display: flex; gap: 15px; }
        .color-btn { width: 50px; height: 50px; border-radius: 50%; border: 3px solid transparent; cursor: pointer; transition: 0.3s; box-shadow: 0 5px 10px rgba(0,0,0,0.2); }
        .color-btn.active { border-color: var(--accent-color); transform: scale(1.1); box-shadow: 0 5px 15px rgba(255,42,42,0.4); }
        .color-btn.black { background: #1a1a1a; }
        .color-btn.white { background: #f0f0f0; }
        
        /* İnputlar */
        .control-group input[type="text"], .control-group select { width: 100%; padding: 15px; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 6px; font-family: 'Montserrat'; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box; transition: 0.3s; }
        .control-group input[type="text"]:focus { border-color: var(--accent-color); }
        
        /* Dosya Yükleme Butonu Özelleştirme */
        .file-upload-wrapper { position: relative; width: 100%; }
        .file-upload-input { width: 100%; padding: 12px; background: var(--bg-color); color: var(--text-color); border: 1px dashed #666; border-radius: 6px; cursor: pointer; font-family: 'Montserrat'; font-size: 12px; font-weight: 600; box-sizing: border-box; transition: 0.3s; }
        .file-upload-input:hover { border-color: var(--accent-color); }
        
        /* Kaydırıcılar (Range) */
        .slider-box { display: flex; align-items: center; gap: 15px; margin-top: 15px; }
        .slider-box label { font-size: 10px; font-weight: 800; color: #888; letter-spacing: 1px; width: 80px; }
        input[type="range"] { flex: 1; cursor: pointer; accent-color: var(--accent-color); }
        
        .btn-checkout { width: 100%; padding: 20px; background: var(--text-color); color: var(--bg-color); border: none; border-radius: 8px; font-family: 'Montserrat'; font-size: 14px; font-weight: 900; letter-spacing: 2px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 10px; }
        .btn-checkout:hover { background: var(--accent-color); color: #fff; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(255, 42, 42, 0.3); }
    </style>
</head>
<body>

    <!-- ÜST MENÜ -->
    <div class="header" id="navbar">
        <h2 onclick="window.location='index.php'">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="kendi_tasarla.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="kendi_tasarla.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            <span style="color:#333; margin:0 5px;">|</span>
            <a href="index.php"><i class="fa-solid fa-store"></i></a>
            <a href="kendi_tasarla.php" class="active" title="<?php echo $page_title; ?>"><i class="fa-solid fa-palette"></i></a>
            <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="admin.php" title="Yönetici Paneli"><i class="fa-solid fa-chart-pie"></i></a>
            <?php endif; ?>
            <a href="iletisim.php" title="<?php echo $i18n[$secili_dil]['iletisim']; ?>"><i class="fa-solid fa-envelope"></i></a>
            <a href="login.php?cikis=1" title="<?php echo $i18n[$secili_dil]['cikis']; ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <span style="color:#333; margin:0 5px;">|</span>
            <button class="menu-btn" onclick="toggleMenu()"><i class="fa-solid fa-bars-staggered"></i></button>
        </div>
    </div>

    <!-- YAN MENÜ -->
    <div class="sidebar-overlay" id="menuOverlay" onclick="toggleMenu()"></div>
    <div class="sidebar" id="sideMenu">
        <button class="close-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></button>
        <div class="sidebar-links">
            <a href="index.php"><i class="fa-solid fa-house"></i> <?php echo $secili_dil == 'tr' ? 'ANA SAYFA' : 'HOME'; ?></a>
            <a href="ai_tasarim.php"><i class="fa-solid fa-wand-magic-sparkles"></i> AI TASARIM</a>
            <a href="kendi_tasarla.php" style="color:var(--accent-color);"><i class="fa-solid fa-palette"></i> <?php echo $secili_dil == 'tr' ? 'KENDİ TASARLA' : 'DESIGN YOUR OWN'; ?></a>
            <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="admin.php"><i class="fa-solid fa-chart-pie"></i> <?php echo $secili_dil == 'tr' ? 'YÖNETİCİ' : 'ADMIN'; ?></a>
            <?php endif; ?>
            <a href="iletisim.php"><i class="fa-solid fa-envelope"></i> <?php echo $i18n[$secili_dil]['iletisim']; ?></a>
            <a href="login.php?cikis=1"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?php echo $i18n[$secili_dil]['cikis']; ?></a>
        </div>
        <div class="sidebar-bottom">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnSide" style="margin-bottom:10px;">
                <i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>
            </button>
            <div class="lang-switch">
                <a href="kendi_tasarla.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="kendi_tasarla.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="designer-grid">
        
        <!-- ÖNİZLEME PANELİ (GERÇEK MANKENLER) -->
        <div class="preview-box">
            <div class="tshirt-wrapper" id="tshirtWrapper">
                <!-- Arka Plan Manken Görseli -->
                <img src="" id="tshirtImg" class="tshirt-image" alt="Model">
                
                <!-- Sürüklenebilir Kumaş Alanı -->
                <div class="printable-area" id="printableArea">
                    
                    <!-- Kullanıcının Yüklediği Grafik -->
                    <div class="user-graphic" id="userGraphic" style="display: none;">
                        <img id="graphicImg" src="" alt="Design">
                    </div>
                    
                    <!-- HATA ÇÖZÜLDÜ: Kullanıcının Yazdığı Metin (Başlangıçta Boş) -->
                    <div class="user-text" id="userText" style="display: none;"></div>
                </div>
            </div>
        </div>
        
        <!-- KONTROL PANELİ -->
        <div class="controls-box">
            <h1 class="studio-header"><i class="fa-solid fa-object-group"></i> STUDIO</h1>
            
            <div class="control-group">
                <div class="section-title"><?php echo $txt_renk_sec; ?></div>
                <div class="color-options">
                    <button class="color-btn black active" onclick="changeColor('black')" title="Siyah Oversize"></button>
                    <button class="color-btn white" onclick="changeColor('white')" title="Beyaz Oversize"></button>
                </div>
            </div>

            <div class="control-group">
                <div class="section-title"><?php echo $txt_resim_yukle; ?></div>
                <div class="file-upload-wrapper">
                    <input type="file" class="file-upload-input" id="imageLoader" accept="image/*" onchange="loadUserImage(event)">
                </div>
                
                <div id="imageControls" style="display: none;">
                    <div class="slider-box">
                        <label><i class="fa-solid fa-expand"></i> <?php echo $txt_boyut; ?></label>
                        <input type="range" id="sizeRange" min="40" max="250" value="120" oninput="scaleImage(this.value)">
                    </div>
                </div>
            </div>

            <div class="control-group">
                <div class="section-title"><?php echo $txt_yazi_ekle; ?></div>
                <input type="text" id="textInput" placeholder="<?php echo $txt_yazi_placeholder; ?>" oninput="updateText(this.value)">
                
                <div id="textControls" style="display: none;">
                    <div class="slider-box">
                        <label><i class="fa-solid fa-droplet"></i> <?php echo $txt_yazi_rengi; ?></label>
                        <input type="color" id="textColor" value="#ffffff" oninput="changeTextColor(this.value)" style="border:none; background:none; cursor:pointer; width:40px; height:30px;">
                    </div>
                    <div class="slider-box">
                        <label><i class="fa-solid fa-text-height"></i> <?php echo $txt_boyut; ?></label>
                        <input type="range" min="12" max="40" value="16" oninput="scaleText(this.value)">
                    </div>
                </div>
            </div>

            <div class="control-group" style="margin-bottom: 15px;">
                <div class="section-title"><?php echo $txt_beden; ?></div>
                <select id="shirtSize">
                    <option value="S">S (Oversize Fit)</option>
                    <option value="M">M (Oversize Fit)</option>
                    <option value="L">L (Oversize Fit)</option>
                    <option value="XL">XL (Oversize Fit)</option>
                </select>
            </div>

            <button class="btn-checkout" onclick="gotoCheckout()">
                <i class="fa-solid fa-receipt"></i> <?php echo $txt_siparis_ver; ?>
            </button>
        </div>

    </div>

    <!-- SAĞ ALT YÜZEN İKONLAR -->
    <a href="kendi_tasarla.php" class="custom-float" title="<?php echo $page_title; ?>"><i class="fa-solid fa-palette"></i></a>
    <a href="ai_tasarim.php" class="ai-float" title="<?php echo $secili_dil == 'tr' ? 'AI Tasarım Robotu' : 'AI Design Bot'; ?>"><i class="fa-solid fa-wand-magic-sparkles"></i></a>
    <a href="https://wa.me/905555555555" target="_blank" class="whatsapp-float"><i class="fa-brands fa-whatsapp"></i></a>

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
        updateThemeIcon();
        function toggleTheme() {
            isLightMode = !isLightMode;
            localStorage.setItem('bazzkila_theme', isLightMode ? 'light' : 'dark');
            updateThemeIcon();
        }

        /* TİŞÖRT VE BLEND MODE SİHRİ */
        let tshirtColor = 'black';
        const tshirtImg = document.getElementById('tshirtImg');
        const graphicImg = document.getElementById('graphicImg');

        function adjustTshirtImg() {
            if (tshirtColor === 'black') {
                // Siyah Manken
                tshirtImg.src = 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=800&q=80';
                // Siyah kumaşa baskı efekti (Opaklığı hafif düşürerek kumaş dokusunu gösteririz)
                graphicImg.style.mixBlendMode = 'normal';
                graphicImg.style.opacity = '0.85'; 
            } else {
                // Beyaz Manken
                tshirtImg.src = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80';
                // Beyaz kumaşta 'multiply' efekti mucizesi: Baskı, kumaşın gölgeleri ve kırışıklıklarıyla kusursuz birleşir!
                graphicImg.style.mixBlendMode = 'multiply';
                graphicImg.style.opacity = '1';
            }
        }
        adjustTshirtImg(); // Sayfa açılışında mankeni yükle

        function changeColor(color) {
            tshirtColor = color;
            document.querySelectorAll('.color-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`.color-btn.${color}`).classList.add('active');
            adjustTshirtImg();
        }

        /* RESİM YÜKLEME VE BOYUTLANDIRMA */
        function loadUserImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                graphicImg.src = reader.result;
                document.getElementById('userGraphic').style.display = 'block';
                document.getElementById('imageControls').style.display = 'block';
                scaleImage(120);
            }
            if(event.target.files[0]){
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function scaleImage(val) {
            document.getElementById('userGraphic').style.width = val + 'px';
        }

        /* METİN EKLEME (HATA ÇÖZÜLDÜ: Başlangıçta gizli ve boş) */
        function updateText(val) {
            const textEl = document.getElementById('userText');
            if(val.trim() === '') {
                textEl.style.display = 'none';
                document.getElementById('textControls').style.display = 'none';
            } else {
                textEl.textContent = val.toUpperCase();
                textEl.style.display = 'block';
                document.getElementById('textControls').style.display = 'block';
            }
        }
        function changeTextColor(color) { document.getElementById('userText').style.color = color; }
        function scaleText(size) { document.getElementById('userText').style.fontSize = size + 'px'; }

        /* SÜRÜKLE BIRAK (DRAG & DROP) SİSTEMİ */
        function makeDraggable(element) {
            let active = false, currentX, currentY, initialX, initialY, xOffset = 0, yOffset = 0;

            element.addEventListener("touchstart", dragStart, {passive: false});
            element.addEventListener("touchend", dragEnd, false);
            element.addEventListener("touchmove", drag, {passive: false});
            element.addEventListener("mousedown", dragStart, false);
            window.addEventListener("mouseup", dragEnd, false);
            window.addEventListener("mousemove", drag, false);

            function dragStart(e) {
                if (e.type === "touchstart") {
                    initialX = e.touches[0].clientX - xOffset;
                    initialY = e.touches[0].clientY - yOffset;
                } else {
                    initialX = e.clientX - xOffset;
                    initialY = e.clientY - yOffset;
                }
                if (e.target === element || element.contains(e.target)) {
                    active = true;
                }
            }
            function dragEnd() {
                initialX = currentX; initialY = currentY; active = false;
            }
            function drag(e) {
                if (active) {
                    e.preventDefault();
                    if (e.type === "touchmove") {
                        currentX = e.touches[0].clientX - initialX;
                        currentY = e.touches[0].clientY - initialY;
                    } else {
                        currentX = e.clientX - initialX;
                        currentY = e.clientY - initialY;
                    }
                    xOffset = currentX; yOffset = currentY;
                    element.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
                }
            }
        }
        makeDraggable(document.getElementById("userGraphic"));
        makeDraggable(document.getElementById("userText"));

        /* FATURAYA GÖNDER */
        function gotoCheckout() {
            const yazi = document.getElementById('textInput').value.trim();
            const renk = tshirtColor === 'black' ? 'Siyah' : 'Beyaz';
            const beden = document.getElementById('shirtSize').value;
            const userGraphicDisp = document.getElementById('userGraphic').style.display;
            
            const yaziDurumu = yazi ? `(Metin: ${yazi.toUpperCase()})` : '';
            const grafikDurumu = userGraphicDisp === 'block' ? 'Baskılı' : 'Baskısız';
            
            const urun_adi = `Kişiye Özel Tasarım ${renk} Tişört ${yaziDurumu} [${grafikDurumu}]`;
            const fiyat = "1199.00"; 

            window.location.href = `fatura.php?urun_adi=${encodeURIComponent(urun_adi)}&fiyat=${fiyat}&renk=${encodeURIComponent(renk)}&beden=${encodeURIComponent(beden)}`;
        }
    </script>
</body>
</html>