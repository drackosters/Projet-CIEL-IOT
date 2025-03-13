<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="stylesheet" href="page_utilisateur.css">
</head>
<body>

    <div class="conteneur-haut">
        <h1 class="titre-iot">Gestion des IoT</h1>
        <p class="nom-utilisateur">nom d'utilisateur</p>
        <button class="bouton-alerte" onclick="toggleConteneur()"></button>
    </div>

    <div id="conteneur-droit" class="conteneur-droit">
        <p>Gestion des alertes</p>
    </div>

    <script>
        function toggleConteneur() {
            var conteneurDroit = document.getElementById('conteneur-droit');
            conteneurDroit.classList.toggle('ouvert'); // Utilise toggle pour ajouter/supprimer la classe
        }
    </script>

    </body>
</html>
