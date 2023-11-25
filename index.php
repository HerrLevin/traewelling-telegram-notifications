<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\TrwlAuth;

$root = $_SERVER['DOCUMENT_ROOT'];
$envFilepath = "$root/.env";
if (is_file($envFilepath)) {
    $file = new \SplFileObject($envFilepath);

    while (false === $file->eof()) {
        putenv(trim($file->fgets()));
    }
}

$trwl = new TrwlAuth(
    (int) getenv('CLIENT_ID'),
    getenv('CLIENT_SECRET'),
    getenv('REDIRECT_URI'),
    getenv('TRWL_WEBHOOK_URL'),
    getenv('REQUEST_URL')
);

echo sprintf('👉<a href="%s">Login</a>👈', $trwl->getAuthUrl());

if (isset($_GET['code'])) {
    echo $trwl->activateWebhook($_GET['code']) ? 'Webhook activated' : 'Error: Webhook not activated';
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

