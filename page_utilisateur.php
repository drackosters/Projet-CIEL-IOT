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
    <link rel="stylesheet" href="page_utilisateur.css?v=47">
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


<div class="conteneur-graphiques">
    <div class="cadre-graph1">
        <div class="titre-graphique">Consommation d'énergie</div>
        <div class="cout-energetique">
            Coût estimé : <span id="cout-energetique">0.00</span> €
        </div>
     <div class="conteneur-canvas">
            <canvas id="conso_elec"></canvas>
        </div>
    </div>

    <div class="cadre-graph2">
        <div class="titre-graphique-temp-humid">Température & Humidité</div>
        <div class="conteneur-canvas-temp-humid">
            <canvas id="temp_humid"></canvas>
        </div>
    </div>
</div>

<script>
let chartInstance = null;
const PRIX_KWH = 0.2016; // Prix en €/kWh (France, mai 2025)

function fetchAndUpdateChart() {
    const spinner = document.getElementById('spinner');
    if (spinner) spinner.style.display = 'block';

    fetch(`data.php`)
        .then(res => res.json())
        .then(data => {
            if (!data.apower || !Array.isArray(data.apower)) {
                throw new Error("Structure des données invalide ou vide");
            }

            // === Traitement graphique consommation élec ===
            const consoCanvas = document.getElementById('conso_elec');
            const consoCtx = consoCanvas.getContext('2d');
            const consoData = data.apower;

            const latestDate = new Date(Math.max(...consoData.map(p => new Date(p.time))));
            const latestDateStr = latestDate.toISOString().split('T')[0];
            const filteredData = consoData.filter(p => p.time.startsWith(latestDateStr));

            const now = new Date();
            const nowHour = now.getHours();
            const nowDateStr = now.toISOString().split('T')[0];

            const dataOfToday = filteredData.filter(p => {
                const date = new Date(p.time);
                return date.toISOString().split('T')[0] === nowDateStr && date.getHours() <= nowHour;
            });

            if (dataOfToday.length === 0 || dataOfToday.every(p => p.value === 0 || p.value === null)) {
                consoCtx.clearRect(0, 0, consoCanvas.width, consoCanvas.height);
                consoCtx.font = '18px Arial';
                consoCtx.fillStyle = 'gray';
                consoCtx.textAlign = 'center';
                consoCtx.fillText("Aucune donnée récente disponible", consoCanvas.width / 2, consoCanvas.height / 2);
                if (chartInstance) chartInstance.destroy();
                chartInstance = null;
                ajouterAlerte("⚠️ Aucune donnée disponible pour aujourd’hui.");
                document.getElementById('cout-energetique').textContent = '0.00';
            } else {
                const regrouped = {};
                for (let h = 0; h <= nowHour; h++) regrouped[h] = [];

                dataOfToday.forEach(p => {
                    const date = new Date(p.time);
                    const hour = date.getHours();
                    regrouped[hour].push(parseFloat(p.value));
                });

                const labels = [];
                const values = [];

                for (let h = 0; h <= nowHour; h++) {
                    labels.push(h.toString().padStart(2, '0') + 'h');
                    if (regrouped[h].length > 0) {
                        const moy = regrouped[h].reduce((sum, v) => sum + v, 0) / regrouped[h].length;
                        values.push(parseFloat(moy.toFixed(2)));
                    } else {
                        values.push(0);
                    }
                }

                let totalKWh = 0;
                for (let i = 1; i < dataOfToday.length; i++) {
                    const time1 = new Date(dataOfToday[i - 1].time);
                    const time2 = new Date(dataOfToday[i].time);
                    const diffHours = (time2 - time1) / 1000 / 3600;
                    const avgPowerW = (parseFloat(dataOfToday[i].value) + parseFloat(dataOfToday[i - 1].value)) / 2;
                    const kWh = (avgPowerW * diffHours) / 1000;
                    totalKWh += kWh;
                }

                const cout = (totalKWh * PRIX_KWH).toFixed(2);
                document.getElementById('cout-energetique').textContent = cout;

                if (chartInstance) {
                    chartInstance.data.labels = labels;
                    chartInstance.data.datasets[0].data = values;
                    chartInstance.update();
                } else {
                    chartInstance = new Chart(consoCtx, {
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
            }

            // === Mise à jour graphique température/humidité ===
            updateTempHumidChart(data.temperature || [], data.humidite || []);
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

// === Température et Humidité ===
const ctxTempHumid = document.getElementById('temp_humid').getContext('2d');
const tempHumidChart = new Chart(ctxTempHumid, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Température (°C)',
                data: [],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                yAxisID: 'y',
            },
            {
                label: 'Humidité (%)',
                data: [],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        stacked: false,
        plugins: {
            title: {
                display: true,
                text: 'Température et Humidité'
            }
        },
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Température (°C)' }
            },
            y1: {
                type: 'linear',
                position: 'right',
                grid: { drawOnChartArea: false },
                title: { display: true, text: 'Humidité (%)' }
            }
        }
    }
});

function updateTempHumidChart(tempData, humidData) {
    const labels = [];
    const tempValues = [];
    const humidValues = [];

    // Assumons que les deux tableaux sont alignés dans le temps
    for (let i = 0; i < tempData.length; i++) {
        const t = new Date(tempData[i].time);
        labels.push(t.getHours().toString().padStart(2, '0') + 'h');
        tempValues.push(parseFloat(tempData[i].value || 0));
        humidValues.push(parseFloat((humidData[i]?.value) || 0));
    }

    tempHumidChart.data.labels = labels;
    tempHumidChart.data.datasets[0].data = tempValues;
    tempHumidChart.data.datasets[1].data = humidValues;
    tempHumidChart.update();
}

// Affichage/Masquage du graphique conso
document.addEventListener('DOMContentLoaded', () => {
    const checkboxEnergie = document.getElementById('checkbox-energie');
    const graphiqueEnergie = document.querySelector('.cadre-graph1');

    if (checkboxEnergie && graphiqueEnergie) {
        graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';
        checkboxEnergie.addEventListener('change', () => {
            graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';
            console.log("Graphique 'Consommateur d'énergie' :", checkboxEnergie.checked ? "visible" : "masqué");
        });
    }
});
</script>


</body>
</html>