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
        learndash_user_set_enrolled_courses( $userId, [$courseId] );
        return new WP_REST_Response("success", 200);
    }

    public function markLessonAsComplete( $request ): WP_REST_Response
    {
        $userId     = $request["user"];
        $courseId   = $request["course"];
        $lessonId   = $request["lesson"];
        learndash_activity_complete_lesson( $userId, $courseId, $lessonId, time());
        return new WP_REST_Response("success", 200);
    }

    public function getCertificate( $request ): WP_REST_Response
    {
        $link = learndash_get_certificate_link(10730);
        return new WP_REST_Response($link, 200);
    }
}