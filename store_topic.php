<?php
$servername = "3.94.92.173";
$username = "admin";
$password = "admin";
$dbname = "plage_iot";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fermer la connexion
$conn = null;
?>