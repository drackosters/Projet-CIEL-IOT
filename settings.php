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

// Récupération du nom de l'utilisateur depuis la session
$nom_utilisateur = htmlspecialchars($_SESSION['nom_utilisateur'] ?? 'Administrateur');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/logo.png" type="image/png">
    <title>Paramètres</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
        }

        .conteneur-haut {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 40px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            height: 50px;
        }

        .titre-iot {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
        }

        .bouton-retour {
            background: none;
            border: 2px solid #ffffff;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            margin-right: 10px;
        }

        .bouton-retour:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .bouton-utilisateur {
            background: none;
            border: 2px solid #ffffff;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .bouton-utilisateur:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <!-- Barre du haut -->
    <div class="conteneur-haut">
        <a href="page_administrateur.php">
            <button class="bouton-retour">← Retour</button>
        </a>
        <h1 class="titre-iot">Paramètres</h1>
        <button class="bouton-utilisateur">
            <?= $nom_utilisateur ?>
        </button>
    </div>

    <!-- Contenu principal -->
    <div class="conteneur-principal">
        <!-- ...contenu existant... -->
    </div>

    <!-- Texte en bas -->
    <div class="texte-bas" id="texteBas"></div>

    <!-- Iframe -->
    <div class="iframe-container" id="iframeContainer">
        <button class="fermer" onclick="fermerIframe()">Fermer</button>
        <iframe id="iframe" src=""></iframe>
    </div>

    <script>
        function afficherTexteBas(texte) {
            const texteBas = document.getElementById('texteBas');
            texteBas.innerHTML = texte;
            texteBas.style.opacity = '1';
        }

        function cacherTexteBas() {
            const texteBas = document.getElementById('texteBas');
            texteBas.style.opacity = '0';
        }

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