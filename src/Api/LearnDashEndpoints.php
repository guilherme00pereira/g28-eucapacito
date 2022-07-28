<?php

namespace G28\Eucapacito\Api;

use Exception;
use G28\Eucapacito\DAO\LearndashDAO;
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

    public function getUserCourseProgress($request ): WP_REST_Response
    {
        $userId     = $request["user"];
        $courseId   = $request["course"];
        $steps      = LearndashDAO::getUserProgress( $userId, $courseId );
        return new WP_REST_Response( $steps, 200 );
    }

    public function getCertificate( $request ): WP_REST_Response
    {
        $userId         = $request["user"];
        $quizId         = $request["quiz"];
        $det            = learndash_certificate_details($quizId, $userId);
        return new WP_REST_Response( $det['certificateLink'], 200 );
    }

    public function setQuizProgress( $request ): WP_REST_Response
    {
        $userId         = $request["user"];
        $quizId         = $request["quiz"];
        $points         = $request["score"];
        $total          = $request["total"];
        $percentage     = $request["percentage"];
        $quizMeta       = get_post_meta($quizId);
        $threshold      = $quizMeta['_ld_certificate_threshold'];
        $passed         = floatval($percentage) > (floatval($threshold[0]) * 100);
        $args = [
            'quiz'          => $quizId,
            'score'         => $points,
            'count'         => $total,
            'percentage'    => $percentage,
            'passed'        => $passed,
            'completed'     => time()
        ];
        $quizzesData = get_user_meta( $userId, '_sfwd-quizzes')[0];
        if( is_null($quizzesData)) {
            $quizzesData = [];
        }
        $quizzesData[] = $args;
        update_user_meta( $userId, '_sfwd-quizzes', $quizzesData);
        return new WP_REST_Response( ['passed' => $passed], 200 );
    }
}