<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/error.log');

header('Content-Type: application/json');

require 'config.php';

try {
    // Configuration InfluxDB (déjà définie dans config.php)
    $topic = "shelly/watt/status/pm1:0-apower"; // Topic corrigé

    // Requête InfluxDB : récupérer les 24 dernières données avec espacement d'une heure
    $query = "SELECT MEAN(apower) AS value FROM mqtt_consumer " .
             "WHERE topic = '$topic' " .
             "GROUP BY time(1h) " .
             "ORDER BY time DESC " .
             "LIMIT 24";
    $url = "$host/query?db=$db&q=" . urlencode($query);

    // Log de la requête pour débogage
    error_log("Exécution de la requête InfluxDB : $query");

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
        // Retourner un tableau vide au lieu de lever une exception
        echo json_encode([]);
        exit;
    }

    // Formatage des données (inverser l'ordre pour avoir les données du plus ancien au plus récent)
    $results = array_map(function($point) {
        return [
            'time' => $point[0],
            'value' => $point[1]
        ];
    }, array_reverse($values)); // Inverser pour chronologie ascendante

    echo json_encode($results);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}