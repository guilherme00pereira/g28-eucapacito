<?php

namespace G28\Eucapacito\CLI;

use WP_CLI;

class CliRegistrator
{
    public function __construct()
    {
        add_action( 'cli_init', [ $this, 'init' ] );
    }

    public function init()
    {
        WP_CLI::add_command('g28', 'G28\Eucapacito\CLI\EuCapCli');
    }
}