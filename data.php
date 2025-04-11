<?php
// Include database config with $url defined
require 'config.php';

// Query InfluxDB using the generated URL
$response = @file_get_contents($url);

// Decode the JSON response
$data = json_decode($response, true);

// Extract data points from InfluxDB response
$values = $data['results'][0]['series'][0]['values'] ?? null;

// If no data found, return an error
if (!$values) {
    http_response_code(500);
    echo json_encode(["error" => "Pas de données trouvées ou requête invalide."]);
    exit;
}

// Reformat InfluxDB data to fit Chart.js expectations
$result = array_map(function($point) {
    return [
        'time' => $point[0],  // ISO timestamp
        'value' => $point[1]  // apower value
    ];
}, $values);

// Return JSON
header('Content-Type: application/json');
echo json_encode($result);
