<?php
session_start();
require 'config.php';

if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true) {
    header("Location: Connexion.php");
    exit();
}

$nom_utilisateur = htmlspecialchars($_COOKIE['nom_utilisateur'] ?? "Utilisateur inconnu");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="page_utilisateur.css?v=40"> <!-- Incrémenter la version pour forcer le rechargement -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="page_utilisateur.js" defer></script>
</head>
<body>

<div class="conteneur-haut">

    <!-- Logo -->
    <a href="https://www.citeconnect.com/citecaas/" target="_blank">
        <img src="image/297f7e763fcbb4896d13120c4c8e3a2b365880689c0e614028de1f3637e0852d.png" alt="Logo" class="logo">
    </a>

    <!-- Titre -->
    <h1 class="titre-iot">Gestion des IoT</h1>

    <!-- Zone utilisateur -->
    <div class="conteneur-utilisateur">
        <!-- Utilisateur -->
        <button class="bouton-utilisateur" onclick="togglePanneauDeconnexion()">
            <?= htmlspecialchars($nom_utilisateur) ?>
        </button>

        <!-- Bouton pour ouvrir le panneau de sélection de graphique -->
        <button class="bouton-ajout-iot">
            <img src="image/ajout_iot.png" alt="Sélectionner un graphique" class="icone-ajout-iot">
        </button>

        <!-- Panneau de sélection de graphique -->
        <div id="conteneur-ajout-iot" class="conteneur-iot">
            <p>Sélectionner un graphique</p>
            <div id="message-ajout-iot">
                <label>
                    <input type="checkbox" name="graphique" value="energie" id="checkbox-energie" checked> Consommateur d'énergie
                </label>
            </div>
        </div>

        <!-- Cloche notification -->
        <button id="bouton-alerte" class="bouton-alerte" onclick="toggleConteneur()"></button>

        <!-- Panneau de déconnexion -->
        <div id="panneau-deconnexion" class="panneau-deconnexion">
            <form action="deconnexion.php" method="post">
                <button type="submit" name="deconnexion" class="bouton-deconnexion">Déconnexion</button>
            </form>
        </div>
    </div>
</div>

<div id="conteneur-droit" class="conteneur-droit">
    <p>Gestion des alertes</p>
    <div id="message-alerte"></div>
</div>

<div class="cadre-graph1">
    <div class="titre-graphique">Consommation d'énergie</div>
    <div class="cout-energetique">
        Coût estimé : <span id="cout-energetique">0.00</span> €
    </div>
    <div class="conteneur-canvas">
        <canvas id="myChart"></canvas>
    </div>
</div>

<script>
    function fetchAndUpdateChart() {
        const checkboxEnergie = document.getElementById('checkbox-energie');
        if (!checkboxEnergie || !checkboxEnergie.checked) {
            return; // Ne pas récupérer les données si le graphique est masqué
        }

        const spinner = document.getElementById('spinner');
        if (spinner) spinner.style.display = 'block';

        fetch(`data.php`)
            .then(res => res.json())
            .then(data => {
                const canvas = document.getElementById('myChart');
                const ctx = canvas.getContext('2d');

                if (!Array.isArray(data) || data.length === 0 || !data.every(p => p.time && typeof p.value === 'number')) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.font = '18px Arial';
                    ctx.fillStyle = 'gray';
                    ctx.textAlign = 'center';
                    ctx.fillText("Données invalides ou manquantes", canvas.width / 2, canvas.height / 2);
                    if (chartInstance) chartInstance.destroy();
                    chartInstance = null;
                    ajouterAlerte("⚠️ Données invalides reçues depuis InfluxDB.");
                    document.getElementById('cout-energetique').textContent = '0.00';
                    return;
                }

                const labels = data.map(p => new Date(p.time).toLocaleTimeString());
                const values = data.map(p => p.value);

                let totalKWh = 0;
                for (let i = 1; i < data.length; i++) {
                    const timeDiffHours = (new Date(data[i].time) - new Date(data[i-1].time)) / 1000 / 3600;
                    if (timeDiffHours > 0.1) continue; // Ignore les écarts > 6 minutes
                    const avgPowerWatts = (data[i].value + data[i-1].value) / 2;
                    totalKWh += (avgPowerWatts * timeDiffHours) / 1000;
                }
                const cout = (totalKWh * PRIX_KWH).toFixed(2);
                document.getElementById('cout-energetique').textContent = cout;

                if (chartInstance) {
                    chartInstance.data.labels = labels;
                    chartInstance.data.datasets[0].data = values;
                    chartInstance.update();
                } else {
                    chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
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
                                x: { title: { display: true, text: 'Heure' } },
                                y: { title: { display: true, text: 'Puissance (W)' } }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                document.getElementById('message-alerte').innerHTML = '';
                ajouterAlerte("⚠️ Erreur lors de la récupération des données : " + error.message);
                console.error("Erreur de fetch data.php :", error);
                document.getElementById('cout-energetique').textContent = '0.00';
            })
            .finally(() => {
                if (spinner) spinner.style.display = 'none';
            });
    }
</script>

</body>
</html>