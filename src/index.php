<?php

declare(strict_types=1);

$root = $_SERVER['DOCUMENT_ROOT'];
$envFilepath = "$root/../.env";
if (is_file($envFilepath)) {
    $file = new \SplFileObject($envFilepath);

    while (false === $file->eof()) {
        putenv(trim($file->fgets()));
    }
}

// generate get request url
$state = "123";

$query = http_build_query([
    'client_id' => getenv('CLIENT_ID'),
    'redirect_uri' => getenv('REDIRECT_URI'),
    'response_type' => 'code',
    'state' => $state,
    'trwl_webhook_events' => 'notification',
    'trwl_webhook_url' => getenv('TRWL_WEBHOOK_URL'),
]);

echo getenv('REQUEST_URL') . "/oauth/authorize?" . $query . "\n";

// fetch oauth token
// generate curl post request

if (isset($_GET['code'])) {
    activateWebhook();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get request body
    getRequestBody();
}
/**
 * @return void
 */
function activateWebhook()
{
    $ch = curl_init();
    $data = [
        'grant_type' => 'authorization_code',
        'client_id' => getenv('CLIENT_ID'),
        'client_secret' => getenv('CLIENT_SECRET'),
        'redirect_uri' => getenv('REDIRECT_URI'),
        'code' => $_GET['code'],
    ];
    $json = json_encode($data);
    curl_setopt($ch, CURLOPT_URL, getenv('REQUEST_URL') . "/oauth/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    curl_exec($ch);
}


// check if request is post
/**
 * @return void
 */
function getRequestBody()
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $data = $data['notification'];
    $title = $data['leadFormatted'];
    $message = $data['noticeFormatted'];
    $url = sprintf(getenv('TELEGRAM_API_URL'), getenv('TELEGRAM_BOT_TOKEN'), "sendMessage");
    $data = [
        'chat_id' => getenv('TELEGRAM_CHAT_ID'),
        'text' => $title . "\n" . $message,
    ];
    $json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    curl_exec($ch);
}

