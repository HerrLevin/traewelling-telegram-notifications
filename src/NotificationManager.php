<?php

namespace App;

class NotificationManager
{
    private array $notificationData;

    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function handle(): void
    {
        $data = $this->notificationData['notification'];
        $title = $data['leadFormatted'];
        $message = $data['noticeFormatted'];
        $data = [
            'chat_id' => getenv('TELEGRAM_CHAT_ID'),
            'text' => $title . "\n" . $message,
        ];

        (new EasyRequest($this->telegramUrl(), $data))->request();

    }

    private function telegramUrl(): string
    {
        return sprintf(getenv('TELEGRAM_API_URL'), getenv('TELEGRAM_BOT_TOKEN'), "sendMessage");
    }
}
