<?php
$hedef = "uploads/";
if (!is_dir($hedef)) mkdir($hedef, 0777, true);

// 12 Farklı Tişörtün Premium Sokak Modası SVG Tasarımları
$tasarimlar = [
    'custom1' => ['bg' => '#111', 'badge' => 'CUSTOM', 'color' => '#fff', 'text' => 'Bazzkıla SKETCH', 'desc' => 'YOUR IMAGE HERE', 'icon' => '🎨'],
    'custom2' => ['bg' => '#111', 'badge' => 'CUSTOM', 'color' => '#fff', 'text' => 'DESIGN YOUR OWN', 'desc' => 'UPLOAD YOUR VISUAL', 'icon' => '👕'],
    'custom3' => ['bg' => '#111', 'badge' => 'CUSTOM', 'color' => '#fff', 'text' => 'PREMIUM TEXT', 'desc' => 'CUSTOM INSCRIPTION', 'icon' => '✍️'],
    'custom4' => ['bg' => '#f5f5f5', 'badge' => 'CUSTOM', 'color' => '#000', 'text' => 'BACKPRINT ART', 'desc' => 'CUSTOM IMAGE', 'icon' => '🔥'],
    
    'nakis1' => ['bg' => '#111', 'badge' => 'EMBROIDERY', 'color' => '#d4af37', 'text' => 'baZZkıla VELVET', 'desc' => 'GOLD STITCHED', 'icon' => '🧵'],
    'nakis2' => ['bg' => '#1b1b1b', 'badge' => 'EMBROIDERY', 'color' => '#fff', 'text' => 'baZZkıla RELIEF', 'desc' => 'HEAVY STITCH', 'icon' => '⚡'],
    'nakis3' => ['bg' => '#111', 'badge' => 'EMBROIDERY', 'color' => '#ff4444', 'text' => 'ROSE SERIES', 'desc' => 'EMBROIDERED ROSE', 'icon' => '🌹'],
    'nakis4' => ['bg' => '#111', 'badge' => 'EMBROIDERY', 'color' => '#4CAF50', 'text' => 'GEOMETRIC', 'desc' => 'CYBER PUNCH', 'icon' => '💠'],
    
    'hazir1' => ['bg' => '#111', 'badge' => 'PRINTED', 'color' => '#fff', 'text' => 'SIRADANLIGi REDDET', 'desc' => 'HEAVY WATER-BASED', 'icon' => '🚫'],
    'hazir2' => ['bg' => '#111', 'badge' => 'PRINTED', 'color' => '#00d2ff', 'text' => 'URBAN BRUSH', 'desc' => 'WATER-BASED PRINT', 'icon' => '🖌️'],
    'hazir3' => ['bg' => '#1c1c1c', 'badge' => 'PRINTED', 'color' => '#fff', 'text' => 'baZZkıla SIGN', 'desc' => 'STREETWEAR GRAPHIC', 'icon' => '💀'],
    'hazir4' => ['bg' => '#f0f0f0', 'badge' => 'PRINTED', 'color' => '#000', 'text' => '90s VINTAGE', 'desc' => 'RETRO STREETWEAR', 'icon' => '📻']
];

foreach ($tasarimlar as $isim => $t) {
    $c = $t['color'];
    $bg = $t['bg'];
    $border = ($bg == '#fff' || $bg == '#f5f5f5' || $bg == '#f0f0f0') ? '#ddd' : '#333';
    
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="100%" height="100%">
        <rect width="100%" height="100%" fill="#050505"/>
        <!-- Askı ve Tişört Silueti -->
        <path d="M 400,100 L 450,130 L 450,110 Q 450,90 400,90 Q 350,90 350,110 L 350,130 Z" fill="none" stroke="#555" stroke-width="4"/>
        <path d="M 400,135 L 530,170 L 680,240 L 640,320 L 580,290 L 580,720 Q 580,730 570,730 L 230,730 Q 220,730 220,720 L 220,290 L 160,320 L 120,240 L 270,170 Z" fill="'.$bg.'" stroke="'.$border.'" stroke-width="5"/>
        
        <!-- Tasarım Baskı/Nakış Alanı -->
        <g transform="translate(250, 240)">
            <rect width="300" height="380" fill="none" stroke="#ff4444" stroke-dasharray="8 8" opacity="0.1"/>
            
            <!-- Sanatsal Grafik ve Etiket -->
            <rect x="20" y="20" width="260" height="40" rx="20" fill="rgba(255,68,68,0.15)"/>
            <text x="150" y="45" font-family="\'Courier New\', Courier, monospace" font-size="14" font-weight="900" fill="#ff4444" text-anchor="middle" letter-spacing="3">'.$t['badge'].'</text>
            
            <text x="150" y="160" font-family="Impact, Haettenschweiler, \'Arial Narrow Bold\', sans-serif" font-size="28" font-weight="900" fill="'.$c.'" text-anchor="middle" letter-spacing="1">'.$t['text'].'</text>
            
            <!-- Dev Sürpriz Simge -->
            <text x="150" y="250" font-size="75" text-anchor="middle">'.$t['icon'].'</text>
            
            <text x="150" y="310" font-family="Arial" font-size="12" fill="#888" text-anchor="middle" letter-spacing="2">'.$t['desc'].'</text>
            <text x="150" y="340" font-family="Arial" font-size="10" fill="#444" text-anchor="middle" letter-spacing="4">PREMIUM APPAREL</text>
        </g>
        
        <!-- Marka İmzası -->
        <text x="400" y="770" font-family="Arial" font-size="12" font-weight="bold" fill="#333" text-anchor="middle" letter-spacing="8">baZZkıla © 2026</text>
    </svg>';

    // Resim uzantılarını hem .png hem de .svg olarak veritabanı uyumluluğu için kaydedelim
    file_put_contents($hedef . $isim . ".svg", $svg);
    file_put_contents($hedef . $isim . ".png", $svg); // HTML img'leri svg kodunu png olarak da okuyabilir
}

echo "<h2 style='font-family:Arial; color:#4CAF50; text-align:center; margin-top:100px;'>⚡ 12 Premium T-Shirt Başarıyla Üretildi!</h2><p style='text-align:center; color:#666;'>Görseller 'uploads/' klasörüne yerleşti. Artık ilanların canavar gibi görünecek!</p>";
?>