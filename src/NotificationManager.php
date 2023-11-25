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
        $ne = NotificationEnum::tryFrom($data['type']) ?? NotificationEnum::DEFAULT;
        $data = [
            'chat_id' => getenv('TELEGRAM_CHAT_ID'),
            'text' => $ne->getEmoji() . ' ' . $this->getTitle($data). "\n" . $ne->getNotificationMessage($data),
            'parse_mode' => 'html',
        ];

        (new EasyRequest($this->telegramUrl(), $data))->request();

    }

    private function telegramUrl(): string
    {
        return sprintf(getenv('TELEGRAM_API_URL'), getenv('TELEGRAM_BOT_TOKEN'), "sendMessage");
    }

    private function getTitle(array $data): string
    {
        return sprintf('<a href="%s">%s</a>',$data['link'],$data['leadFormatted']);
    }
}
