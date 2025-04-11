<?php
require 'config.php';

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
$result = array_map(function($point) {
    return [
        'time' => $point[0],
        'value' => $point[1]
    ];
}, $values);

header('Content-Type: application/json');
echo json_encode($result);
