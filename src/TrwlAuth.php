<?php

declare(strict_types=1);

namespace App;

class TrwlAuth
{
    private int $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $webhookUrl;
    private string $requestUrl;

    public function __construct(
        int $clientId,
        string $clientSecret,
        string $redirectUri,
        string $webhookUrl,
        string $requestUrl
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->webhookUrl = $webhookUrl;
        $this->requestUrl = $requestUrl;
    }

    public function getAuthUrl(): string
    {
        $state = "123";

        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'state' => $state,
            'trwl_webhook_events' => 'notification',
            'trwl_webhook_url' => $this->webhookUrl,
        ]);

        return $this->requestUrl . "/oauth/authorize?" . $query;
    }

    public function activateWebhook(string $code): bool
    {
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ];

        $response = (new EasyRequest(getenv('REQUEST_URL') . "/oauth/token", $data))->request();
        $response = json_decode($response, true);

        if (isset($response['webhook']['id'])) {
            return true;
        }

        return false;
    }
}
