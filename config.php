<?php
$servername = "132.220.210.127";
$username = "admin";
$password = "password";
$dbname = "plage_iot";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "La connexion a échoué : " . $e->getMessage();
    die();
}

//Connexion InfluxDB
$influx_url = 'http://132.220.210.127:8086'; // URL de ton instance InfluxDB
$org = 'ton_organisation';
$bucket = 'ton_bucket';
$token = 'TON_TOKEN_INFLUXDB';
  
?>