<?php

namespace G28\Eucapacito\DAO;

class QuestionLD
{

    public static function getQuestionAnswers( $questionId )
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT answer_data FROM {$wpdb->prefix}learndash_pro_quiz_question WHERE id=%s", $questionId);
        return $wpdb->get_results($sql);
    }

}