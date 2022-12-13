<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\ImageConverter;

class FieldsRegistrator
{
    public function __construct()
    {
        $this->addFieldsToApi();
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
            if( !empty( $response->data[ 'featured_media' ] ) )
            {
                $imageLink = ImageConverter::generetaWebpFile( wp_get_attachment_image_src($response->data[ 'featured_media' ], "medium")[0] );
                $response->data[ 'featured_image_src' ] = $imageLink;
            }
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_post', function( $response, $post, $request ) {
            if( !empty( $response->data[ 'featured_media' ] ) )
            {
                $imageLink = ImageConverter::generetaWebpFile( wp_get_attachment_image_src($response->data[ 'featured_media' ], "medium")[0] );
                $response->data[ 'featured_image_src' ] = $imageLink;
            }
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_jornada', function( $response, $post, $request ) {
            $imageLink = ImageConverter::generetaWebpFile( wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0] );
            $response->data[ 'image' ] = $imageLink;
            $cursos = $response->data[ 'cursos_ec' ];
            $newCursos = [];
            foreach($cursos as $curso)
            {
                $post = get_post($curso['ID']);
                $curso['title'] = $post->post_title;
                $imageThumb = ImageConverter::generetaWebpFile( wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0] );
                $curso['image'] = $imageThumb;
                $curso['post_content'] = "";
                $newCursos[] = $curso;
            }
            $response->data[ 'cursos_ec' ] = $newCursos;
            return $response;
        }, 10, 3 );

        add_filter( 'rest_prepare_bolsa_de_estudo', function( $response, $post, $request ) {
            $cursos = $response->data[ 'cursos_ec' ];
            $imagem = $response->data[ 'imagem' ];
            if( !empty( $imagem ) ){
                $imageLink = ImageConverter::generetaWebpFile( wp_get_attachment_image_src($imagem['ID'], "medium")[0] );
                $imagem["guid"] = $imageLink;
                $response->data[ 'imagem' ] = $imagem;
            }
            $newCursos = [];
            foreach($cursos as $curso)
            {
                $post = get_post($curso['ID']);
                $curso['title'] = $post->post_title;
                $imageThumb = ImageConverter::generetaWebpFile( wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0] );
                $curso['image'] = $imageThumb;
                $curso['responsavel'] = $curso['responsavel']['guid'];
                $newCursos[] = $curso;
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
                $imageThumb = ImageConverter::generetaWebpFile( wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium")[0] );
                $curso['image'] = $imageThumb;
                $newCursos[] = $curso;
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
}