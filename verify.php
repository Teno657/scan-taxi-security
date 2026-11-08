<?php
require 'db_connect.php';
$code = isset($_GET['code']) ? trim($_GET['code']) : '';
$stmt = $pdo->prepare("SELECT * FROM taxis WHERE code_unique = ?");
$stmt->execute([$code]);
$taxi = $stmt->fetch();

if (!$taxi) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h1 style='color:#d9534f;'>❌ QR Code Invalide</h1></div>");
}
$immat = htmlspecialchars($taxi['immatriculation']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Taxi Yaoundé</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        
        body {
            /* LA COULEUR EXACTE DE TES IMAGES */
            background-color: #fafbfd; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 15px;
            color: #333;
        }
        
        /* --- BANDEAU LANGUE --- */
        .lang-top { 
            width: 100%; 
            display: flex; 
            justify-content: flex-end; 
            margin-bottom: 10px; 
            position: relative; 
        }
        
        .lang-btn {
            background: #0a3d2a; 
            color: #fff; 
            padding: 8px 16px; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 600;
            border: none; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            cursor: pointer;
            transition: 0.3s;
        }
        .lang-btn:hover { background: #155d42; }
        
        /* Drapeaux */
        .flag-icon { 
            width: 22px; 
            height: 15px; 
            border-radius: 2px; 
            border: 1px solid #ddd; 
            object-fit: cover; 
            display: inline-block;
        }
        
        .lang-menu {
            display: none; 
            position: absolute; 
            top: 40px; 
            right: 0; 
            background: #fff; 
            border: 1px solid #ddd;
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            overflow: hidden; 
            z-index: 100; 
            min-width: 140px;
        }
        .lang-menu.active { display: block; }
        .lang-option { 
            padding: 12px 15px; 
            cursor: pointer; 
            font-size: 14px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            border-bottom: 1px solid #eee; 
            color: #333;
        }
        .lang-option:last-child { border-bottom: none; }
        .lang-option:hover { background: #f0f0f0; }

        /* --- CARTE PRINCIPALE --- */
        .card {
            width: 100%; 
            max-width: 380px; 
            /* ON ENLÈVE LE FOND BLANC DE LA CARTE POUR QU'ELLE SE FONDE */
            background: transparent; 
            display: flex; 
            flex-direction: column; 
            align-items: center;
            padding: 10px 0;
        }

        .taxi-visual { 
            width: 100%; 
            display: flex; 
            justify-content: center; 
            margin-bottom: 5px; 
        }
        .taxi-img { 
            width: 220px; 
            /* On enlève l'ombre portée pour ne pas voir le contour de l'image */
            filter: none; 
        }

        .verified-row { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            margin: 10px 0; 
        }
        .check-circle {
            width: 40px; 
            height: 40px; 
            background: #2ea44f; 
            border-radius: 50%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            color: #fff; 
            font-size: 24px; 
            font-weight: bold;
            border: 3px solid #eceef1; /* La bordure prend la couleur du fond */
            box-shadow: 0 0 0 2px #c3e6cb; /* Légèrement plus foncé pour rester visible */
        }
        .verified-text { 
            font-size: 28px; 
            font-weight: 800; 
            color: #2ea44f; 
        }

        /* Plaque d'immatriculation */
        .plate {
            background: #ffffff; /* La plaque reste blanche, c'est normal */
            border: 4px solid #047a45; 
            border-radius: 16px; 
            padding: 6px 25px;
            font-size: 32px;
            font-weight: 800; 
            color: #1a202c; 
            letter-spacing: 2px;
            margin: 10px 0 15px 0;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* Bouton contrôle */
        .official-btn {
            background: #033f24; 
            color: #ffffff;
            padding: 12px 30px;
            border-radius: 30px; 
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 25px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 80%; 
            max-width: 300px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        
        .icon-shield {
            width: 24px;
            height: 24px;
        }

        /* Armoiries */
        .armoiries { 
            width: 160px; 
            margin: 10px 0 20px 0; 
            /* On enlève l'ombre si il y en avait une */
            filter: none;
        }
    </style>
</head>
<body>

    <div class="lang-top">
        <button class="lang-btn" onclick="toggleMenu()">
            <img id="current-flag" src="img/drapeau-fr.png" class="flag-icon" alt="Drapeau">
            <span id="current-lang">FR</span> ▼
        </button>
        
        <div class="lang-menu" id="langMenu">
            <div class="lang-option" onclick="changeLang('fr')">
                <img src="img/drapeau-fr.png" class="flag-icon" alt="Français">
                Français
            </div>
            <div class="lang-option" onclick="changeLang('en')">
                <img src="img/drapeau-uk.png" class="flag-icon" alt="English">
                English
            </div>
        </div>
    </div>

    <div class="card">
        <div class="taxi-visual">
            <!-- Assure-toi que cette image a bien le même fond #eceef1 -->
            <img src="img/taxi-verify.png" alt="Taxi" class="taxi-img">
        </div>

        <div class="verified-row">
            <div class="check-circle">✓</div>
            <div class="verified-text" data-fr="Vérifié" data-en="Verified">Vérifié</div>
        </div>

        <div class="plate"><?php echo $immat; ?></div>

        <div class="official-btn">
            <svg class="icon-shield" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-3z" fill="#d4af37" stroke="#8a6d1c" stroke-width="1.5"/>
                <rect x="9" y="11" width="6" height="5" rx="1" fill="#fcd116" stroke="#8a6d1c" stroke-width="1"/>
                <path d="M10 11V9a2 2 0 0 1 4 0v2" stroke="#fcd116" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="12" cy="13" r="1" fill="#000"/>
            </svg>
            <span data-fr="Contrôle officiel" data-en="Official Control">Contrôle officiel</span>
        </div>

        <!-- Assure-toi que cette image a bien le même fond #eceef1 -->
        <img src="img/armoiries.png" alt="Armoiries" class="armoiries">
    </div>

    <script>
        function toggleMenu() { 
            document.getElementById('langMenu').classList.toggle('active'); 
        }
        
        function changeLang(lang) {
            document.getElementById('current-lang').innerText = lang.toUpperCase();
            
            const flagImg = document.getElementById('current-flag');
            
            if (lang === 'fr') {
                flagImg.src = "img/drapeau-fr.png";
            } else {
                flagImg.src = "img/drapeau-uk.png";
            }

            document.querySelectorAll('[data-fr]').forEach(el => { 
                el.innerHTML = el.getAttribute('data-' + lang); 
            });
            
            document.getElementById('langMenu').classList.remove('active');
        }
        
        document.addEventListener('click', function(e) {
            if (!document.querySelector('.lang-top').contains(e.target)) {
                document.getElementById('langMenu').classList.remove('active');
            }
        });
    </script>
</body>
</html>