<?php
set_time_limit(0);
require 'vendor/autoload.php';
require 'config.php';
// utilise composer pour agire comme client est récupèrer les requète mqtt
use PhpMqtt\Client\MqttClient; 

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

//connexion au broker mqtt en tant que client
$server = '3.83.82.167';
$port = 1883;
$clientId = uniqid();
$mqtt = new MqttClient($server, $port, $clientId);

try {
    //connexion
    $mqtt->connect();
    //souscription à tous les topics
    $mqtt->subscribe('#', function ($topic, $message) use ($conn) {
        $data = json_encode(['topic' => $topic, 'message' => $message]);
        echo "data: $data\n\n";
        ob_flush();
        flush();
        file_put_contents('messages.log', "[$topic] $message\n", FILE_APPEND);

        // Vérifier si le topic existe déjà
        $stmt = $conn->prepare("SELECT COUNT(*) FROM unique_topics WHERE topic = ?");
        $stmt->execute([$topic]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Préparer et lier pour insérer le nouveau topic dans la table unique_topics
            $stmt = $conn->prepare("INSERT INTO unique_topics (topic) VALUES (?)");
            $stmt->execute([$topic]);
        }
    }, 0);

    while (true) {
        $mqtt->loop(false);
        usleep(100000); // 100ms pause
    }
} catch (Exception $e) {
    echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
}
?>