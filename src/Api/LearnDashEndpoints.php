<?php

namespace G28\Eucapacito\Api;

use Exception;
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
        learndash_activity_complete_lesson( $userId, $courseId, $lessonId, time() - (3 * 60 * 60));
        return new WP_REST_Response("success", 200);
    }

    public function getCertificate( $request ): WP_REST_Response
    {
        $userId         = $request["user"];
        $quizId         = $request["quiz"];
        $certificate    = learndash_get_certificate_link($quizId, $userId);
        $s              = explode('href="',$certificate);
        $link           = explode('">',$s[1])[0];
        return new WP_REST_Response($link, 200);
    }
}