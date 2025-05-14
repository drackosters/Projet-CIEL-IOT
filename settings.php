<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté ou pas admin
if (
    !isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true ||
    $_SESSION['type_utilisateur'] !== 'admin'
) {
    header("Location: Connexion.php");
    exit();
}

// Récupération du nom depuis session ou cookie
$nom_utilisateur = htmlspecialchars($_SESSION['login_admin'] ?? $_COOKIE['nom_utilisateur'] ?? "Administrateur inconnu");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres</title>
    <link rel="icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="page_administrateur.css?v=7">
    <script src="page_utilisateur.js" defer></script>
</head>
<body>

<div class="conteneur-haut">
    <!-- Logo -->
    <a href="index.php">
        <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
    </a>

    <!-- Titre -->
    <h1 class="titre-iot">Paramètres</h1>

    <!-- Zone utilisateur -->
    <div class="conteneur-utilisateur">
        <!-- Utilisateur -->
        <button class="bouton-utilisateur" onclick="togglePanneauDeconnexion()">
            <?= htmlspecialchars($nom_utilisateur) ?>
        </button>

        <!-- Panneau de déconnexion -->
        <div id="panneau-deconnexion" class="panneau-deconnexion">
            <form action="deconnexion.php" method="post">
                <button type="submit" name="deconnexion" class="bouton-deconnexion">Déconnexion</button>
            </form>
        </div>
    </div>
</div>

<div class="conteneur-principal">
    <h2>Gestion des Paramètres</h2>
    <div class="boutons-container">
        <button class="bouton-action bouton-creationIoT" 
                onclick="ouvrirIframe('creation_iot.php')">Création d'un IoT</button>
        <button class="bouton-action bouton-creationTopic" 
                onclick="ouvrirIframe('topics.php')">Ajout d'un Topic</button>
    </div>
    <div class="boutons-container">
        <button class="bouton-action bouton-modification" 
                onclick="ouvrirIframe('modif_iot.php')">Modification d'un IoT</button>
        <button class="bouton-action bouton-suppression" 
                onclick="ouvrirIframe('suppression.php')">Suppression d'un IoT</button>
    </div>
    <div class="boutons-container">
        <button class="bouton-action bouton-gestionUtilisateur" 
                onclick="ouvrirIframe('création_utilisateur.php')">Création Utilisateur</button>
    </div>
</div>

<!-- Iframe -->
<div class="iframe-container" id="iframeContainer">
    <button class="fermer" onclick="fermerIframe()">Fermer</button>
    <iframe id="iframe" src=""></iframe>
</div>

<script>
    function ouvrirIframe(url) {
        const iframeContainer = document.getElementById('iframeContainer');
        const iframe = document.getElementById('iframe');
        iframe.src = url;
        iframeContainer.style.display = 'flex';
    }

    function fermerIframe() {
        const iframeContainer = document.getElementById('iframeContainer');
        const iframe = document.getElementById('iframe');
        iframe.src = '';
        iframeContainer.style.display = 'none';
    }
</script>

</body>
</html>