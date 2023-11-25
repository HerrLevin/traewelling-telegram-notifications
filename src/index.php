<?php

declare(strict_types=1);

const REQUEST_URL="http://localhost:8000";
const CLIENT_ID=4;
const REDIRECT_URI = "http://localhost:9999/";
const TRWL_WEBHOOK_URL = "http://localhost:9999/";
const CLIENT_SECRET="XXXXXXX";
const TELEGRAM_BOT_TOKEN="XXX:XXX";
const TELEGRAM_CHAT_ID="12345";
const TELEGRAM_API_URL="https://api.telegram.org/bot%s/%s";

// generate get request url
$state = "123";

$query = http_build_query([
    'client_id' => CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'response_type' => 'code',
    'state' => $state,
    'trwl_webhook_events' => 'notification',
    'trwl_webhook_url' => TRWL_WEBHOOK_URL,
]);

echo REQUEST_URL . "/oauth/authorize?" . $query . "\n";

// fetch oauth token
// generate curl post request
if (isset($_GET['code'])) {
    var_dump("hi");
    $ch   = curl_init();
    $data = [
        'grant_type' => 'authorization_code',
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'redirect_uri' => REDIRECT_URI,
        'code' => $_GET['code'],
    ];
    $json = json_encode($data);
    curl_setopt($ch, CURLOPT_URL, REQUEST_URL . "/oauth/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    $response = curl_exec($ch);
    var_dump($response);
}

// check if request is post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get request body
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $data = $data['notification'];
    $title = $data['leadFormatted'];
    $message = $data['noticeFormatted'];
    $url = sprintf(TELEGRAM_API_URL, TELEGRAM_BOT_TOKEN, "sendMessage");
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $title . "\n" . $message,
    ];
    $json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    $response = curl_exec($ch);
    var_dump($response);
}
