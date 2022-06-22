<?php

namespace G28\Eucapacito\Api\Clients;

use GuzzleHttp\Client;

class RDStation
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri'      => '',
            'headers'       => [
                
            ]
        ]);
    }

    public function createLead()
    {
        
    }
}