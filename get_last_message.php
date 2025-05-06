<?php
$payload = json_decode(file_get_contents('php://input'), true);
$topic = $payload['topic'];
$logFile = __DIR__ . '/messages.log';

error_log("Chemin du fichier : " . $logFile);
error_log("Topic transmis : " . $topic);

$lastMessage = null;
$isJson = false;

if (file_exists($logFile)) {
    error_log("Le fichier $logFile existe.");
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    for ($i = count($lines) - 1; $i >= 0; $i--) {
        if (strpos($lines[$i], "[$topic]") === 0) {
            $lastMessage = substr($lines[$i], strlen("[$topic] "));
            break;
        }
    }
} else {
    error_log("Le fichier $logFile n'existe pas.");
}

if ($lastMessage !== null) {
    json_decode($lastMessage);
    $isJson = (json_last_error() == JSON_ERROR_NONE);
    echo json_encode(['success' => true, 'message' => $lastMessage, 'isJson' => $isJson]);
} else {
    error_log("Aucun message trouvé pour le topic : $topic");
    echo json_encode(['success' => false]);
}

error_log("Topic reçu : " . $topic);
if ($lastMessage !== null) {
    error_log("Message trouvé : " . $lastMessage);
} else {
    error_log("Aucun message trouvé pour le topic : " . $topic);
}
?>