<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;
use G28\Eucapacito\Core\DBQueries;

class CourseEndpoints
{

    protected static ?CourseEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?CourseEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getAllEcCoursesSlug(): WP_REST_Response
    {
        $slugs = DBQueries::getEcCoursesSlug();
        return new WP_REST_Response( $slugs , 200 );
    }
    
}