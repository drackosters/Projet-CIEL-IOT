<?php
require 'config.php';

// Requête InfluxDB : moyenne toutes les 30 minutes sur la dernière heure
$url = "http://132.220.210.127:8086/query?db=iot_data&q=" . urlencode("
    SELECT mean(apower) AS apower FROM mqtt_consumer
    WHERE time > now() - 1h
    GROUP BY time(30m) fill(none)
");

// Appel API
$response = @file_get_contents($url);

if (!$response) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l'appel à InfluxDB."]);
    exit;
}

$data = json_decode($response, true);

$values = $data['results'][0]['series'][0]['values'] ?? null;

if (!$values) {
    http_response_code(500);
    echo json_encode([
        "error" => "Pas de données trouvées.",
        "réponse_brute" => $response
    ]);
    exit;
}

// Formatage des données (ordre chronologique)
//$values = array_reverse($values);

$result = array_map(function($point) {
    return [
        'time' => $point[0],
        'value' => $point[1]
    ];
}, $values);

header('Content-Type: application/json');
echo json_encode($result);
?>
