<?php
session_start();
require 'config.php';

// Redirection si l'utilisateur n'est pas connecté ou pas admin
if (
    !isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true ||
    $_SESSION['type_utilisateur'] !== 'admin'
) {
    header("Location: Connexion.php");
    exit();
}

// Récupération du nom depuis session ou cookie
$nom_utilisateur = htmlspecialchars($_SESSION['login_admin'] ?? $_COOKIE['nom_utilisateur'] ?? "Administrateur inconnu");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des IoT</title>
    <link rel="icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="page_administrateur.css?v=7">
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

        <!-- Bouton paramètres -->
        <a href="settings.php" class="bouton-parametres" title="Paramètres">
            <img src="image/paramètre.png" alt="Paramètres">
        </a>


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
let chartInstance = null;
const PRIX_KWH = 0.2016; // Prix en €/kWh (France, mai 2025)

function fetchAndUpdateChart() {
    const spinner = document.getElementById('spinner');
    if (spinner) spinner.style.display = 'block';

    fetch(`data.php`)
        .then(res => res.json())
        .then(data => {
            const canvas = document.getElementById('myChart');
            const ctx = canvas.getContext('2d');

            if (!Array.isArray(data) || data.length === 0) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '18px Arial';
                ctx.fillStyle = 'gray';
                ctx.textAlign = 'center';
                ctx.fillText("Aucune donnée n'a pu être récupérée", canvas.width / 2, canvas.height / 2);
                if (chartInstance) chartInstance.destroy();
                chartInstance = null;
                ajouterAlerte("⚠️ Aucune donnée n'a pu être récupérée depuis InfluxDB.");
                document.getElementById('cout-energetique').textContent = '0.00';
                return;
            }

            // 📅 Récupérer la date la plus récente
            const latestDate = new Date(Math.max(...data.map(p => new Date(p.time))));
            const latestDateStr = latestDate.toISOString().split('T')[0];

            // 🧹 Ne conserver que les données de cette date
            const filteredData = data.filter(p => p.time.startsWith(latestDateStr));

            // ⏱️ Supprimer les données dans le futur (par rapport à l’heure actuelle locale)
            const now = new Date();
            const nowHour = now.getHours();
            const nowDateStr = now.toISOString().split('T')[0];

            const dataOfToday = filteredData.filter(p => {
                const date = new Date(p.time);
                const hour = date.getHours();
                const dateStr = date.toISOString().split('T')[0];
                return dateStr === nowDateStr && hour <= nowHour;
            });

            if (dataOfToday.length === 0 || dataOfToday.every(p => p.value === 0 || p.value === null)) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '18px Arial';
                ctx.fillStyle = 'gray';
                ctx.textAlign = 'center';
                ctx.fillText("Aucune donnée récente disponible", canvas.width / 2, canvas.height / 2);
                if (chartInstance) chartInstance.destroy();
                chartInstance = null;
                ajouterAlerte("⚠️ Aucune donnée disponible pour aujourd’hui.");
                document.getElementById('cout-energetique').textContent = '0.00';
                return;
            }

            // ⏳ Regrouper les données par heure de la journée
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

            // 💰 Calcul du coût énergétique
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

function ajouterAlerte(message) {
    const container = document.getElementById('message-alerte');
    if (!Array.from(container.children).some(p => p.textContent === message)) {
        const p = document.createElement('p');
        p.textContent = message;
        p.style.color = 'red';
        p.style.cursor = 'pointer';
        p.addEventListener('click', () => {
            p.remove();
            const hasAlerts = container.children.length === 0;
            const boutonAlerte = document.getElementById('bouton-alerte');
            if (boutonAlerte && hasAlerts) {
                boutonAlerte.style.backgroundImage = "url('/Projet-CIEL-IOT/image/notification_1.png')";
            }
        });
        container.appendChild(p);
    }
}

function toggleConteneur() {
    console.log("toggleConteneur appelé");
    const conteneurDroit = document.getElementById('conteneur-droit');
    if (conteneurDroit) {
        conteneurDroit.classList.toggle('ouvert');
        console.log("Classe 'ouvert' pour conteneur-droit :", conteneurDroit.classList.contains('ouvert'));
    }
}

function toggleAjoutIot() {
    console.log("toggleAjoutIot appelé");
    const conteneurAjout = document.getElementById('conteneur-ajout-iot');
    if (conteneurAjout) {
        conteneurAjout.classList.toggle('ouvert');
        console.log("Classe 'ouvert' pour conteneur-ajout-iot :", conteneurAjout.classList.contains('ouvert'));
    } else {
        console.error("conteneur-ajout-iot introuvable dans le DOM");
    }
}

// Ajout de l'écouteur pour la case à cocher
document.addEventListener('DOMContentLoaded', () => {
    const checkboxEnergie = document.getElementById('checkbox-energie');
    const graphiqueEnergie = document.querySelector('.cadre-graph1');

    if (checkboxEnergie && graphiqueEnergie) {
        // Initialisation : le graphique est visible si la case est cochée
        graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';

        // Écouteur pour les changements
        checkboxEnergie.addEventListener('change', () => {
            graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';
            console.log("Graphique 'Consommateur d'énergie' :", checkboxEnergie.checked ? "visible" : "masqué");
        });
    } else {
        console.error("checkbox-energie ou cadre-graph1 introuvable dans le DOM");
    }
});

// Initialisation
fetchAndUpdateChart();
setInterval(fetchAndUpdateChart, 30000);

fetchAlertes();
setInterval(fetchAlertes, 10000);
</script>

</body>
</html>