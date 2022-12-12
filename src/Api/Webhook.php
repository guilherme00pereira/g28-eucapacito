<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\Logger;

class Webhook
{

    const HASH = "b6733291dc8383f5940917a98863fa5123816d53c116a7fee42ae5db0140627c";
    const TYPES_TO_REVALIDATE = [
        'blog',
        'bolsa_de_estudo',
        'course',
        'curso_ec',
        'e-book',
        'empregabilidade',
        'jornada',
        'partner',
        'video'
    ];

    public function __construct()
    {
        add_action('save_post', [ $this, 'dispatch' ], 10, 3);
    }

    public function dispatch($id, $post, $updated)
    {
        Logger::getInstance()->add("Webhook", "Atualizando post: " . $id . ", do tipo " . $post->post_type);
        $type       = $post->post_type === "post" ? "blog" : $post->post_type;
        if( in_array( $type, self::TYPES_TO_REVALIDATE ) ) {
            $url        = "https://eucapacito.com.br/api/revalidate?secret=" . self::HASH . "&entity=" . $type . "&slug=" . $post->post_name;
            Logger::getInstance()->add("Webhook", $url);
            $response   = wp_remote_post($url);
            if( is_wp_error( $response ) )
            {
                Logger::getInstance()->add("Webhook", "Erro ao atualizar post: " . $post->post_name . " no React");
            }
            else
            {
                Logger::getInstance()->add("Webhook", "Retorno api react: " . json_decode( wp_remote_retrieve_body( $response ) ) );
            }
        }
    }
}
