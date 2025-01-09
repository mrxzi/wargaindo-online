<?php
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

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
$sampResponse = [];
if ($sampInfo['gq_online']) {
    $sampResponse = [
        'name' => $sampInfo['gq_hostname'],
        'players' => $sampInfo['gq_numplayers'],
        'max_players' => $sampInfo['gq_maxplayers'],
        'map' => $sampInfo['gq_mapname'],
        'status' => 'online',
    ];
} else {
    $sampResponse = [
        'name' => 'Server Offline',
        'players' => 0,
        'max_players' => 0,
        'map' => 'N/A',
        'status' => 'offline',
    ];
}

// Query API FiveM
$cfxApiUrl = "https://servers-frontend.fivem.net/api/servers/single/$cfxAlias";
$cfxData = fetchCfxData($cfxApiUrl);

// Informasi FiveM
$fivemResponse = [
    'name' => 'Server Offline',
    'players' => 0,
    'max_players' => 0,
    'map' => 'N/A',
    'online_players' => [],
    'status' => 'offline',
];

if (isset($cfxData['Data'])) {
    $fivemResponse = [
        'name' => $cfxData['Data']['hostname'],
        'players' => $cfxData['Data']['clients'],
        'max_players' => $cfxData['Data']['sv_maxclients'],
        'map' => $cfxData['Data']['mapname'],
        'online_players' => $cfxData['Data']['players'],
        'status' => 'online',
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'samp' => $sampResponse,
    'fivem' => $fivemResponse,
]);
