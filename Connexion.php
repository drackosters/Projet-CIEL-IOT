<?php
session_start(); //Démarrer la session pour stocker les informations de l'utilisateur

$servername = "44.204.178.27";
$username = "root";
$password = "";
$dbname = "plage_iot";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
  <!-- PAS TERMINER -->
  <!-- PAS TERMINER -->
  <!-- PAS TERMINER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Connexion.css">
</head>
<body>
    <div class="container">

        <h2>Centre de gestion des IoT</h2>
        <!-- logo -->
        <img src="297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">

        <!-- champ de text -->
        <h2>Connexion</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: flex; flex-direction: column; width: 300px; margin: auto;">
            <input type="email" name="email" placeholder="Nom d'utilisateur ou adresse e-mail" required 
            style="padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
            <input type="password" name="pswd" placeholder="Mot de passe" required 
            style="padding: 10px; margin-bottom: 30px; border: 1px solid #ccc; border-radius: 5px;">

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
