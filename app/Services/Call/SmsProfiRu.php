<?php

namespace App\Services\Call;

use App\Services\Sms\SmsSender;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class SmsProfiRu implements SmsSender
{
    private string $appId;
    private string $url;
    private Client $client;

    public function __construct($appId, $url = 'https://lcab.smsprofi.ru/json/v1.0/callpassword/send')
    {
        $this->appId  = $appId;
        $this->url    = $url;
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function send($number, $text = null): ResponseInterface
    {
        return $this->client->post($this->url, [
            'headers' => [
                "X-Token"      => $this->appId,
                "Content-Type" => "application/json",
            ],
            'json' => [
                'recipient' => trim($number, '+'),
                'tags' => ['test', 'тест'] // TODO Remove or change tags
            ]
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendSms($number, $text): ResponseInterface
    {
        $message = [
            'recipient'     => trim($number, '+'),
            'text'          => $text,
            'recipientType' => 'recipient',
            'source'        => 'TTR-Kuhni',
            'timeout'       => 600
        ];

        return $this->client->post('https://lcab.smsprofi.ru/json/v1.0/sms/send/text', [
            'headers' => [
                "X-Token"      => $this->appId,
                "Content-Type" => "application/json",
            ],
            'json' => [
                'messages' => [$message]
            ]
        ]);
    }
}
