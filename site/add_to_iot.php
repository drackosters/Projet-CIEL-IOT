<?php
require 'config.php';

$topic = $_GET['topic'] ?? '';

// Récupérer la liste des IoT existants
$sql = "SELECT ID, Nom FROM IOT";
$stmt = $conn->prepare($sql);
$stmt->execute();
$iotList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le dernier message pour le topic
$lastMessage = null;
$isJson = false;
$jsonKeys = [];
$jsonValues = [];
$messageString = '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'get_last_message.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['topic' => $topic]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['message'])) {
        $lastMessage = $data['message'];
        $decodedMessage = json_decode($lastMessage, true);
        $isJson = (json_last_error() == JSON_ERROR_NONE);
        $messageString = $lastMessage;
        if ($isJson) {
            $jsonKeys = array_keys($decodedMessage);
            $jsonValues = array_values($decodedMessage);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iotId = $_POST['iot_id'];
    $baseTopic = $topic;

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
                $sql = "INSERT INTO TOPICS (Nom, Unite, Seuil_MIN, Seuil_MAX) VALUES (:topic, :unite, :seuil_min, :seuil_max)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':topic', $fullTopic);
                $stmt->bindParam(':unite', $unite);
                $stmt->bindParam(':seuil_min', $seuil_min);
                $stmt->bindParam(':seuil_max', $seuil_max);

                if (!$stmt->execute()) {
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
    <title>Ajouter un topic à un IoT existant</title>
</head>
<body>
    <h1>Ajouter un topic à un IoT existant</h1>
    <form method="POST" action="add_to_iot.php?topic=<?php echo urlencode($topic); ?>">
        <label for="iot_id">Sélectionnez un IoT:</label>
        <select id="iot_id" name="iot_id" required>
            <?php foreach ($iotList as $iot): ?>
                <option value="<?php echo htmlspecialchars($iot['ID']); ?>"><?php echo htmlspecialchars($iot['Nom']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <div id="message-container">
            <?php if ($isJson): ?>
                <h2>Message JSON</h2>
                <ul id="json-keys-container"></ul>
            <?php else: ?>
                <h2>Message non JSON</h2>
                <p><?php echo htmlspecialchars($messageString); ?></p>
                <label for="unite">Unité:</label>
                <input type="text" id="unite" name="unite" required><br>

                <label for="seuil_min">Seuil Min:</label>
                <input type="number" id="seuil_min" name="seuil_min" required><br>

                <label for="seuil_max">Seuil Max:</label>
                <input type="number" id="seuil_max" name="seuil_max" required><br>
            <?php endif; ?>
        </div>

        <button type="submit">Ajouter</button>
    </form>

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
                    let message = '<h2>Message JSON</h2><ul>';
                    message += parseJsonMessage(jsonMessage, '');
                    message += '</ul>';
                    messageContainer.innerHTML = message;
                } catch (e) {
                    console.log('Le message n\'est pas au format JSON.');
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
        }
        return message;
    }
    </script>
</body>
</html>