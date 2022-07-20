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
        $certificate    = learndash_get_certificate_link($quizId, $userId);
        $s              = explode('href="',$certificate);
        $link           = explode('">',$s[1])[0];
//        $content       = file_get_contents($link);
//        $pdf            = wp_remote_retrieve_body($response);
//        header('Content-type: application/pdf');
//        header('Content-Disposition: attachment; filename="52286.pdf"');
//        $g28dir         = trailingslashit( wp_upload_dir()['basedir'] . '/g28' );
//        if( !file_exists( $g28dir )) {
//            wp_mkdir_p($g28dir);
//        }
//        $_filter = true;
//        add_filter( 'upload_dir', function( $arr ) use( &$_filter ){
//            if ( $_filter ) {
//                $folder = '/g28';
//                $arr['path'] .= $folder;
//                $arr['url'] .= $folder;
//                $arr['subdir'] .= $folder;
//            }
//            return $arr;
//        } );
//        $saved = wp_upload_bits("52286.pdf", null, $content);
        return new WP_REST_Response( $link, 200 );
    }
}