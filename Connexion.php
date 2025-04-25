<?php
session_start();

//connexion BDD
require 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données
//définir les variables de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['connexion'])) {
    $login = $_POST['login'];
    $mot_de_passe = $_POST['pswd'];

    // Fonction pour vérifier la connexion
    function verifierConnexion($conn, $table, $login, $mot_de_passe, $type) {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE nom = ?");
        $stmt->execute([$login]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && $utilisateur['mots_de_passe'] == $mot_de_passe) {
            $_SESSION['utilisateur_connecte'] = true;
            $_SESSION['Utilisateur'] = $utilisateur['nom'];
            $_SESSION['type_utilisateur'] = $type;
            header("Location: page_utilisateur.php");

                    // Stocker un cookie qui expire dans 1 jour
            setcookie("nom_utilisateur", $utilisateur['nom'], time() + 86400, "/");
            exit(); //86400
        }
        return false;
    }

    // Vérifier si administrateur
    if (!verifierConnexion($conn, 'administrateur', $login, $mot_de_passe, 'admin')) {
        // Si ce n'est pas un administrateur, vérifier l'utilisateur
        if (!verifierConnexion($conn, 'Utilisateur', $login, $mot_de_passe, 'utilisateur')) {
            $erreur_connexion = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Connexion.css">
    <script src="Connexion.js"></script>
</head>
<body>
    <div class="container">

        <h2>Centre de gestion des IoT</h2>
        <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        <h2>Connexion</h2>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>
        " style="display: flex; flex-direction: column; width: 300px; margin: auto;">

            <input type="text" name="login" placeholder="Nom d'utilisateur ou adresse e-mail"
             required style="padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">

            <input type="password" name="pswd" placeholder="Mot de passe"
             required style="padding: 10px; margin-bottom: 30px; border: 1px solid #ccc; border-radius: 5px;">

            <button type="submit" name="connexion">Se connecter</button>
            <?php if (isset($erreur_connexion)) : ?>
                <p style="color: red;"><?php echo $erreur_connexion; ?></p>
            <?php endif; ?>
        </form>
        </div>

        <!-- Pop-up de consentement pour les cookies -->
<div id="cookie-popup" class="cookie-popup">
    <p>Ce site utilise des cookies pour stocker vos informations. Acceptez-vous de stocker vos données d'utilisateur ?</p>
    <button onclick="handleCookies(true)">Oui</button>
    <button onclick="handleCookies(false)">Non</button>
</div>
        
</body>
</html>