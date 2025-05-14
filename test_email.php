<?php
require 'vendor/autoload.php'; // Charge PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'SMTP.titan.email'; // Remplacez par votre serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'alerte@plagiot.tech'; // Votre adresse email
            $mail->Password = 'Pl@gI0T-@lert3'; // Votre mot de passe email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Utilisez STARTTLS
            $mail->Port = 587; // Port pour TLS

            // Désactiver la vérification SSL (si nécessaire)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Destinataires
            $mail->setFrom('noreply@example.com', 'Test Application');
            $mail->addAddress($email); // Adresse email de destination

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Test d\'envoi d\'email';
            $mail->Body = "Bonjour,<br><br>Ceci est un test d'envoi d'email via PHPMailer.<br><br>Cordialement,<br>L'équipe.";

            // Envoi de l'email
            $mail->send();
            echo "Email envoyé avec succès à $email.";
        } catch (Exception $e) {
            echo "L'email n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
        }
    } else {
        echo "Veuillez entrer une adresse email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test d'envoi d'email</title>
</head>
<body>
    <h1>Test d'envoi d'email</h1>
    <form method="POST" action="">
        <label for="email">Adresse email :</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>

















<?php
/*
if (!empty($alertes)) {
    $sujet = "Alertes IoT détectées";
    $message = "Bonjour $nom_utilisateur,\n\nVoici les alertes détectées :\n\n" . implode("\n", $alertes) . "\n\nVeuillez vérifier vos équipements.";

    try {
        $mail = new PHPMailer(true);
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
        error_log("Erreur envoi email : " . $mail->ErrorInfo);
    }
}*/
?>