<?php

namespace App\Services\Sms;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class SmsRu implements SmsSender
{
    private string $appId;
    private string $url;
    private Client $client;

    public function __construct($appId, $url = 'https://sms.ru/code/call')
    {
        if (empty($appId)) {
            throw new \InvalidArgumentException('appId, для сервиса SmsRu, не может быть пустым.');
        }
        $this->appId = $appId;
        $this->url = $url;
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function send($number, $text): ResponseInterface
    {
        return $this->client->post($this->url, [
            'form_params' => [
                'phone' => trim($number, '+'),
                'ip' => '193.176.79.203',
                'api_id' => $this->appId
            ],
        ]);
    }
}
