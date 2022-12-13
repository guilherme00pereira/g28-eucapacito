<?php

namespace G28\Eucapacito\Api;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Response;
use G28\Eucapacito\Options\PageOptions;

class EndpointRegistrator extends WP_REST_Controller {

    protected string $jwt;
    protected string $eucapacito_namespace;

    public function __construct()
    {
        $this->jwt                      = 'jwt-auth/';
        $this->eucapacito_namespace     = 'eucapacito/v1';
        $this->register_routes();
    }

    public function register_routes()
	{

        register_rest_route( $this->eucapacito_namespace, '/ping', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( $this, 'ping' )
        ));

        // CONFIG
        register_rest_route( $this->eucapacito_namespace, '/config', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( $this, 'config' )
        ));

        // USER ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/register', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'registerUser' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/social-login', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'loginOrRegisterSocialUser' )
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

        // TAGS ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/post-tags", array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( TagsEndpoints::getInstance(), 'savePostTags' )
        ) );

        // COURSES ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/course-slugs", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( CourseEndpoints::getInstance(), 'getAllEcCoursesSlug' )
        ) );

        // CONTENT ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/blog-slugs", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( ContentEndpoints::getInstance(), 'getAllPostsSlug' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/scholarship-slugs", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( ContentEndpoints::getInstance(), 'getAllScholarshipsSlug' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/employability-slugs", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( ContentEndpoints::getInstance(), 'getAllEmployabilitiesSlug' )
        ) );
        register_rest_route( $this->eucapacito_namespace, "/journey-slugs", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( ContentEndpoints::getInstance(), 'getAllJourneysSlug' )
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

    public function ping( $request )
    {
        //echo get_user_meta( 52351, 'avatar_id' )[0] . PHP_EOL . wp_get_attachment_image_url( 13076 );
        echo wp_get_attachment_image_src( get_post_meta( 10429, 'responsavel')[0] )[0];
//        $t = SearchEndpoints::getInstance()->getTaxonomies();
//        $tr = wp_get_post_terms( 10429, $t );
//        $res = array_column($tr, 'term_id');
//        echo array_intersect([232], $res)[0];
    }

    public function config( $request ): WP_REST_Response
    {
        $pageOptions = new PageOptions();
        $pagesOptions = $pageOptions->getPagesRelationship();
        return new WP_REST_Response($pagesOptions, 200);
    }


}
