<?php

namespace G28\Eucapacito\Core;

class Logger
{
    protected static $_instance = null;
    private $file;

    public function __construct()
    {
        $this->file         = Plugin::getDir() . 'logs/log.txt';
    }

    public static function getInstance(): ?Logger {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function add( string $message ) {
        $timestamp    = date('d/m/Y h:i:s A');
        $output = "[ $timestamp ] - $message" . PHP_EOL;
        file_put_contents( $this->file, $output, FILE_APPEND);
    }
}