<?php
$payload = json_decode(file_get_contents('php://input'), true);
$topic = $payload['topic'];
$logFile = 'messages.log';

$lastMessage = null;
$isJson = false;

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    for ($i = count($lines) - 1; $i >= 0; $i--) {
        if (strpos($lines[$i], "[$topic]") === 0) {
            $lastMessage = substr($lines[$i], strlen("[$topic] "));
            break;
        }
    }
}

if ($lastMessage !== null) {
    json_decode($lastMessage);
    $isJson = (json_last_error() == JSON_ERROR_NONE);
    echo json_encode(['success' => true, 'message' => $lastMessage, 'isJson' => $isJson]);
} else {
    echo json_encode(['success' => false]);
}
?>