<?php

namespace G28\Eucapacito\Options;

class PageOptions 
{
    const PAGE_OPTIONS = 'eucapacito_page_relationship';

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $options = get_option(self::PAGE_OPTIONS);
        if( is_bool( $options ) || empty( $options ) )
        {
            update_option( self::PAGE_OPTIONS, $this->pagesDefault() );
        }
    }

    public function getPagesRelationship()
    {
        return get_option(self::PAGE_OPTIONS);
    }

    public function resetRelations()
    {
        update_option( self::PAGE_OPTIONS, $this->pagesDefault() );
    }

    private function pagesDefault(): array
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
                'key'       => 'oportunidades',
                'title'     => 'Oportunidades',
                'wp_id'     => '8573'
            ],
            [
                'key'       => 'parceiros',
                'title'     => 'Parceiros',
                'wp_id'     => '9254'
            ],
            [
                'key'       => 'videos',
                'title'     => 'Vídeos',
                'wp_id'     => '9529'
            ],
            [
                'key'       => 'ebooks',
                'title'     => 'E-Books',
                'wp_id'     => '9536'
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