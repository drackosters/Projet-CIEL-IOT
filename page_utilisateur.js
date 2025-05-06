document.addEventListener("DOMContentLoaded", function () {
    // Initialisation
    observerConteneur();
    observerAjoutIot(); // Nouvelle fonction pour observer conteneur-ajout-iot
    fermerAjoutIotSiClickExterieur();
    attacherEvenements();

    // Rendre les fonctions accessibles globalement
    window.togglePanneauDeconnexion = togglePanneauDeconnexion;
    window.toggleConteneur = toggleConteneur;
    window.toggleAjoutIot = toggleAjoutIot;

    // Fonction pour basculer le panneau de déconnexion
    function togglePanneauDeconnexion() {
        const panneauDeconnexion = document.getElementById('panneau-deconnexion');
        if (panneauDeconnexion) {
            panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
        }
    }

    // Fonction pour basculer le conteneur de droite
    function toggleConteneur() {
        const conteneurDroit = document.getElementById('conteneur-droit');
        if (conteneurDroit) {
            conteneurDroit.classList.toggle('ouvert');
        }
    }

    // Nouvelle fonction pour basculer le conteneur de sélection de graphique
    function toggleAjoutIot() {
        const conteneurAjout = document.getElementById('conteneur-ajout-iot');
        if (conteneurAjout) {
            conteneurAjout.classList.toggle('ouvert');
        }
    }

    // Fonction pour observer les changements dans le conteneur droit
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

    // Nouvelle fonction pour observer les changements dans conteneur-ajout-iot (optionnel pour l'instant)
    function observerAjoutIot() {
        const conteneur = document.getElementById('conteneur-ajout-iot');
        const boutonAjout = document.querySelector('.bouton-ajout-iot');
    
        if (conteneur && boutonAjout) {
            // Pour l'instant, pas de logique complexe, juste une observation
            console.log("observerAjoutIot actif");
        } else {
            console.warn("conteneur-ajout-iot ou bouton-ajout-iot est introuvable dans le DOM.");
        }
    }

    // Fermer le panneau d'ajout IoT si clic à l'extérieur
    function fermerAjoutIotSiClickExterieur() {
        document.addEventListener('click', function (event) {
            const panneau = document.getElementById('conteneur-ajout-iot');
            const boutonAjout = document.querySelector('.bouton-ajout-iot');

            if (!panneau || !boutonAjout) return;

            const isClickInside = panneau.contains(event.target) || boutonAjout.contains(event.target);

            if (!isClickInside) {
                panneau.classList.remove('ouvert');
            }
        });
    }

    // Attacher les événements
    function attacherEvenements() {
        const boutonAjout = document.querySelector('.bouton-ajout-iot');
        if (boutonAjout) {
            boutonAjout.addEventListener('click', toggleAjoutIot);
        } else {
            console.warn("Bouton .bouton-ajout-iot non trouvé");
        }
    }
});