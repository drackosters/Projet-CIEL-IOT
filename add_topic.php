<?php
require 'config.php';

$topic = $_GET['topic'] ?? '';

$isJson = false; // Initialisation par défaut
$messageString = ''; // Initialisation par défaut

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iotName = $_POST['iot_name'];
    $baseTopic = $topic;

    // Insérer le nouvel IoT dans la base de données
    $sql = "INSERT INTO IoT (nom) VALUES (:iot_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':iot_name', $iotName);

    if ($stmt->execute()) {
        $iotId = $conn->lastInsertId();

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'unite_') === 0) {
                // Décoder la clé JSON
                $jsonKey = urldecode(substr($key, 6));
                $unite = $_POST['unite_' . urlencode($jsonKey)] ?? '';
                $seuil_min = $_POST['seuil_min_' . urlencode($jsonKey)] ?? '';
                $seuil_max = $_POST['seuil_max_' . urlencode($jsonKey)] ?? '';
                $fullTopic = $baseTopic . '/' . $jsonKey;

                // Insérer dans la base de données
                try {
                    $sql = "INSERT INTO TOPICS (topic, unite, Seuil_MIN, Seuil_MAX) VALUES (:topic, :unite, :seuil_min, :seuil_max)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':topic', $fullTopic);
                    $stmt->bindParam(':unite', $unite);
                    $stmt->bindParam(':seuil_min', $seuil_min);
                    $stmt->bindParam(':seuil_max', $seuil_max);

                    if ($stmt->execute()) {
                        $topicId = $conn->lastInsertId();

                        // Lier le topic à l'IoT dans la table IOT_TOPICS
                        $sql = "INSERT INTO IoT_Topics (id_IoT, id_Topic) VALUES (:iot_id, :topic_id)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':iot_id', $iotId);
                        $stmt->bindParam(':topic_id', $topicId);

                        if (!$stmt->execute()) {
                            echo "Erreur lors de la liaison du topic à l'IoT.";
                        }
                    } else {
                        echo "Erreur lors de l'ajout du topic pour la clé '$jsonKey'.<br>";
                    }
                } catch (Exception $e) {
                    echo "Erreur SQL : " . htmlspecialchars($e->getMessage()) . "<br>";
                }
            }
        }

        // Supprimer le topic de la table unique_topics
        $sql = "DELETE FROM unique_topics WHERE topic = :topic";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':topic', $topic);
        $stmt->execute();

        echo "Nouvel IoT et topics ajoutés avec succès.";
        header('Location: topics.php');
        exit;
    } else {
        echo "Erreur lors de l'ajout du nouvel IoT.";
    }

    // Fermer la connexion
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un nouvel IoT</title>
    <link rel="stylesheet" href="topics.css">
</head>
<body>
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Ajouter un nouvel IoT</h1>
        <div class="conteneur-utilisateur">
            <button class="bouton-utilisateur" onclick="togglePanneauDeconnexion()">
                <?php echo htmlspecialchars($_SESSION['nom_utilisateur'] ?? 'Utilisateur'); ?>
            </button>
            <div id="panneau-deconnexion" class="panneau-deconnexion">
                <form action="deconnexion.php" method="post">
                    <button type="submit" name="deconnexion" class="bouton-deconnexion">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>

    <div class="conteneur-principal">
        <form method="POST" action="create_iot.php?topic=<?php echo urlencode($topic); ?>" class="formulaire">
            <label for="iot_name">Nom de l'IoT:</label>
            <input type="text" id="iot_name" name="iot_name" required><br>

            <div id="message-container">
                <?php if ($isJson && !empty($jsonKeys)): ?>
                    <h2>Message JSON</h2>
                    <ul id="json-keys-container"></ul>
                <?php else: ?>
                    <h2>Message non JSON ou JSON sans clés</h2>
                    <p><?php echo htmlspecialchars($messageString); ?></p>
                    <label for="unite">Unité:</label>
                    <input type="text" id="unite" name="unite" required><br>

                    <label for="seuil_min">Seuil Min:</label>
                    <input type="number" id="seuil_min" name="seuil_min" required><br>

                    <label for="seuil_max">Seuil Max:</label>
                    <input type="number" id="seuil_max" name="seuil_max" required><br>
                <?php endif; ?>
            </div>

            <button type="submit" class="bouton-alerte">Ajouter</button>
        </form>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_last_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ topic: '<?php echo htmlspecialchars($topic); ?>' })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse reçue:', data); // Ajoutez cette ligne pour afficher la réponse
        let messageContainer = document.getElementById('message-container');
        if (data.success) {
            try {
                let jsonMessage = JSON.parse(data.message);
                if (Object.keys(jsonMessage).length > 0) {
                    let message = '<h2>Message JSON</h2><ul>';
                    message += parseJsonMessage(jsonMessage, '');
                    message += '</ul>';
                    messageContainer.innerHTML = message;
                } else {
                    throw new Error('Le message JSON ne contient pas de clés.');
                }
            } catch (e) {
                console.log('Le message n\'est pas au format JSON ou ne contient pas de clés.');
                messageContainer.innerHTML = '<h2>Message non JSON ou JSON sans clés</h2><p>' + data.message + '</p>';
                messageContainer.innerHTML += '<label for="unite">Unité:</label><input type="text" id="unite" name="unite" required><br>';
                messageContainer.innerHTML += '<label for="seuil_min">Seuil Min:</label><input type="number" id="seuil_min" name="seuil_min" required><br>';
                messageContainer.innerHTML += '<label for="seuil_max">Seuil Max:</label><input type="number" id="seuil_max" name="seuil_max" required><br>';
            }
        } else {
            messageContainer.innerHTML = '<h2>Erreur</h2><p>Impossible de récupérer le message.</p>';
        }
    })
    .catch((error) => {
        console.error('Erreur:', error);
        let messageContainer = document.getElementById('message-container');
        messageContainer.innerHTML = '<h2>Erreur</h2><p>Une erreur est survenue lors de la récupération du message.</p>';
    });
});

function parseJsonMessage(jsonMessage, parentKey) {
    let message = '';
    for (let key in jsonMessage) {
        let fullKey = parentKey ? parentKey + '/' + key : key;
        let encodedKey = encodeURIComponent(fullKey);

        message += '<li>' + fullKey + ': ';
        message += '<label for="unite_' + encodedKey + '">Unité:</label>';
        message += '<input type="text" id="unite_' + encodedKey + '" name="unite_' + encodedKey + '" required><br>';
        message += '<label for="seuil_min_' + encodedKey + '">Seuil Min:</label>';
        message += '<input type="number" id="seuil_min_' + encodedKey + '" name="seuil_min_' + encodedKey + '" required><br>';
        message += '<label for="seuil_max_' + encodedKey + '">Seuil Max:</label>';
        message += '<input type="number" id="seuil_max_' + encodedKey + '" name="seuil_max_' + encodedKey + '" required><br>';
        message += '</li>';
        message += '<hr>'; // Ajout d'une ligne horizontale pour séparer les clés
    }
    return message;
}
</script>
</body>
</html>