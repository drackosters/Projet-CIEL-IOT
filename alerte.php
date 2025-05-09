<?php
// Affichage des erreurs pour débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require 'config.php';

try {
    // Récupérer les topics et leurs seuils depuis la BDD MySQL
    $stmt = $conn->query("SELECT topic, Seuil_Min, Seuil_Max FROM TOPICS");
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($topics)) {
        throw new Exception("Aucun topic trouvé dans la table TOPICS.");
    }

    $alertes = [];

    // Liste des topics à ignorer
    $topics_ignores = [
        'shelly/watt/status/pm1:0-id',
        'shelly/watt/status/pm1:0-voltage',
        'shelly/watt/status/pm1:0-current',
        'shelly/watt/status/pm1:0-freq',
        'shelly/watt/status/pm1:0-aenergy',
        'shelly/watt/status/pm1:0-ret_aenergy'
    ];

    // Configuration InfluxDB
    $host = "http://132.220.210.127:8086";
    $db = "iot_data";

    foreach ($topics as $topic) {
        // Ignorer les topics non pertinents
        if (in_array($topic['topic'], $topics_ignores)) {
            continue;
        }

        // Vérifier uniquement les topics liés à apower
        if (!str_contains($topic['topic'], 'apower')) {
            continue;
        }

        $query = "SELECT LAST(apower) FROM mqtt_consumer WHERE topic = '" . preg_replace('/[^a-zA-Z0-9_\/+-]/', '', $topic['topic']) . "'";
        $url = "$host/query?db=$db&q=" . urlencode($query);

        $response = @file_get_contents($url);

        if ($response === false) {
            $alertes[] = "Erreur : InfluxDB inaccessible pour le topic {$topic['topic']}";
            continue;
        }

        $data = json_decode($response, true);
        if (!isset($data['results'][0]['series'][0]['values'][0][1])) {
            $alertes[] = "Erreur : Donnée manquante pour le topic {$topic['topic']}";
            continue;
        }

        $valeur = $data['results'][0]['series'][0]['values'][0][1];

        if ($valeur > $topic['Seuil_Max']) {
            $alertes[] = "{$topic['topic']} : $valeur W dépasse le seuil max ({$topic['Seuil_Max']})";
        } elseif ($valeur < $topic['Seuil_Min']) {
            $alertes[] = "{$topic['topic']} : $valeur W en dessous du seuil min ({$topic['Seuil_Min']})";
        }
    }

    echo json_encode($alertes);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}