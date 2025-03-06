<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['identifiant'])) {
    header("Location: login.php");
    exit();
}

// Infos de connexion à la BDD
$host = 'localhost';
$dbname = 'IOT';
$username = 'root';
$password = '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <style>
        body {
            font-family: Arial, sans-serif; /* Nous indique la police utilisée pour le texte */
            display: flex; /* Permet de centrer le contenu */
            justify-content: center; /* Centrage horizontal */
            align-items: center; /* Centrage vertical */
            height: 100vh; 
            background-color: #f4f4f4; /* Couleur de fond */
            margin: 0; /* Supprime les marges par défaut */
        }
        .container {
            background-color: white; /* Fond blanc pour le conteneur */
            padding: 20px; /* Permet de selectionner l'espace intérieur */
            border: 1px solid black;
            width: 80%; /* Permet de selection la largeur */
            max-width: 900px; /* Permet de selection la largeur maximale */
        }
        .header {
            text-align: center; /*  Permet de centré le Texte */
            border: 1px solid black; 
            padding: 10px; /* Permet de selectionner l'espace intérieur */
            margin-bottom: 20px; /* Permet de selectionner l'espace sous l'en-tête */
        }
        .content {
            display: grid; 
            grid-template-columns: 2fr 1fr; /* Cette ligne permet d'avoir deux colonnes : une plus grande pour les IoT et une plus petite pour les alertes */
            gap: 20px; /* Espace entre les colonnes */
        }
        .iot-grid {
            display: grid; /* Grid pour organiser les IoT */
            grid-template-columns: repeat(2, 1fr); /*  Permert que les deux colonnes soit égales */
            gap: 20px; /* Espace entre des éléments */
        }
        .iot-item {
            border: 1px solid black; 
            padding: 30px; /* Espace intérieur */
            text-align: center; /*  Ce code permet de centré le texte */
            font-weight: bold; /* Cette ligne permet de mettre ce Texte en gras */
        }
        .iot-item:nth-child(3) {
           
            justify-self: start; /* Alignement à gauche dans la grille */
            width: 75%; /* Cette ligne permet de réduire la largeur, ce qui peremt que IOT3 soit totalment aligné à l'IOT 1 */
        }
        .alertes {
            border: 1px solid black; 
            padding: 20px; /* Espace intérieur */
        }
        .alertes-header {
            font-weight: bold; 
            text-align: center; /* Cette ligne permet de centré le texte centré */
            margin-bottom: 10px; /* Espace sous le titre */
        }
        .alertes-item {
            border: 1px solid black; 
            padding: 15px; /* Espace intérieur */
            text-align: center; /* Cette ligne permet de centré le texte centré */
            font-weight: bold; 
            margin-bottom: 10px; /* Espace entre les alertes */
        }
    </style>
</head>

</head>
<body>
    <div class="container"> <!--  ce code permet de contenir les ingormation suivante -->
        <div class="header">Gestion des IoT</div> <!-- En tête de la page -->
        <div class="content"> <!-- Ce code permet de contenir les IOT et les alerte -->
            <!-- Section IoT -->
            <div class="iot-grid">
                <div class="iot-item">IOT 1</div>
                <div class="iot-item">IOT 2</div>
                <div class="iot-item">IOT 3</div>
            </div>
            
            <!-- Section Alertes -->
            <div class="alertes">
                <div class="alertes-header">Alertes</div>
                <div class="alertes-item">Alerte 1</div>
                <div class="alertes-item">Alerte 2</div>
            </div>
        </div>
    </div>
</body>
</html>
