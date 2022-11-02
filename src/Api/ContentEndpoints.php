<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;
use G28\Eucapacito\Core\DBQueries;

class ContentEndpoints
{

    protected static ?ContentEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?ContentEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getAllPostsSlug(): WP_REST_Response
    {
        $slugs = DBQueries::getPostsSlug();
        return new WP_REST_Response( $slugs , 200 );
    }

    public static function getAllScholarshipsSlug(): WP_REST_Response
    {
        $slugs = DBQueries::getScholarshipsSlug();
        return new WP_REST_Response( $slugs , 200 );
    }

    public static function getAllEmployabilitiesSlug(): WP_REST_Response
    {
        $slugs = DBQueries::getEmployabilitiesSlug();
        return new WP_REST_Response( $slugs , 200 );
    }

    public static function getAllJourneysSlug(): WP_REST_Response
    {
        $slugs = DBQueries::getJourneysSlug();
        return new WP_REST_Response( $slugs , 200 );
    }
    
}