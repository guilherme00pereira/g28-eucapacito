<?php

namespace G28\Eucapacito\Api\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RDStation
{
    const CLIENT_ID     = "a771d771-b5fb-44f7-8ca7-39ebf95143a9";
    const CLIENT_SECRET = "a8637b43bbb74ebfa41d8e7ad8a1a023";
    const CODE          = "ba5e8744d7ad9d6349ea6b7b6d3a7513";

    private Client $client;

    public function __construct()
    {
        $this->client       = new Client([
            'base_uri'      => 'https://api.rd.services/',
        ]);
    }

    private function getToken()
    {
        try{
            $response = $this->client->post('auth/token', [
                'body' => [
                    'client_id'     => self::CLIENT_ID,
                    'client_secret' => self::CLIENT_SECRET,
                    'code'          => self::CODE
                ]
            ]);
            return json_decode( $response->getBody()->getContents() )->access_token;
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function createLead( $formData )
    {
        $access_token = $this->getToken();
        try {
            $response = $this->client->post('auth/token', [
                'headers'   => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json',
                ],
                'body'      => [
                    'name'      => self::CLIENT_ID,
                    'email'     => self::CLIENT_SECRET,
                ]
            ]);
        } catch (GuzzleException $e) {

        }
    }
}