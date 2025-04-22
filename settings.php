<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Barre du haut */
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

        /* Contenu principal */
        .conteneur-principal {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 80%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-top: 100px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }

        .boutons-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .bouton-action {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            color: #fff;
            text-transform: uppercase;
            width: 400px;
        }

        .bouton-creationIoT {
            background: linear-gradient(45deg, #F44336, #E57373);
        }

        .bouton-modification {
            background: linear-gradient(45deg, #2196F3, #64B5F6);
        }

        .bouton-suppression {
            background: linear-gradient(45deg, #FF9800, #FFB74D);
        }

        .bouton-creationTopic {
            background: linear-gradient(45deg, #4CAF50, #66BB6A);
        }

        .bouton-action:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* Styles pour l'iframe */
        .iframe-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(121, 120, 120, 0.9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            z-index: 1001;
        }

        .iframe-container iframe {
            width: calc(75% - 50px);
            height: calc(100% - 50px); /* Ajuste la hauteur pour laisser de la place au bouton "Fermer" */
            border: none;
        }

        .iframe-container .fermer {
            background: #ff4d4d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }

        .iframe-container .fermer:hover {
            background: #ff1a1a;
        }
    </style>
</head>
<body>
    <!-- Barre du haut -->
    <div class="conteneur-haut">
        <a href="index.php">
            <img src="297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Paramètres</h1>
        <button class="bouton-utilisateur">
            <?php echo htmlspecialchars($_SESSION['nom_utilisateur'] ?? 'Utilisateur'); ?>
        </button>
    </div>

    <!-- Contenu principal -->
    <div class="conteneur-principal">
        <h2>Gestion des IoT</h2>
        <div class="boutons-container">
            <button class="bouton-action bouton-creationIoT" onclick="ouvrirIframe('Création d\'un IoT')">Création d'un IoT</button>
            <button class="bouton-action bouton-creationTopic" onclick="ouvrirIframe('topics.php')">Ajout d'un Topic</button>
        </div>
        <div class="boutons-container">
            <button class="bouton-action bouton-modification" onclick="ouvrirIframe('Modification d\'un IoT')">Modification d'un IoT</button>
            <button class="bouton-action bouton-suppression" onclick="ouvrirIframe('Suppression d\'un IoT')">Suppression d'un IoT</button>
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
            
            // Mettre à jour la source de l'iframe
            iframe.src = url;
            
            // Afficher le conteneur
            iframeContainer.style.display = 'flex';
        }

        function fermerIframe() {
            const iframeContainer = document.getElementById('iframeContainer');
            const iframe = document.getElementById('iframe');
            
            // Masquer le conteneur et réinitialiser la source de l'iframe
            iframeContainer.style.display = 'none';
            iframe.src = '';
        }
    </script>
</body>
</html>