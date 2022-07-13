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

    public function enrollUserToCourse( $request ): WP_REST_Response
    {
        $userId     = $request["user"];
        $courseId   = $request["course"];
        learndash_set_users_for_course( $courseId, [$userId]);
        return new WP_REST_Response("ok", 200);
    }

    public function getCertificate( $request ): WP_REST_Response
    {
        $link = learndash_get_certificate_link(10730);
        return new WP_REST_Response($link, 200);
    }
}