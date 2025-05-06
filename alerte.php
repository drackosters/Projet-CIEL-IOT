<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'config.php';

try {
    $stmt = $pdo->query("SELECT topic, Seuil_Min, Seuil_Max FROM TOPICS");
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $alertes = [];

    foreach ($topics as $topic) {
        $query = "SELECT LAST(apower) FROM mqtt_consumer WHERE topic = '{$topic['topic']}'";
        $url = "$host/query?db=$db&q=" . urlencode($query);

        $response = @file_get_contents($url);

        if ($response === false) {
            $alertes[] = "⚠️ Impossible d'accéder à InfluxDB pour le topic {$topic['topic']}";
            continue;
        }

        $data = json_decode($response, true);

        if (!isset($data['results'][0]['series'][0]['values'][0][1])) {
            $alertes[] = "⚠️ Aucune donnée trouvée pour le topic {$topic['topic']}";
            continue;
        }

        $value = $data['results'][0]['series'][0]['values'][0][1];

        if ($value > $topic['Seuil_Max']) {
            $alertes[] = "⚠️ {$topic['topic']} : $value W > Seuil Max ({$topic['Seuil_Max']} W)";
        } elseif ($value < $topic['Seuil_Min']) {
            $alertes[] = "⚠️ {$topic['topic']} : $value W < Seuil Min ({$topic['Seuil_Min']} W)";
        }
    }

    // Test manuel
    $alertes[] = "⚠️ [TEST] Seuil dépassé pour test manuel";

    header('Content-Type: application/json');
    echo json_encode($alertes);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur : " . $e->getMessage()]);
    exit;
}