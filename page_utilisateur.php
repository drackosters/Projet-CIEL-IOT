<?php
session_start();

require 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true) {
    header("Location: Connexion.php");
    exit();
}

$nom_utilisateur = isset($_COOKIE['nom_utilisateur']) ? $_COOKIE['nom_utilisateur'] : "Utilisateur inconnu";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="stylesheet" href="page_utilisateur.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="conteneur-haut">

        <!-- Logo de la page qui ramène au site de citeconnect -->
        <a href="https://www.citeconnect.com/citecaas/" target="_blank">
            <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
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

<div class="centre-graphique">
    <canvas id="Graphique_IOT"></canvas>
</div>

    <script>
    const ctx = document.getElementById('Graphique_IOT');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
</body>
</html>
