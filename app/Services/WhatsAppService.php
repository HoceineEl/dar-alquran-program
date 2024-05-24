<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsAppService
{
    protected $client;

    protected $apiUrl;

    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    public function sendMessage($student, $message = null)
    {
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => 'whatsapp:'.$student->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => 'alert',
                        'language' => [
                            'code' => 'ar',
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    [
                                        'type' => 'text',
                                        'text' => $student->name,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            return ['error' => $e->getMessage()];
        }
    }

    public function sendVoiceMessage($student, $audioUrl)
    {
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => 'whatsapp:'.$student->phone,
                    'type' => 'audio',
                    'audio' => [
                        'link' => $audioUrl,
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            return ['error' => $e->getMessage()];
        }
    }

    public function sendCustomMessage($student, $message = null)
    {

        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => 'whatsapp:'.$student->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => 'absence',
                        'language' => [
                            'code' => 'ar',
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    [
                                        'type' => 'text',
                                        'text' => $student->name,
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $message,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            return ['error' => $e->getMessage()];
        }
    }
}
