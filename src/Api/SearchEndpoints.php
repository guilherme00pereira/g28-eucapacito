<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\DBQueries;
use G28\Eucapacito\Core\Logger;
use WP_REST_Response;
use WP_Query;

class SearchEndpoints
{
    protected static ?SearchEndpoints $_instance = null;
    private array $taxonomies;
    private array $filteredTerms;

    public function __construct()
    {
        $this->taxonomies       = get_object_taxonomies( 'curso_ec' );
        $this->filteredTerms    = [];
    }

    public static function getInstance(): ?SearchEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getSearch( $request ): WP_REST_Response
    {
        $postTypes = ['post', 'curso_ec', 'sfwd-courses', 'bolsa_de_estudo', 'empregabilidade', 'jornada'];
        if( !empty( $request['course'] ) ) {
            $postTypes = ['curso_ec'];
        }
        $courses = [];
        $args = [
            'post_type'         => $postTypes,
            'posts_per_page'    => 15,
            'paged'             => $request['page'],
            'orderby'           => ['post_modified' => 'DESC'],

        ];
        if( !empty( $request['search'] ) ) {
            $args['s'] = $request['search'];
        }
        if( $request['t'] ) {
            $this->filteredTerms    = explode(',', $request['t']);
            $args['tax_query']      = [ 'relation' => 'AND' ];
            $taxonomies             = DBQueries::getTaxnomoiesByTerms($request['t']);
            foreach( $taxonomies as $taxonomy ) {
                $args['tax_query'][] = 
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $this->filteredTerms,
                    ];
            }
        }
        $query = new WP_Query( $args );
        while ($query->have_posts()) {
            $query->the_post();
            $postId = get_the_ID();

            $terms = wp_get_post_terms( $postId, $this->getTaxonomies());
            $courses[] = [
                'id'                => $postId,
                'slug'              => basename(get_permalink($postId)),
                'title'             => $query->post->post_title,
                'image'             => wp_get_attachment_image_src( get_post_thumbnail_id($postId), "full")[0],
                'type'              => $query->post->post_type,
                'logo'              => wp_get_attachment_image_src( get_post_meta( $postId, 'responsavel')[0], "full")[0],
                'terms'             => array_column($terms, 'term_id')
            ];
        }
        $filters = $this->selectFilters();
        $response = new WP_REST_Response( [
            'filters'   => $filters,
            'courses'   => $courses,
            'total'     => $query->found_posts
        ]);
        $response->set_status(200);
        $response->header( 'x-wp-totalpages', $query->max_num_pages);
        wp_reset_postdata();
        return $response;
    }

    private function selectFilters(): array
    {
        $filters = [];
        foreach( $this->taxonomies as $taxonomy ){
            $filters[$taxonomy] = [];
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));
            foreach( $terms as $term ){
                $filters[$term->taxonomy][] = [
                    'id'        => $term->term_id,
                    'name'      => $term->name,
                    'count'     => $term->count,
                    'selected'  => in_array( $term->term_id, $this->filteredTerms )
                ];
            }
        }
        return $filters;
    }

    /**
     * @return array|string[]|\WP_Taxonomy[]
     */
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }
}