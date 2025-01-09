<?php
// Library GameQ untuk SAMP
require 'vendor/autoload.php';
use GameQ\GameQ;

// Konfigurasi server SAMP
$sampServerIp = '104.234.180.169'; // IP server SAMP
$sampServerPort = 7777; // Port server SAMP

// Konfigurasi server FiveM
$cfxAlias = 'p9x7lm'; // Alias server FiveM

// Fungsi untuk Query Data dari API menggunakan cURL
function fetchCfxData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL issues

    // Menambahkan header User-Agent untuk meniru permintaan dari browser
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ]);

    $response = curl_exec($ch);

    // Debugging cURL Error
    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch);
        curl_close($ch);
        return ['error' => curl_error($ch)];
    }

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpStatus !== 200) {
        echo "HTTP Error: " . $httpStatus; // Tampilkan HTTP error jika tidak berhasil
        return ['error' => "HTTP Status $httpStatus"];
    }

    return json_decode($response, true);
}

// Inisialisasi GameQ untuk SAMP
$GameQ = new GameQ();
$GameQ->addServer([
    'type' => 'samp',
    'host' => $sampServerIp . ':' . $sampServerPort,
]);

// Query SAMP
$sampData = $GameQ->process();
$sampInfo = $sampData[$sampServerIp . ':' . $sampServerPort];

// Informasi SAMP
if ($sampInfo['gq_online']) {
    $sampServerName = $sampInfo['gq_hostname'];
    $sampPlayers = $sampInfo['gq_numplayers'];
    $sampMaxPlayers = $sampInfo['gq_maxplayers'];
    $sampMap = $sampInfo['gq_mapname'];
} else {
    $sampServerName = 'Server Offline';
    $sampPlayers = 0;
    $sampMaxPlayers = 0;
    $sampMap = 'N/A';
}

// Query API FiveM
$cfxApiUrl = "https://servers-frontend.fivem.net/api/servers/single/$cfxAlias";
$cfxData = fetchCfxData($cfxApiUrl);

// Informasi Default FiveM
$fivemServerName = 'Server Offline';
$fivemPlayers = 0;
$fivemMaxPlayers = 0;
$fivemMap = 'N/A';
$fivemOnlinePlayers = []; // Daftar pemain online

if (isset($cfxData['Data'])) {
    $fivemServerName = $cfxData['Data']['hostname'];
    $fivemPlayers = $cfxData['Data']['clients'];
    $fivemMaxPlayers = $cfxData['Data']['sv_maxclients'];
    $fivemMap = $cfxData['Data']['mapname'];
    $fivemOnlinePlayers = $cfxData['Data']['players']; // Daftar pemain online
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wargaindo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2a2a72, #009ffd);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            flex-direction: column;
            gap: 20px;
            margin: 0;
        }

        .container {
            background: #ffffff10;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            width: 90%;
            max-width: 500px;
            transition: all 0.3s ease;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .info p {
            font-size: 1.2rem;
            margin: 10px 0;
            font-weight: bold;
        }

        .info span {
            display: block;
            font-size: 1rem;
            font-weight: normal;
            color: #ccc;
        }

        .players-list {
            margin-top: 20px;
            background: #1c1c1c;
            padding: 15px;
            border-radius: 10px;
            text-align: left;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #00d4ff;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .button:hover {
            background: #009acd;
        }
    </style>
</head>
<body>
    <!-- Info SAMP -->
    <div class="container">
        <h1>SAMP</h1>
        <h2><?= htmlspecialchars($sampServerName) ?></h2>
        <div class="info">
            <p>Players Online: <?= htmlspecialchars($sampPlayers) ?>/<?= htmlspecialchars($sampMaxPlayers) ?><span>Jumlah pemain saat ini</span></p>
            <p>Map: <?= htmlspecialchars($sampMap) ?><span>Map yang sedang dimainkan</span></p>
            <p>IP: <?= htmlspecialchars($sampServerIp) ?>:<?= htmlspecialchars($sampServerPort) ?></p>
        </div>
    </div>

    <!-- Info FiveM -->
    <div class="container">
        <h1>FIVEM</h1>
        <h2><?= htmlspecialchars($fivemServerName) ?></h2>
        <div class="info">
            <p>Players Online: <?= htmlspecialchars($fivemPlayers) ?>/<?= htmlspecialchars($fivemMaxPlayers) ?><span>Jumlah pemain saat ini</span></p>
            <p>Map: <?= htmlspecialchars($fivemMap) ?><span>Map yang sedang dimainkan</span></p>
            <p>CFX Link: <a href="https://cfx.re/join/<?= htmlspecialchars($cfxAlias) ?>" class="button">Join Now</a></p>
        </div>
        <div class="players-list">
            <h3>Pemain Online di FiveM:</h3>
            <ul>
                <?php if (!empty($fivemOnlinePlayers)): ?>
                    <?php foreach ($fivemOnlinePlayers as $player): ?>
                        <li><?= htmlspecialchars($player['name']) ?> - ID: <?= htmlspecialchars($player['id'] ?? 'N/A') ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Tidak ada pemain online saat ini.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
