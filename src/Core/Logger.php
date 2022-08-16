<?php

namespace G28\Eucapacito\Core;

class Logger
{
    protected static $_instance = null;
    private $file;
    private string $actualFilename;

    public function __construct()
    {
        $this->actualFilename   = "log_" . date("Ymd") . ".txt";
        $this->file             = Plugin::getDir() . 'logs/' . $this->actualFilename;
    }

    public static function getInstance(): ?Logger {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function add( string $origin, string $message ) {
        date_default_timezone_set('America/Sao_Paulo');
        $timestamp    = date('d/m/Y h:i:s A');
        $output = "[ $timestamp ] $origin => $message" . PHP_EOL;
        file_put_contents( $this->file, $output, FILE_APPEND);
    }

    public function getLogFiles()
    {
        return array_diff( scandir(Plugin::getDir() . 'logs', SCANDIR_SORT_DESCENDING), array('.', '..'));
    }

    public function getLogFileContent( $file = null )
    {
        if( is_null( $file ) ) {
            return [ $this->actualFilename, nl2br(file_get_contents( $this->file )) ];
        } else {
            $filepath = Plugin::getDir() . 'logs/' . $file;
            return [ $file, nl2br(file_get_contents( $filepath )) ];
        }
    }
}