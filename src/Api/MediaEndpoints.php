<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Options\BannerOptions;
use WP_REST_Response;

class MediaEndpoints
{
    protected static ?MediaEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?MediaEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBanners( $request ): WP_REST_Response
    {
        $banners = BannerOptions::getBanners("full");
        return new WP_REST_Response($banners, 200);
    }

}