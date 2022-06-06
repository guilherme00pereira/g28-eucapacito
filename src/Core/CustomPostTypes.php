<?php

namespace G28\Eucapacito\Core;

class CustomPostTypes
{
    public function __construct()
    {
        add_action( 'init', [ $this, 'registerPartnersCategoryTaxonomy']);
        add_action( 'init', [ $this, 'registerPartnersPostType'] );
    }

    public function registerPartnersPostType()
    {
        $labels = array(
		    'name' => _x( 'Parceiros', 'post type general name'),
            'singular_name' => _x('Parceiro', 'post type singular name'),
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'My custom post type',
            'public'        => true,
            'has_archive'   => false,
            'show_in_rest'  => true,
            'menu_position' => 5,
            'taxonomies'    => [ 'partners_category' ],
            'supports'      => [ 'title', 'thumbnail', 'page-attributes' ]
        );
        register_post_type('partner', $args);
    }

    public function registerPartnersCategoryTaxonomy()
    {
        register_taxonomy(
          'partners_category',
          'partner',
            [
                'hierarchical'  => true,
                'label'         => 'Categoria Parceiros',
                'query_var'     => true,
                'has_archive'   => true,
                'rewrite'       => array('slug' => 'partners_category')
            ]
        );
    }
}