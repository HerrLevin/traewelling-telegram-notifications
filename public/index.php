<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\NotificationManager;
use App\TrwlAuth;

$envFilepath = __DIR__ . "/../.env";
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
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    $nm = new NotificationManager($data);
    $nm->handle();
}

