<?php
set_time_limit(0);
require 'vendor/autoload.php';
use PhpMqtt\Client\MqttClient;

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$server = '54.173.114.33';
$port = 1883;
$clientId = uniqid();
$mqtt = new MqttClient($server, $port, $clientId);

try {
    $mqtt->connect();
    $mqtt->subscribe('#', function ($topic, $message) {
        $data = json_encode(['topic' => $topic, 'message' => $message]);
        echo "data: $data\n\n";
        ob_flush();
        flush();
        file_put_contents('messages.log', "[$topic] $message\n", FILE_APPEND);
    }, 0);

    while (true) {
        $mqtt->loop(false);
        usleep(100000); // 100ms pause
    }
} catch (Exception $e) {
    echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
}
