<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\Logger;
use WP_REST_Response;

class IntegrationEndpoints
{
    protected static ?IntegrationEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?IntegrationEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

//    public function authCallback( $request )
//    {
//        Logger::getInstance()->add($request['code']);
//    }
//
//    public function registerRDStationLead( $request ) : WP_REST_Response
//    {
//        Logger::getInstance()->add( $request['name'] );
//        return new WP_REST_Response([ 'success' => true ], 200);
//    }
}