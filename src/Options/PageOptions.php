<?php

namespace G28\Eucapacito\Options;

class PageOptions 
{
    const PAGE_OPTIONS = 'eucapacito_page_relationship';
    private string $pageOptions;

    public function __construct()
    {
        $this->init();
    }

    public function getPagesRelationship()
    {
        return get_option(self::PAGE_OPTIONS);
    }

    private function init()
    {
        $options = get_option(self::PAGE_OPTIONS);
        
        if( is_null( $options ) || is_bool( $options ) || empty( $options ) )
        {
            update_option( self::PAGE_OPTIONS, $this->pagesDefault() );
        }
    }

    private function pagesDefault()
    {
        return [
            [
                'key'       => 'home',
                'title'     => 'Home',
                'wp_id'     => '6208'
            ],
            [
                'key'       => 'cursos',
                'title'     => 'Cursos',
                'wp_id'     => '8952'
            ],
            [
                'key'       => 'conteudos',
                'title'     => 'Conteúdos',
                'wp_id'     => '9453'
            ],
            [
                'key'       => 'parceiros',
                'title'     => 'Parceiros',
                'wp_id'     => '9254'
            ],
            [
                'key'       => 'contato',
                'title'     => 'Contato',
                'wp_id'     => '9264'
            ],
            [
                'key'       => 'about',
                'title'     => 'Quem Somos',
                'wp_id'     => '13315'
            ],
            [
                'key'       => 'terms',
                'title'     => 'Termos e Serviços',
                'wp_id'     => '8073'
            ]
        ];
    }

}