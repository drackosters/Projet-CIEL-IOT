<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/error.log');

header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();
require 'config.php';

if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] !== true) {
    echo json_encode(["error" => "Utilisateur non connecté."]);
    exit;
}

$nom_utilisateur = $_COOKIE['nom_utilisateur'] ?? null;

if (!$nom_utilisateur) {
    echo json_encode(["error" => "Nom d'utilisateur introuvable."]);
    exit;
}

// Récupérer l'e-mail de l'utilisateur
$stmt = $conn->prepare("SELECT email FROM Utilisateur WHERE nom = :nom");
$stmt->execute(['nom' => $nom_utilisateur]);
$email = $stmt->fetchColumn();

if (!$email) {
    echo json_encode(["error" => "E-mail introuvable pour l'utilisateur."]);
    exit;
}

try {
    // Vérifier si le graphique d'énergie est actif
    $energie_active = isset($_GET['energie_active']) && $_GET['energie_active'] == '1';

    // Récupérer les topics et leurs seuils depuis la BDD MySQL
    $stmt = $conn->query("SELECT topic, Seuil_Min, Seuil_Max FROM TOPICS");
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($topics)) {
        throw new Exception("Aucun topic trouvé dans la table TOPICS.");
    }

    $alertes = [];

    // Liste des topics à ignorer
    $topics_ignores = [
        'shelly/watt/status/pm1:0-id',
        'shelly/watt/status/pm1:0-voltage',
        'shelly/watt/status/pm1:0-current',
        'shelly/watt/status/pm1:0-freq',
        'shelly/watt/status/pm1:0-aenergy',
        'shelly/watt/status/pm1:0-ret_aenergy'
    ];

    // Configuration InfluxDB
    $host = "http://132.220.210.127:8086";
    $db = "iot_data";

    foreach ($topics as $topic) {
        if (in_array($topic['topic'], $topics_ignores)) {
            continue;
        }

        if (!str_contains($topic['topic'], 'apower')) {
            continue;
        }

        if (!$energie_active && $topic['topic'] === 'shelly/watt/status/pm1:0-apower') {
            continue;
        }

        $query = "SELECT LAST(apower) FROM mqtt_consumer WHERE topic = '" . preg_replace('/[^a-zA-Z0-9_\/+-]/', '', $topic['topic']) . "'";
        $url = "$host/query?db=$db&q=" . urlencode($query);

        $response = @file_get_contents($url);
        $data = json_decode($response, true);

        if ($response === false || !isset($data['results'][0]['series'][0]['values'][0][1])) {
            $alertes[] = "Erreur : InfluxDB inaccessible pour le topic {$topic['topic']}";
            continue;
        }

        $valeur = $data['results'][0]['series'][0]['values'][0][1];

        if ($valeur > $topic['Seuil_Max']) {
            $alertes[] = "{$topic['topic']} : $valeur W dépasse le seuil max ({$topic['Seuil_Max']})";
        } elseif ($valeur < $topic['Seuil_Min']) {
            $alertes[] = "{$topic['topic']} : $valeur W en dessous du seuil min ({$topic['Seuil_Min']})";
        }
    }

    if (!empty($alertes)) {
        $sujet = "Alertes IoT détectées";
        $message = "Bonjour $nom_utilisateur,\n\nVoici les alertes détectées sur vos appareils IoT :\n\n";
        $message .= implode("\n", $alertes);
        $message .= "\n\nVeuillez vérifier vos équipements ou contacter le support technique.";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.titan.email';
            $mail->SMTPAuth = true;
            $mail->Username = 'alerte@plagiot.tech'; 
            $mail->Password = 'Pl@gI0T-@lert3';        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('alerte@plagiot.tech', 'Système IoT');
            $mail->addAddress($email);
            $mail->Subject = $sujet;
            $mail->Body = $message;
            $mail->isHTML(false);

            $mail->send();
        } catch (Exception $e) {
            error_log("Erreur PHPMailer : {$mail->ErrorInfo}");
        }
    }

    echo json_encode($alertes);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
