<?php
session_start();
require 'dil.php';

$mesaj_durum = "";

// Email Gönderimi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = htmlspecialchars($_POST['ad']);
    $email = htmlspecialchars($_POST['email']);
    $konu = htmlspecialchars($_POST['konu']);
    $icerik = htmlspecialchars($_POST['mesaj']);

    $alici = "iletisim@bazzkila.com";
    $basliklar = "From: " . $email . "\r\n" . "Reply-To: " . $email;

    if (@mail($alici, $konu, $icerik, $basliklar)) {
        $mesaj_durum = $secili_dil == 'tr' ? "Mesajınız başarıyla gönderildi!" : "Your message has been successfully sent!";
    } else {
        $mesaj_durum = $secili_dil == 'tr' ? "Mesaj alındı. (Sunucu mail özelliği aktif değil)" : "Message received. (Mail server is not active)";
    }
}

$page_title = $secili_dil == 'tr' ? 'İletişim' : 'Contact Us';
$txt_bize_ulasin = $secili_dil == 'tr' ? 'BİZE ULAŞIN' : 'CONTACT US';
$txt_alt_baslik = $secili_dil == 'tr' ? 'Gönen Merkez Atölyemizden tüm Türkiye\'ye kargo!' : 'Worldwide shipping from our Gönen Workshop!';
$txt_ad = $secili_dil == 'tr' ? 'Adınız Soyadınız' : 'Full Name';
$txt_konu = $secili_dil == 'tr' ? 'Konu' : 'Subject';
$txt_mesaj = $secili_dil == 'tr' ? 'Mesajınız...' : 'Your Message...';
$txt_gonder = $secili_dil == 'tr' ? 'E-POSTA GÖNDER' : 'SEND EMAIL';
$txt_wp = $secili_dil == 'tr' ? 'WhatsApp\'tan Hızlı İletişim' : 'Quick Contact via WhatsApp';
$txt_wp_mesaj = $secili_dil == 'tr' ? 'Merhaba baZZkıla, özel tasarımlar hakkında bilgi almak istiyorum.' : 'Hello baZZkıla, I would like to get info about custom designs.';
$txt_harita = $secili_dil == 'tr' ? 'Konumumuz (Gönen / Balıkesir)' : 'Our Location (Gonen / Balikesir)';
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

        .contact-container { max-width: 800px; margin: 120px auto 50px auto; padding: 40px; background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center; }
        .contact-container h2 { font-size: 24px; font-weight: 900; letter-spacing: 4px; margin: 0 0 10px 0; color: var(--accent-color); }
        .contact-container p { font-size: 12px; color: #888; letter-spacing: 2px; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group input, .form-group textarea { width: 100%; padding: 15px; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 500; outline: none; transition: 0.3s; box-sizing: border-box; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--accent-color); box-shadow: 0 0 10px rgba(255, 42, 42, 0.1); }
        
        .btn-submit { width: 100%; padding: 18px; background: var(--text-color); color: var(--bg-color); border: none; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 3px; cursor: pointer; transition: 0.3s; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-submit:hover { background: var(--accent-color); color: #fff; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 42, 42, 0.2); }

        .wa-btn { background: #25D366; color: white; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 18px; border-radius: 6px; text-decoration: none; margin-top: 20px; font-weight: 800; letter-spacing: 2px; width: 100%; box-sizing: border-box; font-size: 13px; transition: 0.3s; }
        .wa-btn:hover { background: #1ebe56; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3); }

        .alert { padding: 15px; background: rgba(0, 200, 83, 0.1); color: #00c853; border: 1px solid #00c853; border-radius: 6px; margin-bottom: 25px; font-size: 12px; font-weight: 700; letter-spacing: 1px; }
        
        .map-section { margin-top: 50px; text-align: left; }
        .map-section h3 { font-size: 14px; font-weight: 800; letter-spacing: 2px; color: var(--text-color); margin-bottom: 15px; text-transform: uppercase; }
        .map-section iframe { width: 100%; height: 350px; border: 1px solid var(--border-color); border-radius: 8px; filter: grayscale(100%) contrast(1.2); transition: 0.5s; }
        .map-section iframe:hover { filter: grayscale(0%); }
    </style>
</head>
<body>

    <div class="header" id="navbar">
        <h2 onclick="window.location='index.php'">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="iletisim.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="iletisim.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            <span style="color:#333; margin:0 5px;">|</span>
            <a href="index.php"><i class="fa-solid fa-store"></i></a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="login.php?cikis=1" title="<?php echo $i18n[$secili_dil]['cikis']; ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <?php endif; ?>
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
            <?php if (isset($_SESSION['user_id']) && $_SESSION['rol'] == 'admin'): ?>
                <a href="admin.php"><i class="fa-solid fa-chart-pie"></i> <?php echo $secili_dil == 'tr' ? 'YÖNETİCİ' : 'ADMIN'; ?></a>
            <?php endif; ?>
            <a href="iletisim.php" style="color:var(--accent-color);"><i class="fa-solid fa-envelope"></i> <?php echo $page_title; ?></a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="login.php?cikis=1"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?php echo $i18n[$secili_dil]['cikis']; ?></a>
            <?php endif; ?>
        </div>

        <div class="sidebar-bottom">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnSide" style="margin-bottom:10px;">
                <i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>
            </button>
            <div class="lang-switch">
                <a href="iletisim.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="iletisim.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="contact-container">
        <h2><?php echo $txt_bize_ulasin; ?></h2>
        <p><?php echo $txt_alt_baslik; ?></p>
        
        <?php if($mesaj_durum): ?>
            <div class="alert"><i class="fa-solid fa-check"></i> <?php echo $mesaj_durum; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group"><input type="text" name="ad" placeholder="<?php echo $txt_ad; ?>" required></div>
            <div class="form-group"><input type="email" name="email" placeholder="Email (ornek@email.com)" required></div>
            <div class="form-group"><input type="text" name="konu" placeholder="<?php echo $txt_konu; ?>" required></div>
            <div class="form-group"><textarea name="mesaj" rows="5" placeholder="<?php echo $txt_mesaj; ?>" required></textarea></div>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> <?php echo $txt_gonder; ?></button>
        </form>

        <a href="https://wa.me/905555555555?text=<?php echo urlencode($txt_wp_mesaj); ?>" target="_blank" class="wa-btn">
            <i class="fa-brands fa-whatsapp" style="font-size:20px;"></i> <?php echo $txt_wp; ?>
        </a>

        <div class="map-section">
            <h3><?php echo $txt_harita; ?></h3>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3032.5!2d27.8!3d40.1!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA2JzAwLjAiTiAyN8KwNDgnMDAuMCJF!5e0!3m2!1str!2str!4v1600000000000!5m2!1str!2str" allowfullscreen="" loading="lazy"></iframe>
        </div>
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
        updateThemeIcon();

        function toggleTheme() {
            isLightMode = !isLightMode;
            localStorage.setItem('bazzkila_theme', isLightMode ? 'light' : 'dark');
            updateThemeIcon();
        }
    </script>
</body>
</html>