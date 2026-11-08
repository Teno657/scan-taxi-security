<?php
header("Access-Control-Allow-Origin: *");
require 'db_connect.php';
include 'phpqrcode/qrlib.php';

$code = isset($_GET['code']) ? trim($_GET['code']) : '';
if (empty($code)) { die("Code manquant."); }

$stmt = $pdo->prepare("SELECT * FROM taxis WHERE code_unique = ?");
$stmt->execute([$code]);
$taxi = $stmt->fetch();
if (!$taxi) { die("Taxi introuvable."); }

$qr_image = 'qrcodes/' . $taxi['code_unique'] . '.png';
if (!file_exists($qr_image)) {
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
    $url_to_encode = "http://" . $ip_actuelle . "/scan-taxi/verify.php?code=" . $taxi['code_unique'];
    QRcode::png($url_to_encode, $qr_image, QR_ECLEVEL_L, 10, 2);
}

$num_avant = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
$num_apres = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
$num_id = $num_avant . ' ' . $num_apres;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Autocollant - <?php echo $taxi['code_unique']; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            background: #e0e0e0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .download-btn {
            margin-bottom: 20px;
            padding: 12px 25px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .sticker {
            background: #ffffff;
            width: 660px;  
            height: 280px; 
            border: 2px solid #666; 
            border-radius: 6px;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            box-sizing: border-box;
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

        /* --- PARTIE DROITE : TEXTES (Alignement manuel) --- */
        .info-section {
            width: 70%;
            padding-left: 20px;
            height: 100%; 
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centre tout le bloc au milieu */
        }

        /* "SCANNEZ POUR IDENTIFIER" (Descendu un peu) */
        .text-top {
            color: #333;
            font-size: 17px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.5px;
            margin-bottom: 30px; /* Espace avec le numéro en dessous */
            margin-left: 5px; /* Aligne avec le chiffre 9 du dessous */
        }

        .shield-icon {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #333;
            stroke-width: 2;
        }

        /* Bloc central : Numéro + CUY */
        .main-block {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2px; /* Petit espace pour le texte du bas */
        }

        .main-number {
            font-size: 65px; 
            font-weight: 900;
            color: #2a2a2a; /* <-- LA SEULE MODIFICATION : Le noir vif est remplacé par un gris très foncé */
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

        /* "SCAN TO IDENTIFY" (Monté un peu) */
        .text-bottom {
            color: #333;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: 30px; /* Colle au numéro */
            margin-left: 30px; /* Aligné avec le chiffre du dessus */
        }

    </style>
</head>
<body>

    <button onclick="downloadSticker()" class="download-btn" id="downloadBtn">
        📥 Télécharger l'autocollant
    </button>

    <div class="sticker-container">
        <div class="sticker" id="stickerContent">
            
            <div class="qr-section">
                <div class="qr-frame">
                    <img src="<?php echo $qr_image; ?>?v=<?php echo time(); ?>" alt="QR Code" crossorigin="anonymous">
                </div>
            </div>

            <div class="info-section">
                
                <!-- Texte en haut, proche du numéro -->
                <div class="text-top">
                    <svg class="shield-icon" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                    SCANNEZ POUR IDENTIFIER
                </div>

                <!-- Numéro + CUY -->
                <div class="main-block">
                    <div class="main-number"><?php echo $num_id; ?></div>
                    <div class="logo-cuy">CUY</div>
                </div>

                <!-- Texte en bas, proche du numéro -->
                <div class="text-bottom">
                    SCAN TO IDENTIFY
                </div>
                
            </div>

        </div>
    </div>

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
                link.download = 'Autocollant_<?php echo $taxi['code_unique']; ?>.png';
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