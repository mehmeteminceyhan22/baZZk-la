<?php
session_start();
require 'db.php';
require 'dil.php';

// Güvenlik: Sadece 'admin' rolüne sahip olanlar girebilir
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

// İstatistikleri Veritabanından Çek
$stmtUsers = $db->query("SELECT COUNT(*) FROM users");
$toplamKullanici = $stmtUsers->fetchColumn();

$stmtProducts = $db->query("SELECT COUNT(*) FROM products");
$toplamUrun = $stmtProducts->fetchColumn();

// Chart.js Grafiği için Kategori Dağılımını Çek
$stmtCats = $db->query("SELECT kategori, COUNT(*) as sayi FROM products GROUP BY kategori");
$kategoriVerileri = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];
foreach($kategoriVerileri as $row) {
    $etiket = $row['kategori'];
    if($secili_dil == 'en') {
        if($etiket == 'Hazır Baskı') $etiket = 'Printed';
        if($etiket == 'Nakış') $etiket = 'Embroidery';
        if($etiket == 'Kişiye Özel') $etiket = 'Custom';
    }
    $labels[] = $etiket;
    $data[] = $row['sayi'];
}

// Çeviri Değişkenleri
$page_title = $secili_dil == 'tr' ? 'Yönetici Paneli' : 'Admin Dashboard';
$txt_hosgeldin = $secili_dil == 'tr' ? 'YÖNETİM PANELİNE HOŞ GELDİNİZ,' : 'WELCOME TO THE DASHBOARD,';
$txt_toplam_kul = $secili_dil == 'tr' ? 'TOPLAM KULLANICI' : 'TOTAL USERS';
$txt_toplam_urn = $secili_dil == 'tr' ? 'TOPLAM ÜRÜN' : 'TOTAL PRODUCTS';
$txt_grafik = $secili_dil == 'tr' ? 'KATEGORİ DAĞILIMI' : 'CATEGORY DISTRIBUTION';
$txt_hizli_islem = $secili_dil == 'tr' ? 'HIZLI İŞLEMLER' : 'QUICK ACTIONS';
$txt_urun_ekle = $secili_dil == 'tr' ? 'YENİ ÜRÜN EKLE' : 'ADD NEW PRODUCT';
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
        :root { 
            --bg-color: #050505; --text-color: #ffffff; --card-bg: #111; 
            --accent-color: #ff2a2a; --border-color: #222; --glass-bg: rgba(5, 5, 5, 0.85);
        }
        body.theme-light { 
            --bg-color: #f8f8f8; --text-color: #0a0a0a; --card-bg: #fff; 
            --accent-color: #d10000; --border-color: #e0e0e0; --glass-bg: rgba(248, 248, 248, 0.9);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }

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

        .admin-container { max-width: 1200px; margin: 120px auto 50px auto; padding: 0 40px; }
        .admin-header { margin-bottom: 40px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; }
        .admin-header h1 { font-size: 24px; font-weight: 800; letter-spacing: 4px; margin: 0; text-transform: uppercase; color: var(--accent-color); }
        .admin-header p { font-size: 12px; font-weight: 600; letter-spacing: 2px; color: #888; margin-top: 10px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 40px; }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: 1fr; } }
        
        .stat-card { background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.2); cursor: default; }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--accent-color); box-shadow: 0 15px 30px rgba(255, 42, 42, 0.15); }
        .stat-info h4 { margin: 0; font-size: 11px; font-weight: 700; color: #888; letter-spacing: 2px; }
        .stat-info h2 { margin: 10px 0 0 0; font-size: 36px; font-weight: 900; }
        .stat-icon { width: 60px; height: 60px; background: rgba(255, 42, 42, 0.1); color: var(--accent-color); border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 24px; }

        .dashboard-content { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 900px) { .dashboard-content { grid-template-columns: 1fr; } }

        .chart-box, .action-box { background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .box-title { font-size: 14px; font-weight: 800; letter-spacing: 2px; margin: 0 0 25px 0; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }

        .action-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 15px; background: transparent; color: var(--text-color); border: 1px solid var(--border-color); border-radius: 8px; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-decoration: none; transition: 0.3s; margin-bottom: 15px; box-sizing: border-box; }
        .action-btn:hover { background: var(--text-color); color: var(--bg-color); border-color: var(--text-color); }
        .action-btn.primary:hover { background: var(--accent-color); color: #fff; border-color: var(--accent-color); }
        
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="header" id="navbar">
        <h2 onclick="window.location='index.php'">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="admin.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="admin.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
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
            <a href="kendin_tasarla.php"><i class="fa-solid fa-wand-magic"></i> <?php echo $secili_dil == 'tr' ? 'KENDİ TASARLA' : 'DESIGN YOUR OWN'; ?></a>
            <a href="index.php"><i class="fa-solid fa-house"></i> <?php echo $secili_dil == 'tr' ? 'ANA SAYFA' : 'HOME'; ?></a>
            <a href="ai_tasarim.php"><i class="fa-solid fa-wand-magic-sparkles"></i> AI TASARIM</a>
            <a href="admin.php" style="color:var(--accent-color);"><i class="fa-solid fa-chart-pie"></i> <?php echo $page_title; ?></a>
            <a href="add_product.php"><i class="fa-solid fa-plus"></i> <?php echo $txt_urun_ekle; ?></a>
            <a href="login.php?cikis=1"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?php echo $i18n[$secili_dil]['cikis']; ?></a>
        </div>

        <div class="sidebar-bottom">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnSide" style="margin-bottom:10px;">
                <i class="fa-solid fa-moon"></i> <?php echo $secili_dil == 'tr' ? 'Karanlık Tema' : 'Dark Mode'; ?>
            </button>
            <div class="lang-switch">
                <a href="admin.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="admin.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-header">
            <h1><?php echo $page_title; ?></h1>
            <p><?php echo $txt_hosgeldin; ?> <?php echo strtoupper($_SESSION['ad_soyad']); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h4><?php echo $txt_toplam_kul; ?></h4>
                    <h2><?php echo $toplamKullanici; ?></h2>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h4><?php echo $txt_toplam_urn; ?></h4>
                    <h2><?php echo $toplamUrun; ?></h2>
                </div>
                <div class="stat-icon" style="color:#00c853; background:rgba(0, 200, 83, 0.1);"><i class="fa-solid fa-tags"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h4><?php echo $secili_dil == 'tr' ? 'SİSTEM DURUMU' : 'SYSTEM STATUS'; ?></h4>
                    <h2 style="font-size:24px; color:#00e5ff;">ONLINE</h2>
                </div>
                <div class="stat-icon" style="color:#00e5ff; background:rgba(0, 229, 255, 0.1);"><i class="fa-solid fa-server"></i></div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="chart-box">
                <h3 class="box-title"><i class="fa-solid fa-chart-bar"></i> <?php echo $txt_grafik; ?></h3>
                <canvas id="categoryChart" height="120"></canvas>
            </div>

            <div class="action-box">
                <h3 class="box-title"><i class="fa-solid fa-bolt"></i> <?php echo $txt_hizli_islem; ?></h3>
                <a href="add_product.php" class="action-btn primary"><i class="fa-solid fa-plus"></i> <?php echo $txt_urun_ekle; ?></a>
                <a href="index.php" class="action-btn"><i class="fa-solid fa-store"></i> <?php echo $txt_siteye_git; ?></a>
            </div>
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
                Chart.defaults.color = '#050505'; 
                updateChartBorder('#fff');
            } else { 
                document.body.classList.remove('theme-light'); 
                if(themeBtnSide) themeBtnSide.innerHTML = '<i class="fa-solid fa-sun"></i> <?php echo $secili_dil == 'tr' ? 'Aydınlık Tema' : 'Light Mode'; ?>'; 
                if(themeBtnTop) themeBtnTop.innerHTML = '<i class="fa-solid fa-sun"></i>';
                Chart.defaults.color = '#fff';
                updateChartBorder('#111');
            }
        }

        function toggleTheme() {
            isLightMode = !isLightMode;
            localStorage.setItem('bazzkila_theme', isLightMode ? 'light' : 'dark');
            updateThemeIcon();
            catChart.update(); 
        }

        const ctx = document.getElementById('categoryChart').getContext('2d');
        const jsLabels = <?php echo json_encode($labels); ?>;
        const jsData = <?php echo json_encode($data); ?>;
        
        let catChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: jsLabels,
                datasets: [{
                    label: '<?php echo $txt_toplam_urn; ?>',
                    data: jsData,
                    backgroundColor: [
                        'rgba(255, 42, 42, 0.8)',
                        'rgba(138, 43, 226, 0.8)',
                        'rgba(0, 200, 83, 0.8)'
                    ],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(136, 136, 136, 0.2)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        function updateChartBorder(color) {
            catChart.options.scales.y.grid.color = (color === '#111') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
        }

        updateThemeIcon();
    </script>
</body>
</html>