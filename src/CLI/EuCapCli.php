<?php

namespace G28\Eucapacito\CLI;

use G28\Eucapacito\Core\ImageConverter;
use WP_CLI;

class EuCapCli
{

    public function __construct()
    {

    }

    public function webp( $args )
    {
        WP_CLI::line("Iniciando conversão da imagem...");
        [$message, $ret] = ImageConverter::generetaWebpFile( $args[0] );
        WP_CLI::line("Mensagem: " . $message);
        WP_CLI::line("R: " . $ret);
    }
}