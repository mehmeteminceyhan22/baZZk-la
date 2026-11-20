<?php
session_start();
require 'dil.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = $secili_dil == 'tr' ? 'Ana Sayfa' : 'Home';
$seo_desc = $secili_dil == 'tr' ? 'baZZkıla Premium Sokak Modası - Kendi lüksünü giy.' : 'baZZkıla Premium Streetwear - Wear your own luxury.';
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $seo_desc; ?>">
    <title>baZZkıla - <?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --bg-color: #050505; --text-color: #ffffff; --card-bg: transparent; 
            --accent-color: #ff2a2a; --border-color: #1a1a1a; --glass-bg: rgba(5, 5, 5, 0.75);
        }
        body.theme-light { 
            --bg-color: #f8f8f8; --text-color: #0a0a0a; --card-bg: transparent; 
            --accent-color: #d10000; --border-color: #e0e0e0; --glass-bg: rgba(248, 248, 248, 0.85);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-color); }

        html { scroll-behavior: smooth; }
        body { background-color: var(--bg-color); color: var(--text-color); font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; transition: background 0.5s ease, color 0.5s ease; overflow-x: hidden; }
        
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background: var(--glass-bg); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-color); position: fixed; width: 100%; top: 0; z-index: 1000; box-sizing: border-box; transition: padding 0.4s ease, background 0.4s ease; }
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

        .hero { position: relative; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color); }
        .hero-video-placeholder { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; transform: scale(1.05); animation: slowZoom 20s infinite alternate; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, var(--bg-color) 95%); z-index: -1; }
        .hero-content { text-align: center; z-index: 1; margin-top: -30px; }
        .hero-content h1 { font-size: 5rem; font-weight: 900; letter-spacing: 15px; margin: 0; text-transform: uppercase; text-shadow: 0px 5px 15px rgba(0,0,0,0.6); }
        .hero-content p { font-size: 1rem; font-weight: 400; letter-spacing: 8px; margin-top: 20px; color: #aaa; text-transform: uppercase; }
        @keyframes slowZoom { from { transform: scale(1); } to { transform: scale(1.1); } }
        
        .scroll-down { position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%); font-size: 30px; color: rgba(255,255,255,0.5); text-decoration: none; animation: floatArrow 2s infinite ease-in-out; z-index: 10; transition: 0.3s; }
        .scroll-down:hover { color: var(--accent-color); }
        @keyframes floatArrow { 0%, 100% { transform: translateY(0) translateX(-50%); } 50% { transform: translateY(15px) translateX(-50%); } }

        /* YÜZEN BUTONLAR */
        .whatsapp-float { position: fixed; bottom: 30px; right: 30px; background-color: #25D366; color: white; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 26px; box-shadow: 0 10px 20px rgba(0,0,0,0.4); z-index: 100; transition: 0.3s; text-decoration: none; }
        .whatsapp-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(37, 211, 102, 0.4); }

        .ai-float { position: fixed; bottom: 100px; right: 30px; background: linear-gradient(135deg, #ff4444, #8a2be2); color: white; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 22px; box-shadow: 0 10px 20px rgba(255,68,68,0.3); z-index: 100; transition: 0.3s; text-decoration: none; }
        .ai-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(138,43,226,0.5); }

        /* YENİ KENDİ TASARLA BUTONU */
        .custom-float { position: fixed; bottom: 170px; right: 30px; background: linear-gradient(135deg, #ff9100, #ffea00); color: #000; border-radius: 50%; width: 55px; height: 55px; display: flex; justify-content: center; align-items: center; font-size: 22px; box-shadow: 0 10px 20px rgba(255,145,0,0.3); z-index: 100; transition: 0.3s; text-decoration: none; }
        .custom-float:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(255,145,0,0.5); }

        .shop-section { max-width: 1400px; margin: 80px auto 100px auto; padding: 0 50px; }
        .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; }
        .filter-group { display: flex; gap: 35px; }
        .filter-btn { background: transparent; color: #888; border: none; font-family: 'Montserrat', sans-serif; cursor: pointer; font-weight: 500; font-size: 11px; letter-spacing: 3px; text-transform: uppercase; transition: 0.4s; padding-bottom: 8px; position: relative; }
        .filter-btn::after { content: ''; position: absolute; width: 0; height: 1px; bottom: 0; left: 50%; transform: translateX(-50%); background-color: var(--text-color); transition: width 0.4s ease; }
        .filter-btn:hover, .filter-btn.active { color: var(--text-color); font-weight: 700; }
        .filter-btn:hover::after, .filter-btn.active::after { width: 100%; }
        select { background: transparent; color: var(--text-color); border: none; border-bottom: 1px solid #444; padding: 8px 5px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; outline: none; transition: 0.3s; }
        
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 50px 30px; }
        @media (max-width: 1200px) { .grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; } .header-nav { display: none; } .menu-btn { display: block; } .hero-content h1 { font-size: 3rem; letter-spacing: 8px; } .filter-bar { flex-direction: column; gap: 30px; align-items: flex-start; } .sidebar { width: 100%; right: -100%; } }

        .card { position: relative; cursor: pointer; }
        .img-container { overflow: hidden; position: relative; aspect-ratio: 3/4; background: #0a0a0a; border-radius: 4px; }
        .card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94), filter 0.5s; opacity: 0.95; }
        .card:hover img { transform: scale(1.05); filter: brightness(0.7); }
        .badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 9px; padding: 6px 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; z-index: 10; border-radius: 30px; }
        .quick-view { position: absolute; bottom: -60px; left: 0; width: 100%; padding: 18px 0; background: var(--accent-color); color: #fff; text-align: center; font-size: 11px; font-weight: 800; letter-spacing: 4px; transition: 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); display: flex; justify-content: center; align-items: center; gap: 10px; z-index: 11; }
        .card:hover .quick-view { bottom: 0; }
        .card-link { text-decoration: none; color: inherit; display: block; height: 100%; outline: none; }
        .card-info { padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-start; }
        .info-left h4 { margin: 0 0 6px 0; font-size: 12px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; transition: 0.3s; }
        .card:hover .info-left h4 { color: var(--accent-color); }
        .info-left p { margin: 0; color: #666; font-size: 10px; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }
        .info-right .price { font-size: 13px; font-weight: 700; letter-spacing: 1px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

        .footer { background: #050505; color: #666; padding: 80px 50px 40px 50px; border-top: 1px solid var(--border-color); }
        .footer-grid { display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 50px; max-width: 1400px; margin: 0 auto; }
        .footer h3 { color: #fff; font-size: 13px; letter-spacing: 4px; margin-bottom: 30px; text-transform: uppercase; font-weight: 800; }
        .footer p { font-size: 12px; line-height: 2; letter-spacing: 1px; }
        .map-container { width: 100%; height: 180px; border-radius: 4px; overflow: hidden; border: 1px solid #222; filter: grayscale(100%) contrast(1.2); transition: 0.5s; }
        .map-container:hover { filter: grayscale(0%); }
        .footer-bottom { text-align: center; margin-top: 80px; font-size: 10px; letter-spacing: 3px; border-top: 1px solid #111; padding-top: 30px; font-weight: 600; text-transform: uppercase; }
        @media (max-width: 900px) { .footer-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="header" id="navbar">
        <h2 onclick="window.scrollTo(0,0)">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="index.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="index.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            <span style="color:#333; margin:0 5px;">|</span>
            
            <!-- ÜST MENÜYE KENDİ TASARLA EKLENDİ -->
            <a href="kendi_tasarla.php" title="<?php echo $secili_dil == 'tr' ? 'Kendi Tişörtünü Tasarla' : 'Design Your Own'; ?>"><i class="fa-solid fa-palette"></i></a>
            
            <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="admin.php" title="Yönetici Paneli"><i class="fa-solid fa-chart-pie"></i></a>
            <?php endif; ?>
            <a href="iletisim.php" title="<?php echo $i18n[$secili_dil]['iletisim']; ?>"><i class="fa-solid fa-envelope"></i></a>
            <a href="login.php?cikis=1" title="<?php echo $i18n[$secili_dil]['cikis']; ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <span style="color:#333; margin:0 5px;">|</span>
            <button class="menu-btn" onclick="toggleMenu()"><i class="fa-solid fa-bars-staggered"></i></button>
        </div>
    </div>

    <div class="sidebar-overlay" id="menuOverlay" onclick="toggleMenu()"></div>
    <div class="sidebar" id="sideMenu">
        <button class="close-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></button>
        
        <div class="sidebar-links">
            <a href="index.php" style="color:var(--accent-color);"><i class="fa-solid fa-house"></i> <?php echo $secili_dil == 'tr' ? 'ANA SAYFA' : 'HOME'; ?></a>
            
            <!-- YAN MENÜYE KENDİ TASARLA EKLENDİ -->
            <a href="kendi_tasarla.php"><i class="fa-solid fa-palette"></i> <?php echo $secili_dil == 'tr' ? 'KENDİ TASARLA' : 'DESIGN YOUR OWN'; ?></a>
            
            <a href="ai_tasarim.php"><i class="fa-solid fa-wand-magic-sparkles"></i> AI TASARIM</a>
            <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="admin.php"><i class="fa-solid fa-chart-pie"></i> <?php echo $secili_dil == 'tr' ? 'YÖNETİCİ' : 'ADMIN'; ?></a>
                <a href="add_product.php"><i class="fa-solid fa-plus"></i> <?php echo $secili_dil == 'tr' ? 'ÜRÜN EKLE' : 'ADD PRODUCT'; ?></a>
            <?php endif; ?>
            <a href="iletisim.php"><i class="fa-solid fa-envelope"></i> <?php echo $i18n[$secili_dil]['iletisim']; ?></a>
            <a href="login.php?cikis=1"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?php echo $i18n[$secili_dil]['cikis']; ?></a>
        </div>

        <div class="sidebar-bottom">
            <div style="color:#888; font-size:11px; font-weight:800; letter-spacing:2px; margin-bottom:10px;">PREFERENCES</div>
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnSide" style="margin-bottom:10px;">
                <i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>
            </button>
            <div class="lang-switch">
                <a href="index.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="index.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="hero">
        <!-- ONAYLADIĞIN YENİ GÖRSEL BURADA -->
        <img src="resimler/ana.png" alt="Streetwear Collection" class="hero-video-placeholder">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?php echo $i18n[$secili_dil]['slogan']; ?></h1>
            <p>2026/27 EXCLUSIVE COLLECTION</p>
        </div>
        <a href="#shop" class="scroll-down"><i class="fa-solid fa-arrow-down-long"></i></a>
    </div>

    <div class="shop-section" id="shop">
        <div class="filter-bar">
            <div class="filter-group">
                <button class="filter-btn active" onclick="setKategori('hepsi', this)"><?php echo $i18n[$secili_dil]['filtre_hepsi']; ?></button>
                <button class="filter-btn" onclick="setKategori('Kişiye Özel', this)"><?php echo $i18n[$secili_dil]['filtre_kisiye']; ?></button>
                <button class="filter-btn" onclick="setKategori('Nakış', this)"><?php echo $i18n[$secili_dil]['filtre_nakis']; ?></button>
                <button class="filter-btn" onclick="setKategori('Hazır Baskı', this)"><?php echo $i18n[$secili_dil]['filtre_hazir']; ?></button>
            </div>
            <div>
                <select id="siralama" onchange="setSiralama(this.value)">
                    <option value="yeni"><?php echo $i18n[$secili_dil]['sira_yeni']; ?></option>
                    <option value="ucuz"><?php echo $i18n[$secili_dil]['sira_ucuz']; ?></option>
                    <option value="pahali"><?php echo $i18n[$secili_dil]['sira_pahali']; ?></option>
                </select>
            </div>
        </div>

        <div class="grid" id="product-grid"></div>
    </div>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>baZZkıla</h3>
                <p><?php echo $secili_dil == 'tr' ? 'Sokak modasının yeni kuralları. Premium kumaş, eşsiz yapay zeka tasarımları.' : 'New rules of streetwear. Premium fabric, unique AI designs.'; ?></p>
            </div>
            <div>
                <h3><?php echo $i18n[$secili_dil]['iletisim']; ?></h3>
                <p>hello@bazzkila.com</p>
                <p>+90 555 555 55 55</p>
                <p>Gönen, Balıkesir / TR</p>
            </div>
            <div>
                <h3><?php echo $secili_dil == 'tr' ? 'MAĞAZAMIZ (Harita)' : 'OUR STORE (Map)'; ?></h3>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3032.5!2d27.8!3d40.1!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA2JzAwLjAiTiAyN8KwNDgnMDAuMCJF!5e0!3m2!1str!2str!4v1600000000000!5m2!1str!2str" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 baZZkıla. <?php echo $secili_dil == 'tr' ? 'Tüm hakları saklıdır.' : 'All rights reserved.'; ?> (Designed by Mehmet Emin Ceyhan)
        </div>
    </footer>

    <!-- SAĞ ALTTAKİ SÜZÜLEN BUTONLAR (YENİ KENDİ TASARLA BUTONU EKLENDİ) -->
    <a href="kendi_tasarla.php" class="custom-float" title="<?php echo $secili_dil == 'tr' ? 'Kendi Tişörtünü Tasarla' : 'Design Your Own T-Shirt'; ?>">
        <i class="fa-solid fa-palette"></i>
    </a>
    
    <a href="ai_tasarim.php" class="ai-float" title="<?php echo $secili_dil == 'tr' ? 'AI Tasarım Robotu' : 'AI Design Bot'; ?>">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
    </a>

    <a href="https://wa.me/905555555555?text=<?php echo urlencode($secili_dil == 'tr' ? 'Merhaba, baZZkıla tasarımları hakkında bilgi almak istiyorum.' : 'Hello, I want to get information about baZZkıla designs.'); ?>" target="_blank" class="whatsapp-float" title="WhatsApp Destek">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <script>
        // MENÜ KONTROLÜ
        function toggleMenu() {
            document.getElementById('sideMenu').classList.toggle('active');
            document.getElementById('menuOverlay').classList.toggle('active');
        }

        // TEMA KONTROLÜ
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

        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if(window.scrollY > 80) { nav.style.background = 'var(--bg-color)'; nav.style.padding = '15px 50px'; } 
            else { nav.style.background = 'var(--glass-bg)'; nav.style.padding = '20px 50px'; }
        });

        // ÜRÜNLERİ ÇEKME
        let currentKategori = 'hepsi';
        let currentSiralama = 'yeni';
        const seciliDil = '<?php echo $secili_dil; ?>';
        const txtBulunamadi = '<?php echo $i18n[$secili_dil]["urun_bulunamadi"]; ?>';
        const txtKesfet = '<?php echo $secili_dil == 'tr' ? 'KEŞFET' : 'DISCOVER'; ?>';

        function setKategori(kat, btnElement) {
            currentKategori = kat;
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');
            loadProducts();
        }

        function setSiralama(sort) { currentSiralama = sort; loadProducts(); }

        function loadProducts() {
            const grid = document.getElementById('product-grid');
            grid.innerHTML = '<div style="grid-column:1/-1; height:300px; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:#444;"></i></div>';
            
            fetch(`api_products.php?kategori=${currentKategori}&siralama=${currentSiralama}`)
                .then(res => res.json())
                .then(data => {
                    grid.innerHTML = '';
                    if(data.length === 0) {
                        grid.innerHTML = `<p style="grid-column:1/-1; text-align:center; color:#555; font-weight:800; letter-spacing:3px; font-size:16px; margin:50px 0; text-transform:uppercase;">${txtBulunamadi}</p>`;
                        return;
                    }
                    data.forEach((urun, index) => {
                        let hamKategori = urun.kategori;
                        if(seciliDil === 'en') {
                            if(hamKategori === 'Kişiye Özel') hamKategori = 'CUSTOM';
                            if(hamKategori === 'Nakış') hamKategori = 'EMBROIDERY';
                            if(hamKategori === 'Hazır Baskı') hamKategori = 'PRINTED';
                        }
                        let etiket = hamKategori.toUpperCase();
                        
                        grid.innerHTML += `
                            <div class="card" style="animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: ${index * 0.1}s;">
                                <a href="urun.php?id=${urun.id}" class="card-link">
                                    <div class="img-container">
                                        <div class="badge">${etiket}</div>
                                        <img src="${urun.resim_yolu}" alt="${urun.urun_adi}" onerror="this.src='https://via.placeholder.com/800x1200.png?text=baZZkila'">
                                        <div class="quick-view">
                                            <span>${txtKesfet}</span> <i class="fa-solid fa-arrow-right-long"></i>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <div class="info-left">
                                            <h4>${urun.urun_adi}</h4>
                                            <p>${etiket} SERIES</p>
                                        </div>
                                        <div class="info-right">
                                            <span class="price">${parseFloat(urun.fiyat).toLocaleString('tr-TR')} ₺</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        `;
                    });
                })
                .catch(err => console.error("API Bağlantı Hatası:", err));
        }
        
        loadProducts();
    </script>
</body>
</html>