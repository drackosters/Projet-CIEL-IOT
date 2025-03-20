<?php
require 'config.php';

$topic = $_GET['topic'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iotName = $_POST['iot_name'];
    $unite = $_POST['unite'] ?? null;
    $seuil_min = $_POST['seuil_min'] ?? null;
    $seuil_max = $_POST['seuil_max'] ?? null;

    // Insérer le nouvel IoT dans la base de données
    $sql = "INSERT INTO IOT (Nom) VALUES (:iot_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':iot_name', $iotName);

    if ($stmt->execute()) {
        $iotId = $conn->lastInsertId();

        // Insérer le topic dans la table TOPICS
        $sql = "INSERT INTO TOPICS (Nom, Unite, Seuil_MIN, Seuil_MAX) VALUES (:topic, :unite, :seuil_min, :seuil_max)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':unite', $unite);
        $stmt->bindParam(':seuil_min', $seuil_min);
        $stmt->bindParam(':seuil_max', $seuil_max);

        if ($stmt->execute()) {
            $topicId = $conn->lastInsertId();

            // Lier le topic à l'IoT dans la table IOT_TOPICS
            $sql = "INSERT INTO IoT_Topics (IoT_ID, Topic_ID) VALUES (:iot_id, :topic_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':iot_id', $iotId);
            $stmt->bindParam(':topic_id', $topicId);

            if ($stmt->execute()) {
                // Supprimer le topic de la table unique_topics
                $sql = "DELETE FROM unique_topics WHERE topic = :topic";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':topic', $topic);
                $stmt->execute();

                echo "Nouvel IoT et topic ajoutés avec succès.";
                header('Location: topics.php');
                exit;
            } else {
                echo "Erreur lors de la liaison du topic à l'IoT.";
            }
        } else {
            echo "Erreur lors de l'ajout du topic.";
        }
    } else {
        echo "Erreur lors de l'ajout du nouvel IoT.";
    }

    // Fermer la connexion
    $conn = null;
} else {
    // Vérifier si le dernier message est un JSON
    $logFile = 'messages.log';
    $lastMessage = null;

    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        for ($i = count($lines) - 1; $i >= 0; $i--) {
            if (strpos($lines[$i], "[$topic]") === 0) {
                $lastMessage = substr($lines[$i], strlen("[$topic] "));
                break;
            }
        }
    }

    $isJson = false;
    if ($lastMessage !== null) {
        json_decode($lastMessage);
        $isJson = (json_last_error() == JSON_ERROR_NONE);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un nouvel IoT</title>
</head>
<body>
    <h1>Ajouter un nouvel IoT</h1>
    <form method="POST" action="create_iot.php?topic=<?php echo urlencode($topic); ?>">
        <label for="iot_name">Nom de l'IoT:</label>
        <input type="text" id="iot_name" name="iot_name" required><br>

        
            <label for="unite">Unité:</label>
            <input type="text" id="unite" name="unite" required><br>

            <label for="seuil_min">Seuil Min:</label>
            <input type="number" id="seuil_min" name="seuil_min" required><br>

            <label for="seuil_max">Seuil Max:</label>
            <input type="number" id="seuil_max" name="seuil_max" required><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>