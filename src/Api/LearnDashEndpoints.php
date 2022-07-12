<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;

class LearnDashEndpoints
{
    protected static ?LearnDashEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?LearnDashEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getCertificate( $request ): WP_REST_Response
    {
        return new WP_REST_Response("ok", 200);
    }
}