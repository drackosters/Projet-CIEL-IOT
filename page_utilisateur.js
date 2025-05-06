document.addEventListener("DOMContentLoaded", function () {
  // Gestion des options du panneau d'options
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

  // panneau de déconnexion
  window.togglePanneauDeconnexion = function () {
      const panneauDeconnexion = document.getElementById('panneau-deconnexion');
      panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
  };

  // conteneur de droite
  window.toggleConteneur = function () {
      const conteneurDroit = document.getElementById('conteneur-droit');
      conteneurDroit.classList.toggle('ouvert');
  };

  // MutationObserver sur le conteneur droit
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

  // panneau latéral droit ajout IoT
  window.toggleAjoutIot = function () {
    const formulaire = document.getElementById('conteneur-ajout-iot');
    if (!formulaire) {
        console.error("conteneur-ajout-iot introuvable dans le DOM");
        return;
    }

    formulaire.style.display = (formulaire.style.display === 'none' || formulaire.style.display === '') ? 'block' : 'none';
};

// Attache l'événement au clic du bouton
const boutonAjout = document.querySelector('.bouton-ajout-iot');
if (boutonAjout) {
    boutonAjout.addEventListener('click', toggleAjoutIot);
} else {
    console.warn("Bouton .bouton-ajout-iot non trouvé");
}
}); // ← C’était manquant
