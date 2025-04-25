<?php
session_start();

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'set') {
        // Crée un cookie qui expire dans 1 jour
        setcookie("user_consent", "accepted", time() + 86400, "/");
    } elseif ($action === 'delete') {
        // Supprime le cookie en le remplaçant par un cookie expiré
        setcookie("user_consent", "", time() - 3600, "/");
    }
}