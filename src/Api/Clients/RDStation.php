<?php

namespace G28\Eucapacito\Api\Clients;

use G28\Eucapacito\Core\Logger;
use GuzzleHttp\Client;

class RDStation
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri'      => 'https://api.rd.services/platform/',
            'headers'       => [
                
            ]
        ]);
    }

    public function authCallback( $request ) 
    {
        Logger::getInstance()->add($request['code']);
    }

    public function createLead()
    {
        
    }
}