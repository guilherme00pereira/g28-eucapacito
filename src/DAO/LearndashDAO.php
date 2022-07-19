<?php

namespace G28\Eucapacito\DAO;

use LDLMS_DB;

class LearndashDAO
{

    public static function getUserProgress( $userId, $courseId )
    {
        global $wpdb;
        $steps          = [];
        $progress       = learndash_user_get_course_progress( $userId, $courseId )['lessons'];
        foreach($progress as $k => $v) {
            $steps[] = [
                'id'        => $k,
                'status'    => '0'
            ];
        }
        $sql            = $wpdb->prepare( 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE
                             course_id=%d AND user_id=%d AND activity_completed > 0', $courseId, $userId );
        $activities     = $wpdb->get_results( $sql, ARRAY_A );
        if( !empty( $activities ) ) {
            foreach ($activities as $activity) {
                $key = array_search($activity['post_id'], array_column($steps, 'id'));
                $steps[$key]['status'] = isset($activity['activity_completed']) && $activity['activity_completed'] > 0 ? "completed" : '0';
            }
        }
        return $steps;
    }

}