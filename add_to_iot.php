<?php
require 'config.php';

$topic = $_GET['topic'] ?? '';

// Récupérer la liste des IoT existants
$sql = "SELECT id, Nom FROM IoT";
$stmt = $conn->prepare($sql);
$stmt->execute();
$iotList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lastMessage = null;
$isJson = false;
$jsonKeys = [];
$jsonValues = [];
$messageString = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iotId = $_POST['iot_id'];
    $baseTopic = $topic;

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'unite_') === 0) {
            $jsonKey = urldecode(substr($key, 6));
            $unite = $_POST['unite_' . urlencode($jsonKey)] ?? '';
            $seuil_min = $_POST['seuil_min_' . urlencode($jsonKey)] ?? '';
            $seuil_max = $_POST['seuil_max_' . urlencode($jsonKey)] ?? '';
            $fullTopic = $baseTopic . '-' . $jsonKey;

            try {
                $sql = "INSERT INTO TOPICS (topic, unite, Seuil_MIN, Seuil_MAX) VALUES (:topic, :unite, :seuil_min, :seuil_max)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':topic', $fullTopic);
                $stmt->bindParam(':unite', $unite);
                $stmt->bindParam(':seuil_min', $seuil_min);
                $stmt->bindParam(':seuil_max', $seuil_max);

                if ($stmt->execute()) {
                    $topicId = $conn->lastInsertId();

                    $sql = "INSERT INTO IoT_Topics (id_IoT, id_topic) VALUES (:iot_id, :topic_id)";
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

    echo "Topics ajoutés avec succès.";
    header('Location: topics.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un topic à un IoT existant</title>
    <link rel="stylesheet" href="topics.css">
</head>
<body>
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="image/logo.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Ajouter un topic à un IoT existant</h1>
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
        <form method="POST" action="add_to_iot.php?topic=<?php echo urlencode($topic); ?>" class="formulaire">
            <label for="iot_id">Sélectionnez un IoT:</label>
            <select id="iot_id" name="iot_id" required>
                <?php foreach ($iotList as $iot): ?>
                    <option value="<?php echo htmlspecialchars($iot['id']); ?>"><?php echo htmlspecialchars($iot['Nom']); ?></option>
                <?php endforeach; ?>
            </select><br>

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
            }
        })
        .catch((error) => {
            console.error('Erreur:', error);
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

    function togglePanneauDeconnexion() {
        var panneauDeconnexion = document.getElementById('panneau-deconnexion');
        panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
    }
    </script>
</body>
</html>