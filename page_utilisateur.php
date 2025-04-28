<?php
session_start();

// Fichier de config pour connexion BDD
require 'config.php';

//Retour sur la page de connexion si utilisateur non connecter dans la session
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
    <script src="page_utilisateur.js"></script>
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

<h2>Consommation (apower) - Dernière heure</h2>
  <canvas id="myChart" width="800" height="400"></canvas>

  <script>
    fetch('data.php')
      .then(response => response.json())
      .then(data =>
       { 
        console.log(data);
        const labels = data.map(point => new Date(point.time).toLocaleTimeString());
        const values = data.map(point => point.value);

        new Chart(document.getElementById('myChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'apower (W)',
              data: values,
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.3,
              fill: false
            }]
          },
          options: {
            responsive: true,
            scales: {
              x: {
                title: { display: true, text: 'Heure' }
              },
              y: {
                title: { display: true, text: 'Puissance (W)' }
              }
            }
          }
        });
      });
  </script>

</body>
</html>
