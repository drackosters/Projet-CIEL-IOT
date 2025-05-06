<?php
require 'config.php';

// Exécuter la requête
$sql = "SELECT topic FROM unique_topics";
$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Topics</title>
    <link rel="stylesheet" href="topics.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
                width: 100%;
    height: 100%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #ffffff;
        }

        .conteneur-haut {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .logo {
            height: 50px;
        }

        .titre-iot {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }

        .conteneur-principal {
            padding: 20px;
            margin-top: 20px;
        }

        .table-container {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color:rgb(12, 12, 12);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        th {
            background-color: rgba(255, 255, 255, 0.2);
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .iframe-container {
            width: 50%;
            height: 400px;
            margin-top: 20px;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .bouton-ajouter {
            background-color:rgb(49, 0, 245);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .bouton-ajouter:hover {
            background-color:rgb(3, 1, 98);
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: red;
            background: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="conteneur-haut">
        <!-- Logo -->
        <a href="index.php">
            <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
        </a>
        <h1 class="titre-iot">Gestion des Topics</h1>
    </div>

    <div class="conteneur-principal">
        <!-- Affichage des erreurs -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <strong>Erreur :</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Table des topics -->
        <div class="table-container">
            <?php
            if ($stmt->rowCount() > 0) {
                echo "<table>";
                echo "<tr><th>Topic</th><th>Ajouter à un IoT</th></tr>";
                
                // Afficher les données
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . htmlspecialchars($row["topic"]) . "</td>";
                    echo "<td><button class='bouton-ajouter' onclick=\"handleButtonClick('" . htmlspecialchars($row["topic"]) . "')\">Ajouter</button></td></tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>Aucun résultat trouvé</p>";
            }

            // Fermer la connexion
            $conn = null;
            ?>
        </div>

        <!-- Iframe pour gestion -->
        <div class="iframe-container">
            <iframe src="gestion.html"></iframe>
        </div>
    </div>

    <script>
        function handleButtonClick(topic) {
            window.location.href = 'add_to_iot.php?topic=' + encodeURIComponent(topic);

        }
    </script>
</body>
</html>