<?php

namespace G28\Eucapacito\Api;


use WP_REST_Controller;
use WP_REST_Server;

class EndpointRegistrator extends WP_REST_Controller {

    protected string $jwt;
    protected string $eucapacito_namespace;

    public function __construct()
    {
        $this->jwt                      = 'jwt-auth/';
        $this->eucapacito_namespace     = 'eucapacito/v1';
        $this->register_routes();
        $this->addFieldsToApi();
    }

    public function register_routes()
	{

        register_rest_route( $this->eucapacito_namespace, '/ping', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( $this, 'ping' )
        ));

        // USER ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/register', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'registerUser' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/update-profile', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'updateUser' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/recoverpwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'recoverPassword' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/changepwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'changePassword' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/resetpwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'resetPassword' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/verify-reset', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( UserEndpoints::class, 'verifyResetLink' )
        ) );
        register_rest_route( $this->jwt, $this->eucapacito_namespace . '/avatar', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'avatar' )
        ) );


        // PARTNERS ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/partners', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( PartnersEndpoints::class, 'getPartners' )
        ) );

        // SEARCH ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/search', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( SearchEndpoints::getInstance(), 'getSearch' )
        ) );

        // PAGES ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/aboutpage", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( PageEndpoints::getInstance(), 'getAboutPage' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/terms-and-services", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( PageEndpoints::getInstance(), 'getTermsAndServicesPage' )
        ) );

        // MEDIA ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/banners", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( MediaEndpoints::getInstance(), 'getBanners' )
        ) );

        // LEARNDASH ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/get-certificate", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( LearnDashEndpoints::getInstance(), 'getCertificate' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/get-user-progress", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( LearnDashEndpoints::getInstance(), 'getUserCourseProgress')
        ) );
        register_rest_route( $this->eucapacito_namespace, "/enroll-user-to-course", array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( LearnDashEndpoints::getInstance(), 'enrollUserToCourse' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/lesson-complete", array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( LearnDashEndpoints::getInstance(), 'markLessonAsComplete' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/set-quiz-progress", array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( LearnDashEndpoints::getInstance(), 'setQuizProgress' )
        ) );



	}

    public function addFieldsToApi()
    {
        add_filter( 'rest_prepare_user', function( $response, $user, $request ) {
            $response->data[ 'email' ]      = $user->user_email;
            $meta                           = get_user_meta( $user->ID, 'avatar_id' );
            $response->data[ 'avatar' ]     = wp_get_attachment_image_url( $meta[0] );
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_curso_ec', function( $response, $post, $request ) {
            $response->data[ 'duration' ] = get_post_meta($post->ID, '_learndash_course_grid_duration');
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_jornada', function( $response, $post, $request ) {
            $response->data[ 'image' ] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0];
            $cursos = $response->data[ 'cursos_ec' ];
            $newCursos = [];
            foreach($cursos as $curso)
            {
                $post = get_post($curso['ID']);
                $curso['title'] = $post->post_title;
                $curso['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0];
                $curso['post_content'] = "";
                array_push($newCursos, $curso);
            }
            $response->data[ 'cursos_ec' ] = $newCursos;
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_bolsa_de_estudo', function( $response, $post, $request ) {
            $cursos = $response->data[ 'cursos_ec' ];
            $newCursos = [];
            foreach($cursos as $curso)
            {
                $post = get_post($curso['ID']);
                $curso['title'] = $post->post_title;
                $curso['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0];
                $curso['responsavel'] = $curso['responsavel']['guid'];
                array_push($newCursos, $curso);
            }
            $response->data[ 'cursos_ec' ] = $newCursos;
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_empregabilidade', function( $response, $post, $request ) {
            $cursos = $response->data[ 'cursos_ec' ];
            $newCursos = [];
            foreach($cursos as $curso)
            {
                $post = get_post($curso['ID']);
                $curso['title'] = $post->post_title;
                $curso['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0];
                array_push($newCursos, $curso);
            }
            $response->data[ 'cursos_ec' ] = $newCursos;
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_sfwd-courses', function( $response, $post, $request ) {
            $response->data[ 'image' ] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0];
            $response->data[ 'quizz' ] = [];
            $quizzes = get_posts([ 'post_type' => 'sfwd-quiz', 'meta_key' => 'course_id', 'meta_value' => $post->ID ]);
            if(count($quizzes) > 0) {
                $response->data[ 'quizz' ] = [
                    "id"        => $quizzes[0]->ID,
                    "slug"      => $quizzes[0]->post_name
                ];
            }
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_sfwd-lessons', function ( $response, $post, $request) {
            $courseId                   = get_post_meta($post->ID, 'course_id')[0];
            $response->data["course"]   = get_post(intval($courseId))->post_name;
            $steps                      = learndash_get_course_steps($courseId);
            $next                       = $steps[array_search($post->ID, $steps) + 1];
            $prev                       = $steps[array_search($post->ID, $steps) - 1];
            $response->data["next"]     = get_post(intval($next))->post_name;
            $response->data["prev"]     = get_post(intval($prev))->post_name;
            return $response;
        }, 10, 3);
    }

    public function ping( $request )
    {
        //echo get_user_meta( 52351, 'avatar_id' )[0] . PHP_EOL . wp_get_attachment_image_url( 13076 );
        echo wp_get_attachment_image_src( get_post_meta( 10429, 'responsavel')[0] )[0];
//        $t = SearchEndpoints::getInstance()->getTaxonomies();
//        $tr = wp_get_post_terms( 10429, $t );
//        $res = array_column($tr, 'term_id');
//        echo array_intersect([232], $res)[0];
    }
    

}