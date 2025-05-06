<?php
require 'config.php';

// Récupérer la liste des IoT existants
$sql = "SELECT id, Nom FROM IoT";
$stmt = $conn->prepare($sql);
$stmt->execute();
$iotList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer l'ID de l'IoT depuis l'URL ou le formulaire
$iotId = $_GET['iot_id'] ?? $_POST['iot_id'] ?? null;

if ($iotId) {
    // Récupérer les topics associés à l'IoT
    $sql = "SELECT t.id, t.topic, t.unite, t.Seuil_MIN, t.Seuil_MAX 
            FROM TOPICS t
            INNER JOIN IoT_Topics it ON t.id = it.id_topic
            WHERE it.id_IoT = :iot_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':iot_id', $iotId, PDO::PARAM_INT);
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mettre à jour les topics si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topics'])) {
        foreach ($_POST['topics'] as $topicId => $data) {
            $unite = $data['unite'];
            $seuil_min = $data['seuil_min'];
            $seuil_max = $data['seuil_max'];

            $sql = "UPDATE TOPICS 
                    SET unite = :unite, Seuil_MIN = :seuil_min, Seuil_MAX = :seuil_max 
                    WHERE id = :topic_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':unite', $unite);
            $stmt->bindParam(':seuil_min', $seuil_min);
            $stmt->bindParam(':seuil_max', $seuil_max);
            $stmt->bindParam(':topic_id', $topicId, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                echo "Erreur lors de la mise à jour du topic ID $topicId.<br>";
            }
        }

        echo "Topics mis à jour avec succès.";
        header("Refresh: 2; url=modif_iot.php?iot_id=$iotId");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les topics d'un IoT</title>
    <link rel="stylesheet" href="topics.css">
</head>
<body>
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="image/logo.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Modifier les topics d'un IoT</h1>
    </div>

    <div class="conteneur-principal">
        <?php if (!$iotId): ?>
            <!-- Formulaire de sélection de l'IoT -->
            <form method="GET" action="" class="formulaire">
                <label for="iot_id">Sélectionnez un IoT:</label>
                <select id="iot_id" name="iot_id" required>
                    <option value="">-- Sélectionnez un IoT --</option>
                    <?php foreach ($iotList as $iot): ?>
                        <option value="<?php echo htmlspecialchars($iot['id']); ?>">
                            <?php echo htmlspecialchars($iot['Nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bouton-alerte">Valider</button>
            </form>
        <?php else: ?>
            <!-- Formulaire de modification des topics -->
            <form method="POST" action="">
                <?php if (!empty($topics)): ?>
                    <?php foreach ($topics as $topic): ?>
                        <div class="topic-item">
                            <h3>Topic: <?php echo htmlspecialchars($topic['topic']); ?></h3>
                            <label for="unite_<?php echo $topic['id']; ?>">Unité:</label>
                            <input type="text" id="unite_<?php echo $topic['id']; ?>" name="topics[<?php echo $topic['id']; ?>][unite]" value="<?php echo htmlspecialchars($topic['unite']); ?>" required><br>

                            <label for="seuil_min_<?php echo $topic['id']; ?>">Seuil Min:</label>
                            <input type="number" id="seuil_min_<?php echo $topic['id']; ?>" name="topics[<?php echo $topic['id']; ?>][seuil_min]" value="<?php echo htmlspecialchars($topic['Seuil_MIN']); ?>" required><br>

                            <label for="seuil_max_<?php echo $topic['id']; ?>">Seuil Max:</label>
                            <input type="number" id="seuil_max_<?php echo $topic['id']; ?>" name="topics[<?php echo $topic['id']; ?>][seuil_max]" value="<?php echo htmlspecialchars($topic['Seuil_MAX']); ?>" required><br>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                    <input type="hidden" name="iot_id" value="<?php echo htmlspecialchars($iotId); ?>">
                    <button type="submit" class="bouton-alerte">Mettre à jour</button>
                <?php else: ?>
                    <p>Aucun topic trouvé pour cet IoT.</p>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>