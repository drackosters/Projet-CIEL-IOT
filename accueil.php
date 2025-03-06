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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="stylesheet" href="accueil.css">
</head>
<body>
    <div class="container">
        <div class="header">Gestion des IoT</div>
        <div class="content">
            <div class="iot-grid">
                <div class="iot-item">IOT 1</div>
                <div class="iot-item">IOT 2</div>
                <div class="iot-item">IOT 3</div>
            </div>
            
            <div class="alertes">
                <div class="alertes-header">Alertes</div>
                <div class="alertes-item">Alerte 1</div>
                <div class="alertes-item">Alerte 2</div>
            </div>
        </div>
    </div>
</body>
</html>