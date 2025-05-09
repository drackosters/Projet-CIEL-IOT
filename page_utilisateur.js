document.addEventListener("DOMContentLoaded", function () {
    const PRIX_KWH = 0.2016; // Prix en €/kWh (France, mai 2025)
    let chartInstance = null;

    // Initialisation
    observerConteneur();
    observerAjoutIot();
    fermerAjoutIotSiClickExterieur();
    attacherEvenements();
    initialiserGraphique();

    // Rendre les fonctions accessibles globalement
    window.togglePanneauDeconnexion = togglePanneauDeconnexion;
    window.toggleConteneur = toggleConteneur;
    window.toggleAjoutIot = toggleAjoutIot;
    window.fetchAndUpdateChart = fetchAndUpdateChart;
    window.fetchAlertes = fetchAlertes;

    function fetchAlertes() {
        const checkboxEnergie = document.getElementById('checkbox-energie');
        const energieActive = checkboxEnergie ? checkboxEnergie.checked : false;
        
        fetch(`alerte.php?energie_active=${energieActive ? 1 : 0}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('message-alerte');
                container.innerHTML = '';

                if (data.error) {
                    ajouterAlerte("⚠️ " + data.error);
                    return;
                }

                if (!Array.isArray(data)) {
                    ajouterAlerte("⚠️ Format inattendu reçu depuis alerte.php");
                    console.error("Donnée reçue :", data);
                    return;
                }

                data.forEach(ajouterAlerte);
            })
            .catch(error => {
                const container = document.getElementById('message-alerte');
                container.innerHTML = '';
                ajouterAlerte("⚠️ Erreur JS ou réseau : " + error.message);
                console.error("Erreur fetch alertes :", error);
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

    function togglePanneauDeconnexion() {
        const panneauDeconnexion = document.getElementById('panneau-deconnexion');
        if (panneauDeconnexion) {
            panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
        }
    }

    function toggleConteneur() {
        const conteneurDroit = document.getElementById('conteneur-droit');
        if (conteneurDroit) {
            conteneurDroit.classList.toggle('ouvert');
        }
    }

    function toggleAjoutIot() {
        const conteneurAjout = document.getElementById('conteneur-ajout-iot');
        if (conteneurAjout) {
            conteneurAjout.classList.toggle('ouvert');
        }
    }

    function observerConteneur() {
        const conteneur = document.getElementById('conteneur-droit');
        const boutonAlerte = document.getElementById('bouton-alerte');
        if (conteneur && boutonAlerte) {
            const observer = new MutationObserver(mutations => {
                const messageAlerte = conteneur.querySelector('#message-alerte');
                if (messageAlerte) {
                    const hasAlerts = messageAlerte.children.length > 0;
                    boutonAlerte.style.backgroundImage = hasAlerts
                        ? "url('/Projet-CIEL-IOT/image/notification_2.png')"
                        : "url('/Projet-CIEL-IOT/image/notification_1.png')";
                }
            });
            observer.observe(conteneur, { childList: true, subtree: true });
        } else {
            console.warn("conteneur-droit ou bouton-alerte est introuvable dans le DOM.");
        }
    }

    function observerAjoutIot() {
        const conteneur = document.getElementById('conteneur-ajout-iot');
        const boutonAjout = document.querySelector('.bouton-ajout-iot');
        if (conteneur && boutonAjout) {
            console.log("observerAjoutIot actif");
        } else {
            console.warn("conteneur-ajout-iot ou bouton-ajout-iot est introuvable dans le DOM.");
        }
    }

    function fermerAjoutIotSiClickExterieur() {
        document.addEventListener('click', function (event) {
            const panneau = document.getElementById('conteneur-ajout-iot');
            const boutonAjout = document.querySelector('.bouton-ajout-iot');
            if (!panneau || !boutonAjout) return;
            if (panneau.contains(event.target) || boutonAjout.contains(event.target)) {
                return;
            }
            if (panneau.classList.contains('ouvert')) {
                panneau.classList.remove('ouvert');
            }
        });
    }

    function attacherEvenements() {
        const boutonAjout = document.querySelector('.bouton-ajout-iot');
        if (boutonAjout) {
            boutonAjout.addEventListener('click', toggleAjoutIot);
        } else {
            console.warn("Bouton .bouton-ajout-iot non trouvé");
        }
    }

    function initialiserGraphique() {
        const checkboxEnergie = document.getElementById('checkbox-energie');
        const graphiqueEnergie = document.querySelector('.cadre-graph1');
        if (checkboxEnergie && graphiqueEnergie) {
            graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';
            checkboxEnergie.addEventListener('change', () => {
                graphiqueEnergie.style.display = checkboxEnergie.checked ? 'block' : 'none';
                console.log("Graphique 'Consommateur d'énergie' :", checkboxEnergie.checked ? "visible" : "masqué");
                fetchAlertes(); // Mettre à jour les alertes lorsque la case change
                if (checkboxEnergie.checked) {
                    fetchAndUpdateChart();
                }
            });
        } else {
            console.error("checkbox-energie ou cadre-graph1 introuvable dans le DOM");
        }
        fetchAndUpdateChart();
        setInterval(fetchAndUpdateChart, 30000);
        fetchAlertes();
        setInterval(fetchAlertes, 10000);
    }
});