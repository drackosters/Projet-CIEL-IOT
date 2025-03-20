<?php
require 'config.php';

// Exécuter la requête
$sql = "SELECT topic FROM unique_topics";
$stmt = $conn->prepare($sql);
$stmt->execute();


// Vérifier s'il y a des résultats
if ($stmt->rowCount() > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Topic</th><th>Action</th></tr>";
    
    // Afficher les données
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . htmlspecialchars($row["topic"]) . "</td>";
        echo "<td><button onclick=\"handleButtonClick('" . htmlspecialchars($row["topic"]) . "')\">Action</button></td></tr>";
    }
    
    echo "</table>";
} else {
    echo "Aucun résultat trouvé";
}

// Fermer la connexion
$conn = null;
?>

<script>
function handleButtonClick(topic) {
    fetch('get_last_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ topic: topic })
    })
    .then(response => response.json())
    .then(data => {
        let message = 'Aucun message trouvé pour le topic "' + topic + '"';
        if (data.success) {
            message = 'Dernier message pour le topic "' + topic + '": ' + data.message;
        }
        var action = prompt(message + '\n\nChoisissez une action pour le topic "' + topic + '":\n1. Ajouter à un IoT existant\n2. Ajouter un nouvel IoT\n3. Ajouter un nouvel IoT avec JSON');
        if (action === '1') {
            addToExistingIoT(topic);
        } else if (action === '2') {
            addNewIoT(topic);
        } else if (action === '3') {
            addNewIoTWithJSON(topic);
        } else {
            alert('Action non valide');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function addToExistingIoT(topic) {
    alert('Ajouter à un IoT existant: ' + topic);
    window.location.href = 'add_to_iot.php?topic=' + encodeURIComponent(topic);
}

function addNewIoT(topic) {
    alert('Ajouter un nouvel IoT: ' + topic);
    window.location.href = 'create_iot.php?topic=' + encodeURIComponent(topic);
}

function addNewIoTWithJSON(topic) {
    alert('Ajouter un nouvel IoT: ' + topic);
}

function updateTopics() {
    fetch('store_topic.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ topic: 'example_topic' }) // Remplacez 'example_topic' par le topic que vous souhaitez envoyer
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

// Mettre à jour les topics toutes les 10 secondes
setInterval(updateTopics, 10000);
</script>