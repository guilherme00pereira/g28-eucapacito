<?php

namespace G28\Eucapacito\DAO;

use LDLMS_DB;

class LearndashDAO
{

    public static function getUserProgress( $userId, $courseId ): array
    {
        global $wpdb;
        $steps          = [];
        $sql            = $wpdb->prepare( 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE course_id=%d AND user_id=%d', $courseId, $userId );
        $activities     = $wpdb->get_results( $sql, ARRAY_A );
        if( !empty( $activities ) ) {
            foreach ($activities as $activity) {
                $steps[] = [
                    'id'        => $activity['post_id'],
                    'status'    => isset($activity['activity_completed']) && $activity['activity_completed'] > 0 ? "completed" : null,
                    'type'      => $activity['activity_type'],
                ];
            }
        }
        return $steps;
    }

}