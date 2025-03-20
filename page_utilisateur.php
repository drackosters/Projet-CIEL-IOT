<?php
session_start();

$servername = "Adresse_IP_du_serveur";
$username = "admin";
$password = "admin";
$dbname = "plage_iot";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true) {
    header("Location: Connexion.php");
    exit();
}

$nom_utilisateur = isset($_SESSION['nom_utilisateur']) ? $_SESSION['nom_utilisateur'] : "Utilisateur inconnu";
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

        <!-- Logo de la page qui ramène au site de citeconnect -->
        <a href="https://www.citeconnect.com/citecaas/" target="_blank">
            <img src="297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        </a>

        <!-- Titre de la page -->
        <h1 class="titre-iot">Gestion des IoT</h1>

        <div class="conteneur-utilisateur">
    <button class="bouton-utilisateur" onclick="togglePanneauDeconnexion()">
        <?php echo htmlspecialchars($nom_utilisateur); ?>
    </button>
    <div id="panneau-deconnexion" class="panneau-deconnexion">
        <form action="deconnexion.php" method="post">
            <button type="submit" name="deconnexion" class="bouton-deconnexion">Déconnexion</button>
        </form>
        </div>
    </div>

    <script>
        function togglePanneauDeconnexion() {
            var panneauDeconnexion = document.getElementById('panneau-deconnexion');
            panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
        }
    </script>

        <button class="bouton-alerte" onclick="toggleConteneur()"></button>
    </div>

    <div id="conteneur-droit" class="conteneur-droit">
        <p>Gestion des alertes</p>
    </div>

    <script>
        function toggleConteneur() {
            var conteneurDroit = document.getElementById('conteneur-droit');
            conteneurDroit.classList.toggle('ouvert');
        }
    </script>
<iframe src="http://localhost:3000/d-solo/cegam49i6el1ce/apower?orgId=1&from=1742367841475&to=1742378641475&timezone=browser&panelId=1&__feature.dashboardSceneSolo" width="450" height="200" frameborder="0"></iframe>
</body>
</html>
