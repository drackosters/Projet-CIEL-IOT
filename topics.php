<?php
$servername = "54.173.114.33";
$username = "admin";
$password = "admin";
$dbname = "plage_iot";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
} catch(PDOException $e) {
    echo "La connexion a échoué : " . $e->getMessage();
}

// Fermer la connexion
$conn = null;
?>

<script>
function handleButtonClick(topic) {
    var action = prompt('Choisissez une action pour le topic "' + topic + '":\n1. Ajouter à un IoT existant\n2. Ajouter un nouvel IoT');
    if (action === '1') {
        addToExistingIoT(topic);
    } else if (action === '2') {
        addNewIoT(topic);
    } else {
        alert('Action non valide');
    }
}

function addToExistingIoT(topic) {
    alert('Ajouter à un IoT existant: ' + topic);
    // Ajoutez ici le code pour gérer l'ajout à un IoT existant
}

function addNewIoT(topic) {
    alert('Ajouter un nouvel IoT: ' + topic);
    // Ajoutez ici le code pour gérer l'ajout d'un nouvel IoT
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