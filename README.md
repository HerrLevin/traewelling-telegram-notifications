# TraewellingTelegramNotifications

This is very much a wip. It's not really the best but it kinda works.

## Install

1. Clone the repository
2. Install the dependencies with `composer install`
3. Register a `confidential` app on [Träwelling](https://traewelling.de/settings/applications/create) and enable webhooks
4. Register a Telegram Bot on [BotFather](https://t.me/botfather)
5. Copy .env.example to .env and fill in the values
6. Point your webserver to the `public` directory (or use `php -S 0.0.0.0:8001 public/index.php` for testing -- If you try it locally you'll need a local Träwelling instance.)
