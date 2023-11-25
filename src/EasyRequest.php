<?php

declare(strict_types=1);

namespace App;

use CurlHandle;

class EasyRequest
{
    private string $url;
    private array $data;
    private false|CurlHandle $ch;

    public function __construct(string $url, array $data = [])
    {
        $this->url = $url;
        $this->data = $data;
    }

    private function init(): void
    {
        $this->ch = curl_init();
        $json = json_encode($this->data);

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
    }

    public function request(): bool|string
    {
        $this->init();
        return curl_exec($this->ch);
    }
}
