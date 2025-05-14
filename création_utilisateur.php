<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $mots_de_passe = $_POST['mots_de_passe'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!empty($nom) && !empty($mots_de_passe) && !empty($email)) {
        try {
            $sql = "INSERT INTO Utilisateur (nom, mots_de_passe, email) VALUES (:nom, :mots_de_passe, :email)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':mots_de_passe', $mots_de_passe);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                echo "Utilisateur créé avec succès.";
                header('Location: utilisateurs.php');
                exit;
            } else {
                echo "Erreur lors de la création de l'utilisateur.";
            }
        } catch (Exception $e) {
            echo "Erreur SQL : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un utilisateur</title>
    <link rel="stylesheet" href="topics.css">
</head>
<body>
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="image/logo.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Créer un utilisateur</h1>
    </div>

    <div class="conteneur-principal">
        <form method="POST" action="creation_utilisateur.php" class="formulaire">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required><br>

            <label for="mots_de_passe">Mot de passe:</label>
            <input type="password" id="mots_de_passe" name="mots_de_passe" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <button type="submit" class="bouton-utilisateur">Créer</button>
        </form>
    </div>
</body>
</html>