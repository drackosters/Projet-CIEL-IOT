<?php
require 'config.php';

$payload = json_decode(file_get_contents('php://input'), true);
$topic = $payload['topic'];

// Vérifier si le topic existe déjà
$stmt = $conn->prepare("SELECT COUNT(*) FROM unique_topics WHERE topic = ?");
$stmt->execute([$topic]);
$count = $stmt->fetchColumn();

if ($count == 0) {
    // Préparer et lier pour insérer le nouveau topic
    $stmt = $conn->prepare("INSERT INTO unique_topics (topic) VALUES (?)");
    $stmt->execute([$topic]);
}

// Fermer la connexion
$conn = null;
?>