<?php
/*
Plugin Name: G28 EuCapacito Plugin
Plugin URI: #
Description: Funcionalidades e endpoints para o webapp feito em React
Version: 0.1.23
Author: Guilherme Pereira - G28
Author URI: #
Text Domain: g28-eucapacito
Domain Path: /languages
*/

use G28\Eucapacito\Startup;

if ( ! defined( 'ABSPATH' ) ) exit;

require "vendor/autoload.php";

$startup = Startup::getInstance();
$startup->run( __FILE__ );