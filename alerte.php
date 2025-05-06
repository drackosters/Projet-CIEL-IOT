<?php
// Affichage des erreurs pour debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require 'config.php';

try {
    // Vérification de la connexion PDO
    if (!isset($pdo)) {
        throw new Exception("Connexion PDO manquante.");
    }

    $stmt = $pdo->query("SELECT topic, Seuil_Min, Seuil_Max FROM TOPICS");
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $alertes = [];

    foreach ($topics as $topic) {
        $query = "SELECT LAST(apower) FROM mqtt_consumer WHERE topic = '{$topic['topic']}'";
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

        $valeur = $data['results'][0]['series'][0]['values'][0][ turbo1];

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
