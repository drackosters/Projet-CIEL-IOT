<?php

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
}
