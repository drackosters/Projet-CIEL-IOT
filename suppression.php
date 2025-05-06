<?php
require 'config.php';

// Récupérer la liste des IoT existants
$sql = "SELECT id, Nom FROM IoT";
$stmt = $conn->prepare($sql);
$stmt->execute();
$iotList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Supprimer un IoT et ses topics liés
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iot_id'])) {
    $iotId = $_POST['iot_id'];

    try {
        // Supprimer les topics liés à l'IoT
        $sql = "DELETE FROM TOPICS WHERE id IN (SELECT id_topic FROM IoT_Topics WHERE id_IoT = :iot_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':iot_id', $iotId);
        $stmt->execute();

        // Supprimer les relations dans IoT_Topics
        $sql = "DELETE FROM IoT_Topics WHERE id_IoT = :iot_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':iot_id', $iotId);
        $stmt->execute();

        // Supprimer l'IoT
        $sql = "DELETE FROM IoT WHERE id = :iot_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':iot_id', $iotId);
        $stmt->execute();

        echo "<script>alert('IoT et ses topics liés ont été supprimés avec succès.');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Erreur lors de la suppression : " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un IoT</title>
    <link rel="stylesheet" href="topics.css">
</head>
<body>
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="image/logo.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Supprimer un IoT</h1>
    </div>

    <div class="conteneur-principal">
        <form method="POST" action="suppression.php" class="formulaire">
            <label for="iot_id">Sélectionnez un IoT à supprimer :</label>
            <select id="iot_id" name="iot_id" required>
                <?php foreach ($iotList as $iot): ?>
                    <option value="<?php echo htmlspecialchars($iot['id']); ?>">
                        <?php echo htmlspecialchars($iot['Nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <button type="submit" class="bouton-alerte">Supprimer</button>
        </form>
    </div>
</body>
</html>