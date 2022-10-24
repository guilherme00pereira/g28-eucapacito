<?php

namespace G28\Eucapacito\Core;

use LDLMS_DB;

class DBQueries
{

    public static function getLearndashUserProgress($userId, $courseId ): array
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

    public static function getSubscribersIds(): array
    {
        global $wpdb;
        $ids        = [];
        $sql        = "SELECT wp_users.ID as id
                FROM wp_users INNER JOIN wp_usermeta 
                ON wp_users.ID = wp_usermeta.user_id 
                WHERE wp_usermeta.meta_key = 'wp_capabilities' 
                AND wp_usermeta.meta_value LIKE '%subscriber%'";
        $bdIds   = $wpdb->get_results( $sql );
        if( !empty( $bdIds ) ) {
            foreach ( $bdIds as $bdId ) {
                $ids[] = $bdId->id;
            }
        }
        return $ids;
    }

    public static function getMediaAuthorIds()
    {
        global $wpdb;
        $ids        = [];
        $sql        = "select ID, post_author from wp_posts where post_type = 'attachment' order by ID desc";
        $bdIds   = $wpdb->get_results( $sql );
        if( !empty( $bdIds ) ) {
            foreach ( $bdIds as $bdId ) {
                $ids[] = [
                    "id"        => $bdId->ID,
                    "author"    => $bdId->post_author
                ];
            }
        }
        return $ids;
    }

    public static function getTaxnomoiesByTerms( $ids )
    {
        global $wpdb;
        $taxs       = [];
        $sql        = "select taxonomy from wp_term_taxonomy where term_id in (" . $ids . ")";
        $names      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($names as $name)
        {
            $taxs[] = $name['taxonomy'];
        }
        return $taxs;
    }

    public static function getEcCoursesSlug(): array
    {
        global $wpdb;
        $slugs      = [];
        $sql        = "select post_name as slug from wp_posts where post_type = 'curso_ec'  and post_name <> '' and post_status = 'publish'";
        $rows      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($rows as $row)
        {
            $slugs[] = $row['slug'];
        }
        return $slugs;
    }

    public static function getPostsSlug(): array
    {
        global $wpdb;
        $slugs      = [];
        $sql        = "select post_name as slug from wp_posts where post_type = 'post'  and post_name <> '' and post_status = 'publish'";
        $rows      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($rows as $row)
        {
            $slugs[] = $row['slug'];
        }
        return $slugs;
    }


    public static function getScholarshipsSlug(): array
    {
        global $wpdb;
        $slugs      = [];
        $sql        = "select post_name as slug from wp_posts where post_type = 'bolsa_de_estudo'  and post_name <> '' and post_status = 'publish'";
        $rows      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($rows as $row)
        {
            $slugs[] = $row['slug'];
        }
        return $slugs;
    }

    public static function getEmployabilitiesSlug(): array
    {
        global $wpdb;
        $slugs      = [];
        $sql        = "select post_name as slug from wp_posts where post_type = 'empregabilidade'  and post_name <> '' and post_status = 'publish'";
        $rows      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($rows as $row)
        {
            $slugs[] = $row['slug'];
        }
        return $slugs;
    }

    public static function getJourneysSlug(): array
    {
        global $wpdb;
        $slugs      = [];
        $sql        = "select post_name as slug from wp_posts where post_type = 'jornada'  and post_name <> '' and post_status = 'publish'";
        $rows      = $wpdb->get_results( $sql, ARRAY_A );
        foreach($rows as $row)
        {
            $slugs[] = $row['slug'];
        }
        return $slugs;
    }

}