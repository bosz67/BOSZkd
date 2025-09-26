<?php
// send_discord.php

// Ontvang JSON vanuit de frontend
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
  http_response_code(400);
  echo json_encode(["error" => "Geen data ontvangen"]);
  exit;
}

// === JOUW DISCORD WEBHOOK ===
$webhook = "https://discord.com/api/webhooks/1419409401353339062/5BKkXdhDzHTzHZ4ylJWpSSWGg-Dhl-Sk5Mrh_Pd0w1tyzlgEUcICtBywzdGUyohocNlu";

// Zet bericht in tekstvorm
$items = [];
foreach ($data["items"] as $item) {
  $items[] = $item["qty"] . "× " . $item["title"];
}
$message = "✅ " . $data["name"] . " heeft " . implode(", ", $items) . " gekocht voor €" . number_format($data["total"], 2, ",", ".");

// Payload voor Discord
$payload = json_encode([
  "content" => $message
]);

// Verstuur naar Discord
$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
  http_response_code(500);
  echo json_encode(["error" => $err]);
} else {
  echo json_encode(["success" => true]);
}
