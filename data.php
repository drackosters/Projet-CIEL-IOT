<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/error.log');

header('Content-Type: application/json');

require 'config.php';

try {
    // Récupérer et valider l'intervalle
    $intervalle = isset($_GET['intervalle']) ? $_GET['intervalle'] : '3h';
    $allowed_intervals = ['3h', '15h', '24h'];
    if (!in_array($intervalle, $allowed_intervals)) {
        $intervalle = '3h'; // Par défaut
    }

    // Configuration InfluxDB
    $host = "http://132.220.210.127:8086";
    $db = "iot_data";
    $topic = "shelly/watt/status/pm1:0-apower";

    // Requête InfluxDB : moyenne toutes les 30 minutes sur l'intervalle choisi
    $query = "SELECT mean(apower) AS value FROM mqtt_consumer " .
             "WHERE topic = '$topic' AND time >= now() - $intervalle " .
             "GROUP BY time(30m) fill(none) ORDER BY time ASC";
    $url = "$host/query?db=$db&q=" . urlencode($query);

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
        throw new Exception("Aucune donnée trouvée pour l'intervalle $intervalle.");
    }

    // Formatage des données
    $results = array_map(function($point) {
        return [
            'time' => $point[0],
            'value' => $point[1]
        ];
    }, $values);

    echo json_encode($results);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}