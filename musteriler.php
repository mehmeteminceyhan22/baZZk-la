<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
$musteriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>baZZkıla - Müşteriler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #050505; color: #fff; font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111; padding: 30px 20px; border-right: 1px solid #222; display: flex; flex-direction: column; }
        .sidebar h2 { color: #ff4444; letter-spacing: 4px; text-transform: uppercase; font-size: 24px; margin-top: 0; margin-bottom: 40px; text-align: center; }
        .sidebar a { display: block; color: #888; text-decoration: none; padding: 15px 20px; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s; font-size: 13px; font-weight: bold; letter-spacing: 1px; display: flex; align-items: center; gap: 15px; }
        .sidebar a:hover, .sidebar a.active { background: #1a1a1a; color: #fff; border-left: 4px solid #ff4444; }
        .content { flex: 1; padding: 40px; }
        .header { border-bottom: 1px solid #222; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; letter-spacing: 2px; }
        .table-container { background: #111; border-radius: 12px; border: 1px solid #222; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #1a1a1a; color: #888; padding: 15px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #333; }
        td { padding: 15px 20px; border-bottom: 1px solid #222; font-size: 13px; color: #ccc; }
        tr:hover td { background: #151515; color: #fff; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .badge.admin { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }
        .badge.user { background: rgba(76, 175, 80, 0.1); color: #4CAF50; border: 1px solid #4CAF50; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>baZZkıla</h2>
        <a href="admin.php"><i class="fa-solid fa-chart-pie"></i> ÖZET (DASHBOARD)</a>
        <a href="add_product.php"><i class="fa-solid fa-box-open"></i> ÜRÜN YÖNETİMİ</a>
        <a href="musteriler.php" class="active"><i class="fa-solid fa-users"></i> MÜŞTERİLER</a>
        <div style="margin-top: auto;">
            <a href="index.php" style="border-left: none;"><i class="fa-solid fa-arrow-left"></i> MAĞAZAYA DÖN</a>
        </div>
    </div>
    <div class="content">
        <div class="header"><h1>MÜŞTERİ VERİTABANI</h1></div>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Ad Soyad</th><th>E-Posta Adresi</th><th>Yetki Durumu</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($musteriler)): ?>
                        <tr><td colspan="4" style="text-align:center;">Henüz sistemde kullanıcı yok.</td></tr>
                    <?php else: ?>
                        <?php foreach($musteriler as $m): ?>
                        <tr>
                            <td style="color:#888;">#<?php echo $m['id']; ?></td>
                            <td style="font-weight:bold; color:#fff;"><?php echo htmlspecialchars($m['ad_soyad'] ?? 'İsimsiz'); ?></td>
                            <td><?php echo htmlspecialchars($m['email']); ?></td>
                            <td>
                                <?php if($m['rol'] == 'admin'): ?>
                                    <span class="badge admin">YÖNETİCİ</span>
                                <?php else: ?>
                                    <span class="badge user">MÜŞTERİ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>