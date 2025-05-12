<?php
$to = "julienmolina11700@gmail.com";
$subject = "Test mail PHP";
$message = "Ceci est un test depuis ton serveur.";
$headers = "From: test@tonsite.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail envoyé avec succès.";
} else {
    echo "Échec de l'envoi du mail.";
}
?>
