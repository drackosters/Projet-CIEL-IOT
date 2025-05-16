<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/error.log');
header('Content-Type: application/json');

require 'config.php';

try {
    $response = @file_get_contents($url);
    if ($response === false) {
        error_log("Erreur InfluxDB : impossible de se connecter à $url");
        throw new Exception("Impossible de se connecter à InfluxDB");
    }

    $data = json_decode($response, true);

    // Traitement des 3 séries : apower, temperature, humidite
    $series = $data['results'][0]['series'] ?? [];

    $result = [
        'apower' => [],
        'temperature' => [],
        'humidite' => []
    ];

    foreach ($series as $serie) {
        $field = $serie['columns'][1]; // le nom du champ : apower, temperature ou humidite

        foreach ($serie['values'] as $point) {
            $result[$field][] = [
                'time' => $point[0],
                'value' => $point[1]
            ];
        }
    }

    echo json_encode($result);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
