<?php
session_start();
require 'dil.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = $secili_dil == 'tr' ? 'AI Tasarım & Stil Danışmanı' : 'AI Design & Style Advisor';
$ai_isim = $secili_dil == 'tr' ? 'Çevrimiçi' : 'Online';
$ai_yaziyor = $secili_dil == 'tr' ? 'baZZkıla yazıyor' : 'baZZkıla is typing';
$placeholder = $secili_dil == 'tr' ? 'Mesajınızı yazın...' : 'Type your message...';
?>
<!DOCTYPE html>
<html lang="<?php echo $secili_dil; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baZZkıla - <?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --bg-color: #050505; --text-color: #ffffff; --accent-color: #8a2be2; --accent-hover: #ff4444; --chat-bg: #0f0f0f; --bot-msg: #1a1a1a; --user-msg: linear-gradient(135deg, #ff4444, #8a2be2); --border-color: #1a1a1a; --glass-bg: rgba(5, 5, 5, 0.75); }
        body.theme-light { --bg-color: #f8f8f8; --text-color: #0a0a0a; --chat-bg: #fff; --bot-msg: #f0f0f0; --border-color: #e0e0e0; --glass-bg: rgba(248, 248, 248, 0.85); }
        
        /* HATA ÇÖZÜLDÜ: height 100vh yerine 100dvh eklendi, kaydırma düzeltildi */
        body { background-color: var(--bg-color); color: var(--text-color); font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; height: 100vh; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; transition: background 0.3s ease, color 0.3s ease; }
        
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background: var(--glass-bg); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 8px; text-transform: uppercase; cursor: pointer; transition: 0.3s; color: var(--text-color); }
        .header h2:hover { color: var(--accent-hover); }
        .header-nav { display: flex; align-items: center; gap: 20px; }
        .header-nav a { color: var(--text-color); text-decoration: none; font-size: 13px; font-weight: 600; letter-spacing: 2px; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .header-nav a:hover, .header-nav a.active { color: var(--accent-hover); transform: translateY(-2px); }
        .theme-toggle-btn { background: transparent; color: var(--text-color); border: none; cursor: pointer; font-size: 16px; transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        .theme-toggle-btn:hover { transform: rotate(180deg); color: var(--accent-hover); }
        .menu-btn { font-size: 22px; cursor: pointer; color: var(--text-color); transition: 0.3s; background: none; border: none; outline: none; margin-left: 10px; }
        .menu-btn:hover { color: var(--accent-hover); transform: scale(1.1); }

        .sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1001; opacity: 0; visibility: hidden; transition: 0.4s; }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        .sidebar { position: fixed; top: 0; right: -400px; width: 350px; height: 100vh; background: var(--bg-color); border-left: 1px solid var(--border-color); z-index: 1002; transition: 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); display: flex; flex-direction: column; padding: 80px 50px; box-sizing: border-box; box-shadow: -10px 0 30px rgba(0,0,0,0.5); }
        .sidebar.active { right: 0; }
        .sidebar .close-btn { position: absolute; top: 25px; right: 40px; font-size: 30px; cursor: pointer; color: var(--text-color); transition: 0.3s; background: none; border: none; }
        .sidebar .close-btn:hover { color: var(--accent-hover); transform: rotate(90deg); }
        .sidebar-links { display: flex; flex-direction: column; gap: 30px; margin-top: 20px; }
        .sidebar-links a { color: var(--text-color); text-decoration: none; font-size: 14px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase; transition: 0.3s; display: flex; align-items: center; gap: 15px; }
        .sidebar-links a:hover { color: var(--accent-hover); padding-left: 10px; }
        .sidebar-bottom { margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 30px; display: flex; flex-direction: column; gap: 20px; }
        .lang-switch { display: flex; gap: 20px; }
        .lang-switch a { color: #666; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 2px; transition: 0.3s; }
        .lang-switch a.active { color: var(--text-color); border-bottom: 2px solid var(--accent-hover); padding-bottom: 5px; }

        /* HATA ÇÖZÜLDÜ: min-height: 0 eklendi (Sayfanın dışına taşmayı engeller) */
        .chat-container { flex: 1; max-width: 1000px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; background: var(--chat-bg); border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color); position: relative; min-height: 0; }
        
        .chat-header { padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 15px; }
        .ai-avatar { width: 45px; height: 45px; background: var(--user-msg); border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 20px; box-shadow: 0 0 15px rgba(138, 43, 226, 0.4); color: #fff; }
        .ai-info h3 { margin: 0; font-size: 14px; font-weight: 800; letter-spacing: 2px; }
        .ai-info p { margin: 3px 0 0 0; font-size: 10px; color: #4CAF50; font-weight: 700; letter-spacing: 1px; display: flex; align-items: center; gap: 5px; }
        .ai-info p::before { content: ''; display: block; width: 6px; height: 6px; background: #4CAF50; border-radius: 50%; box-shadow: 0 0 5px #4CAF50; }

        /* HATA ÇÖZÜLDÜ: overflow-y aktif edildi ve js ile çakışan scroll-behavior silindi */
        .chat-messages { flex: 1; padding: 30px; overflow-y: auto; display: flex; flex-direction: column; gap: 20px; }
        .chat-messages::-webkit-scrollbar { width: 6px; }
        .chat-messages::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        
        .message { max-width: 75%; padding: 15px 20px; border-radius: 12px; font-size: 13px; line-height: 1.6; letter-spacing: 0.5px; position: relative; font-weight: 500; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .msg-bot { background: var(--bot-msg); color: var(--text-color); align-self: flex-start; border-bottom-left-radius: 2px; border: 1px solid var(--border-color); }
        .msg-user { background: var(--user-msg); color: #fff; align-self: flex-end; border-bottom-right-radius: 2px; box-shadow: 0 5px 15px rgba(255, 68, 68, 0.2); }
        
        .quick-replies { display: flex; gap: 10px; flex-wrap: wrap; padding: 0 30px 15px 30px; }
        .chip { background: transparent; color: #888; border: 1px solid #333; padding: 8px 15px; border-radius: 30px; font-size: 11px; cursor: pointer; transition: 0.3s; font-family: 'Montserrat'; font-weight: 600; letter-spacing: 1px; }
        .chip:hover { border-color: var(--accent-hover); color: #fff; background: rgba(255,68,68,0.1); }

        .chat-input-area { padding: 20px 30px; background: var(--chat-bg); border-top: 1px solid var(--border-color); display: flex; gap: 15px; align-items: center; }
        .chat-input-area input { flex: 1; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); padding: 18px 25px; border-radius: 30px; font-size: 13px; font-family: 'Montserrat'; outline: none; transition: 0.3s; }
        .chat-input-area input:focus { border-color: var(--accent-hover); box-shadow: 0 0 10px rgba(255, 68, 68, 0.1); }
        .btn-send { background: var(--user-msg); color: #fff; border: none; width: 55px; height: 55px; border-radius: 50%; font-size: 18px; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center; box-shadow: 0 5px 15px rgba(138, 43, 226, 0.3); flex-shrink: 0;}
        .btn-send:hover { transform: scale(1.05) rotate(-10deg); box-shadow: 0 5px 20px rgba(255, 68, 68, 0.5); }

        .typing-indicator { display: none; padding: 15px 20px; background: var(--bot-msg); align-self: flex-start; border-radius: 12px; border-bottom-left-radius: 2px; border: 1px solid var(--border-color); font-size: 12px; color: #888; }
        .dots span { display: inline-block; width: 6px; height: 6px; background: #888; border-radius: 50%; margin: 0 2px; animation: wave 1.2s infinite; }
        .dots span:nth-child(2) { animation-delay: 0.2s; }
        .dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes wave { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-4px); } }
        @media (max-width: 768px) { .header-nav a { display: none; } .header-nav .menu-btn { display: block; } }
    </style>
</head>
<body>

    <div class="header" id="navbar">
        <h2 onclick="window.location='index.php'">baZZkıla</h2>
        <div class="header-nav">
            <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeBtnTop" title="Tema Değiştir"><i class="fa-solid fa-moon"></i></button>
            <a href="ai_tasarim.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
            <a href="ai_tasarim.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            <span style="color:#333; margin:0 5px; display:inline-block;">|</span>
            <a href="index.php"><i class="fa-solid fa-store"></i></a>
            <button class="menu-btn" onclick="toggleMenu()"><i class="fa-solid fa-bars-staggered"></i></button>
        </div>
    </div>

    <div class="sidebar-overlay" id="menuOverlay" onclick="toggleMenu()"></div>
    <div class="sidebar" id="sideMenu">
        <button class="close-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></button>
        
        <div class="sidebar-links">
            <a href="kendi_tasarla.php"><i class="fa-solid fa-wand-magic"></i> <?php echo $secili_dil == 'tr' ? 'KENDİ TASARLA' : 'DESIGN YOUR OWN'; ?></a>
            <a href="index.php"><i class="fa-solid fa-house"></i> <?php echo $secili_dil == 'tr' ? 'ANA SAYFA' : 'HOME'; ?></a>
            <a href="ai_tasarim.php" style="color:var(--accent-hover);"><i class="fa-solid fa-wand-magic-sparkles"></i> AI TASARIM</a>
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
                <a href="ai_tasarim.php?lang=tr" class="<?php echo $secili_dil=='tr' ? 'active' : ''; ?>">TR</a>
                <a href="ai_tasarim.php?lang=en" class="<?php echo $secili_dil=='en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-header">
            <div class="ai-avatar"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <div class="ai-info">
                <h3>baZZkıla CORE v2.0</h3>
                <p><?php echo $ai_isim; ?></p>
            </div>
        </div>

        <div class="chat-messages" id="chatBox">
            <div class="message msg-bot">
                <?php if($secili_dil == 'tr'): ?>
                    Selam! Ben baZZkıla Yapay Zeka Stil Danışmanı. Sana nasıl yardımcı olabilirim?<br><br>Eğer tarzına uygun bir <b>tişört bulmak</b> istiyorsan bana söyleyebilirsin, ya da tamamen sana ait <b>özel bir tasarım (custom)</b> fikri yaratmamı isteyebilirsin.
                <?php else: ?>
                    Hello! I am baZZkıla AI Style Advisor. How can I help you today?<br><br>You can ask me to <b>find a t-shirt</b> that matches your style, or request a completely <b>custom design idea</b> for you.
                <?php endif; ?>
            </div>
        </div>

        <div class="typing-indicator" id="typing">
            <?php echo $ai_yaziyor; ?> <span class="dots"><span></span><span></span><span></span></span>
        </div>

        <div class="quick-replies">
            <?php if($secili_dil == 'tr'): ?>
                <button class="chip" onclick="sendChip('Tişört almak istiyorum')">Tişört önerisi yap</button>
                <button class="chip" onclick="sendChip('Kişiye özel tasarım fikri ver')">Custom tasarım fikri ver</button>
                <button class="chip" onclick="sendChip('En çok satanlar neler?')">Trendleri göster</button>
            <?php else: ?>
                <button class="chip" onclick="sendChip('I want to buy a t-shirt')">Recommend a t-shirt</button>
                <button class="chip" onclick="sendChip('Give me a custom design idea')">Custom design idea</button>
                <button class="chip" onclick="sendChip('What are the best sellers?')">Show trends</button>
            <?php endif; ?>
        </div>

        <div class="chat-input-area">
            <input type="text" id="userInput" placeholder="<?php echo $placeholder; ?>" onkeypress="handleKeyPress(event)">
            <button class="btn-send" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
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

        const chatBox = document.getElementById('chatBox');
        const userInput = document.getElementById('userInput');
        const typingIndicator = document.getElementById('typing');
        const seciliDil = '<?php echo $secili_dil; ?>';

        // HATA ÇÖZÜLDÜ: Scroll motoru güncellendi (Eski js hesaplama hatası giderildi)
        function scrollToBottom() { 
            setTimeout(() => {
                chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            }, 50);
        }
        
        function handleKeyPress(e) { if (e.key === 'Enter') sendMessage(); }
        function sendChip(text) { userInput.value = text; sendMessage(); }

        function sendMessage() {
            const text = userInput.value.trim();
            if (text === '') return;

            const userMsg = document.createElement('div');
            userMsg.className = 'message msg-user';
            userMsg.textContent = text;
            chatBox.appendChild(userMsg);
            
            userInput.value = '';
            scrollToBottom();

            typingIndicator.style.display = 'block';
            chatBox.appendChild(typingIndicator);
            scrollToBottom();

            setTimeout(() => {
                typingIndicator.style.display = 'none';
                generateAIResponse(text.toLowerCase());
            }, 1200 + Math.random() * 1000);
        }

        function generateAIResponse(input) {
            let response = "";

            if (seciliDil === 'tr') {
                if (input.includes("almak") || input.includes("tişört") || input.includes("öneri")) {
                    response = "Tarzını yansıtma vakti. Sokakların ritmine ayak uyduran dökümlü 'Oversize' bir kalıp mı istersin, yoksa premium kumaşlı şık bir 'Nakış' işleme mi? Ne tarz renklerden (koyu/açık) hoşlanırsın?";
                } else if (input.includes("tasarım") || input.includes("fikir") || input.includes("custom") || input.includes("özel")) {
                    response = "Kendi kurallarını koymak harika! Senin için birkaç custom tasarım konsepti oluşturabilirim:<br><br><b>1. Siberpunk:</b> Neon mor ve mavi tonlarında, sırtı kaplayan fütüristik bir kurt silüeti.<br><b>2. Minimalist:</b> Sol göğüste ufak, ince fontlu Japon kaligrafisi.<br><b>3. Sokak Sanatı (Graffiti):</b> Siyah oversize üzerine damlayan beyaz sprey boya efekti.<br><br>Hangisi ruhuna daha yakın hitap ediyor?";
                } else if (input.includes("nakış") || input.includes("oversize") || input.includes("baskı") || input.includes("siyah") || input.includes("koyu")) {
                    response = "Harika bir seçim! Vitrindeki <b>'Hazır Baskı'</b> ve <b>'Nakış'</b> koleksiyonlarımız tam sana göre. Hemen sol üstteki 'VİTRİNE DÖN' butonuna tıklayarak yeni sezon ürünlerimizi inceleyebilirsin.";
                } else if (input.includes("trend") || input.includes("satan") || input.includes("popüler")) {
                    response = "Şu an sokak modasında en çok 'BAZZKILA SIGNATURE SERIES' ve 'VELVET EMBROIDERY' modellerimiz sınırları zorluyor. İstersen vitrine dönüp detaylarına göz atabilirsin.";
                } else {
                    response = "Bu konuda tam olarak ne istediğini biraz daha açar mısın? Sana en uygun sokak modası parçasını veya custom tasarımı yaratmam için bana tarzından bahset.";
                }
            } else {
                if (input.includes("buy") || input.includes("t-shirt") || input.includes("recommend") || input.includes("shirt")) {
                    response = "Time to reflect your style. Do you want a loose 'Oversize' fit that keeps up with the rhythm of the streets, or an elegant 'Embroidery' on premium fabric? What kind of colors (dark/light) do you prefer?";
                } else if (input.includes("design") || input.includes("idea") || input.includes("custom")) {
                    response = "Setting your own rules is great! Here are some custom design concepts for you:<br><br><b>1. Cyberpunk:</b> A futuristic wolf silhouette covering the back in neon purple and blue tones.<br><b>2. Minimalist:</b> Small, fine-font Japanese calligraphy on the left chest.<br><b>3. Street Art (Graffiti):</b> White spray paint dripping effect on a black oversize tee.<br><br>Which one speaks to your soul?";
                } else if (input.includes("embroidery") || input.includes("oversize") || input.includes("print") || input.includes("black") || input.includes("dark")) {
                    response = "Great choice! Our <b>'Printed'</b> and <b>'Embroidery'</b> collections in the store are perfect for you. You can click the 'BACK TO STORE' button on the top left to check them out.";
                } else if (input.includes("trend") || input.includes("best") || input.includes("popular")) {
                    response = "Right now, our 'BAZZKILA SIGNATURE SERIES' and 'VELVET EMBROIDERY' models are pushing the limits in streetwear. You can go back to the store to check the details.";
                } else {
                    response = "Could you elaborate a bit more on what exactly you want? Tell me about your style so I can create the most suitable streetwear piece or custom design for you.";
                }
            }

            const botMsg = document.createElement('div');
            botMsg.className = 'message msg-bot';
            botMsg.innerHTML = response;
            chatBox.appendChild(botMsg);
            scrollToBottom();
        }
    </script>
</body>
</html>