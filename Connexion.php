<?php
session_start();

$servername = "3.94.92.173";
$username = "admin";
$password = "admin";
$dbname = "plage_iot";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion de la connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['connexion'])) {
    $email = $_POST['login'];
    $mot_de_passe = $_POST['pswd'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE nom_utilisateur = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && $utilisateur['mot_de_passe'] == $mot_de_passe) {
        // Rediriger vers 2.php après une connexion réussie
        header("Location: topics.php");
        exit(); // exit pour arrêter le script
    } else {
        // Ajouter un message d'erreur si la connexion échoue
        $erreur_connexion = "Nom d'utilisateur ou mot de passe incorrect.";
    }
    
    // Aucune erreur n'est affichée ici, même en cas d'échec de la connexion
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Connexion.css">
</head>
<body>
    <div class="container">
        <h2>Centre de gestion des IoT</h2>
        <img src="297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        <h2>Connexion</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: flex; flex-direction: column; width: 300px; margin: auto;">
        <!-- login + CSS-->    
        <input type="text" name="login" placeholder="Nom d'utilisateur ou adresse e-mail" required style="padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
        <!-- mot de passe + CSS-->
        <input type="password" name="pswd" placeholder="Mot de passe" required style="padding: 10px; margin-bottom: 30px; border: 1px solid #ccc; border-radius: 5px;">
        
        <button type="submit" name="connexion">Se connecter</button>
            <?php if (isset($erreur_connexion)) : ?>
                <p style="color: red;"><?php echo $erreur_connexion; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>