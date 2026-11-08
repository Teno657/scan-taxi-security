<?php
header("Access-Control-Allow-Origin: *");
require 'db_connect.php';
include 'phpqrcode/qrlib.php';

// 1. Générer une immatriculation aléatoire
$lettres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$chiffres = '0123456789';
$immat = 'CE' . $chiffres[rand(0, 9)] . $chiffres[rand(0, 9)] . $chiffres[rand(0, 9)] . $lettres[rand(0, 25)] . $lettres[rand(0, 25)];

// 2. Générer un code unique interne
$code_unique = 'TAXI-' . strtoupper(bin2hex(random_bytes(4)));

// 3. Enregistrer en base de données
$stmt = $pdo->prepare("INSERT INTO taxis (code_unique, immatriculation) VALUES (?, ?)");
$stmt->execute([$code_unique, $immat]);

// 4. Détection automatique de l'IP
function getLocalIP() {
    $output = shell_exec('ipconfig');
    if (preg_match_all('/IPv4.*?:\s*([0-9\.]+)/i', $output, $matches)) {
        foreach ($matches[1] as $ip) {
            if (preg_match('/^192\.168\./', $ip) || preg_match('/^10\./', $ip) || preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)) {
                if (strpos($ip, '192.168.146.') !== 0 && strpos($ip, '192.168.88.') !== 0) return $ip;
            }
        }
    }
    return '127.0.0.1';
}

$ip_actuelle = getLocalIP();
$folder = '/scan-taxi';
$url_to_encode = "http://" . $ip_actuelle . $folder . "/verify.php?code=" . $code_unique;

// 5. Créer le fichier image du QR Code
$tempDir = 'qrcodes/';
if (!file_exists($tempDir)) { mkdir($tempDir, 0777, true); }
$filename = $tempDir . $code_unique . '.png';
QRcode::png($url_to_encode, $filename, QR_ECLEVEL_L, 10, 2);

// 6. Générer un numéro aléatoire pour l'autocollant (style 02 097)
$num_avant = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
$num_apres = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
$num_id = $num_avant . ' ' . $num_apres;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autocollant Généré - Taxi Yaoundé</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        body {
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            padding: 20px;
        }

        .download-btn {
            margin-bottom: 30px;
            padding: 15px 30px;
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(58, 123, 213, 0.4);
        }
        .download-btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 12px 25px rgba(58, 123, 213, 0.6); 
        }

               /* --- LE STICKER --- */
        .sticker {
            background: #ffffff;
            width: 660px;  
            height: 280px; 
            border: 4px solid #999; /* <-- TRAIT FIN AJOUTÉ ICI */
            border-radius: 6px;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            box-sizing: border-box;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .qr-section {
            width: 30%; 
            display: flex;
            justify-content: center;
            align-items: center;
            border-right: 2px solid #555; 
            padding-right: 15px;
            height: 100%;
        }

        .qr-frame {
            border: 3px solid #333; 
            border-radius: 10px; 
            padding: 2px; 
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-frame img {
            width: 190px; 
            height: 190px; 
            border-radius: 20px; 
            display: block;
        }

        .info-section {
            width: 70%;
            padding-left: 20px;
            height: 100%; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .text-top {
            color: #333;
            font-size: 17px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.5px;
            margin-bottom: 30px;
            margin-left: 5px;
        }

        .shield-icon {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #333;
            stroke-width: 2;
        }

        .main-block {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2px;
        }

        .main-number {
            font-size: 65px; 
            font-weight: 900;
            color: #2a2a2a; 
            line-height: 1;
        }

        .logo-cuy {
            background: #1c2541; 
            color: #fff;
            padding: 8px 15px;
            font-size: 40px;
            font-weight: 900;
            border-radius: 4px;
            line-height: 1;
            letter-spacing: 1px;
        }

        .text-bottom {
            color: #333;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: 30px;
            margin-left: 30px;
        }

        /* Lien de retour discret */
        .link-back {
            color: #718096;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-top: 25px;
            transition: color 0.2s;
        }
        .link-back:hover { color: #3a7bd5; }
    </style>
</head>
<body>

    <!-- Le bouton de téléchargement -->
    <button onclick="downloadSticker()" class="download-btn" id="downloadBtn">
        📥 Télécharger l'autocollant
    </button>

    <!-- Le visuel de l'autocollant (qui sera capturé) -->
    <div class="sticker" id="stickerContent">
        
        <div class="qr-section">
            <div class="qr-frame">
                <img src="<?php echo $filename; ?>?v=<?php echo time(); ?>" alt="QR Code" crossorigin="anonymous">
            </div>
        </div>

        <div class="info-section">
            
            <div class="text-top">
                <svg class="shield-icon" viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                </svg>
                SCANNEZ POUR IDENTIFIER
            </div>

            <div class="main-block">
                <div class="main-number"><?php echo $num_id; ?></div>
                <div class="logo-cuy">CUY</div>
            </div>

            <div class="text-bottom">
                SCAN TO IDENTIFY
            </div>
            
        </div>

    </div>

    <!-- Lien retour -->
    <a href="index.php" class="link-back">← Retour à l'accueil</a>

    <script>
        function downloadSticker() {
            const sticker = document.getElementById('stickerContent');
            const btn = document.getElementById('downloadBtn');
            
            const originalText = btn.innerText;
            btn.innerText = "⏳ Génération...";
            btn.disabled = true;

            html2canvas(sticker, {
                scale: 3, 
                backgroundColor: "#ffffff",
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Autocollant_<?php echo $code_unique; ?>.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
                
                btn.innerText = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                alert("Erreur de téléchargement.");
                btn.innerText = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>