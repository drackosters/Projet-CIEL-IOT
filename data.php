<?php
require 'config.php'; // garde si nécessaire

header('Content-Type: application/json');

// Récupère l'intervalle choisi par l'utilisateur
$intervalle = isset($_GET['intervalle']) ? $_GET['intervalle'] : 'minute';

// Construction de la requête InfluxDB selon l'intervalle
if ($intervalle === 'heure') {
    $query = "SELECT MEAN(apower) FROM mqtt_consumer WHERE time >= now() - 24h GROUP BY time(1h) fill(none)";
} elseif ($intervalle === 'jour') {
    $query = "SELECT MEAN(apower) FROM mqtt_consumer WHERE time >= now() - 7d GROUP BY time(1d) fill(none)";
} else { // minute (par défaut)
    $query = "SELECT apower FROM mqtt_consumer ORDER BY time DESC LIMIT 60";
}

// Construction de l'URL InfluxDB
$url = "http://132.220.210.127:8086/query?db=iot_data&q=" . urlencode($query);

// Appel API
$response = @file_get_contents($url);

if (!$response) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l'appel à InfluxDB. URL: $url"]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    http_response_code(500);
    echo json_encode(["error" => "Réponse Influx invalide. Réponse brute: $response"]);
    exit;
}

$values = $data['results'][0]['series'][0]['values'] ?? null;

if (!$values) {
    http_response_code(500);
    echo json_encode([
        "error" => "Pas de données trouvées ou requête invalide.",
        "url" => $url,
        "réponse_brute" => $response
    ]);
    exit;
}

// Formatage des données
$result = array_map(function($point) use ($intervalle) {
    return [
        'time' => $point[0],
        'value' => is_array($point[1]) ? null : $point[1] // Parfois Influx renvoie des nulls
    ];
}, $values);

echo json_encode($result);
?>
