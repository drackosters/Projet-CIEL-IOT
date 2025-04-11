// Gestion des options du panneau d'options
const optionsPanneau = document.querySelectorAll('.option-panneau');
optionsPanneau.forEach(option => {
    option.addEventListener('click', () => {
        const value = option.dataset.value;
        console.log(`Option du panneau sélectionnée : ${value}`);

        // Ajoutez ici le code pour gérer l'option sélectionnée
        if (value === 'optionA') {
            alert('Option A sélectionnée !');
        } else if (value === 'optionB') {
            alert('Option B sélectionnée !');
        }

        document.getElementById('panneau-options').style.display = 'none'; // Ferme le panneau après la sélection
    });
});

//------------PAGE DE DECONNEXION-----------------

//panneau de déconnexion
function togglePanneauDeconnexion() {
    var panneauDeconnexion = document.getElementById('panneau-deconnexion');
    panneauDeconnexion.style.display = (panneauDeconnexion.style.display === 'block') ? 'none' : 'block';
}

//conteneur de droite
function toggleConteneur() {
    var conteneurDroit = document.getElementById('conteneur-droit');
    conteneurDroit.classList.toggle('ouvert');
}