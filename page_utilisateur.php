
<?php
session_start();

//Connexion à la BDD
$servername = "3.82.221.126";
$username = "admin";
$password = "admin";
$dbname = "plage_iot";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si la variable de session d'authentification est définie
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true) {
    // L'utilisateur n'est pas connecté, le rediriger vers la page de connexion
    header("Location: Connexion.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="stylesheet" href="page_utilisateur.css">
</head>
<body>

    <div class="conteneur-haut">
        <h1 class="titre-iot">Gestion des IoT</h1>
        <p class="nom-utilisateur">nom d'utilisateur</p>
        <button class="bouton-alerte" onclick="toggleConteneur()"></button>
    </div>

    <div id="conteneur-droit" class="conteneur-droit">
        <p>Gestion des alertes</p>
    </div>

    <script>
        function toggleConteneur() {
            var conteneurDroit = document.getElementById('conteneur-droit');
            conteneurDroit.classList.toggle('ouvert'); // Utilise toggle pour ajouter/supprimer la classe
        }
    </script>

    </body>
</html>
