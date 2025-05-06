document.addEventListener("DOMContentLoaded", function () {
    // Initialisation
    gererOptionsPanneau();
    observerConteneur();
    fermerAjoutIotSiClickExterieur();
    attacherEvenements();

    // Rendre les fonctions accessibles globalement si nécessaire
    window.togglePanneauDeconnexion = togglePanneauDeconnexion;
    window.toggleConteneur = toggleConteneur;
    window.toggleAjoutIot = toggleAjoutIot;

    // Fonction pour gérer les clics sur les options du panneau
    function gererOptionsPanneau() {
        const optionsPanneau = document.querySelectorAll('.option-panneau');
        optionsPanneau.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                console.log(`Option du panneau sélectionnée : ${value}`);

                if (value === 'optionA') {
                    alert('Option A sélectionnée !');
                } else if (value === 'optionB') {
                    alert('Option B sélectionnée !');
                }

                document.getElementById('panneau-options').style.display = 'none';
            });
        });
    }

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

    // Fonction pour observer les changements dans le conteneur droit
    function observerConteneur() {
        const conteneur = document.getElementById('conteneur-droit');
        const boutonAlerte = document.getElementById('bouton-alerte');

        if (conteneur && boutonAlerte) {
            const observer = new MutationObserver(mutations => {
                for (const mutation of mutations) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        boutonAlerte.style.backgroundImage = "url('/Projet-CIEL-IOT/image/notification_2.png')";
                    }
                }
            });

            observer.observe(conteneur, { childList: true, subtree: true });
        } else {
            console.warn("conteneur-droit ou bouton-alerte est introuvable dans le DOM.");
        }
    }

    // Fonction pour ouvrir ou fermer le formulaire d’ajout IoT
    function toggleAjoutIot() {
        const formulaire = document.getElementById('conteneur-ajout-iot');
        if (!formulaire) {
            console.error("conteneur-ajout-iot introuvable dans le DOM");
            return;
        }

        formulaire.classList.toggle('ouvert');
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
