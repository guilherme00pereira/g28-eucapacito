<?php
/*
Plugin Name: G28 EuCapacito Plugin
Plugin URI: #
Description: Adiciona uma página de negociação com integração ao checkout transparente do Arespay
Version: 0.1.2
Author: Guilherme Pereira G28
Author URI: #
Text Domain: g28-eucapacito
Domain Path: /languages
*/

use G28\Eucapacito\Startup;

if ( ! defined( 'ABSPATH' ) ) exit;

require "vendor/autoload.php";

$startup = Startup::getInstance();
$startup->run( __FILE__ );