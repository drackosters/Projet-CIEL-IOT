<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/error.log');
header('Content-Type: application/json');

require 'config.php';

try {
    // Appel API
    $response = @file_get_contents($url);

    if ($response === false) {
        error_log("Erreur InfluxDB : impossible de se connecter à $url");
        throw new Exception("Impossible de se connecter à InfluxDB");
    }

    $data = json_decode($response, true);

    // Vérifier si des données sont disponibles
    $values = $data['results'][0]['series'][0]['values'] ?? null;
    if (!$values) {
        error_log("Aucune donnée trouvée pour la requête : $query");
        throw new Exception("Aucune donnée trouvée pour les 24 dernières heures.");
    }

    // Formatage des données
    $results = array_map(function($point) {
        return [
            'time' => $point[0],
            'value' => $point[1]
        ];
    }, $values);

    // Inverser les résultats pour afficher du plus ancien au plus récent
    $results = array_reverse($results);

    echo json_encode($results);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}