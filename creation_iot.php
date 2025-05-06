<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iotName = $_POST['iot_name'];

    // Insérer le nouvel IoT dans la base de données
    $sql = "INSERT INTO IoT (nom) VALUES (:iot_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':iot_name', $iotName);

    if ($stmt->execute()) {
        $message = "Nouvel IoT ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout du nouvel IoT.";
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
        <form method="POST" action="" class="formulaire">
            <label for="iot_name">Nom de l'IoT:</label>
            <input type="text" id="iot_name" name="iot_name" required><br>
            <button type="submit" class="bouton-alerte">Ajouter</button>
        </form>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>