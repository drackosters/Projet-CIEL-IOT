<?php
//Connexion BDD MySQL
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
$host = "http://132.220.210.127:8086";
$db = "iot_data";
$query = "SELECT mean(apower) as apower, mean(temperature) as temperature, mean(humidite) as humidite 
          FROM mqtt_consumer 
          WHERE time > now() - 24h 
          GROUP BY time(1h) fill(none)";
$url = "$host/query?db=$db&q=" . urlencode($query);
?>
