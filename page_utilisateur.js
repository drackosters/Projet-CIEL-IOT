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



//graphique conso d'électricité
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