<?php
/*
 * Plugin Name: JoeBooking
 * Plugin URI: https://www.joebooking.com/
 * Description: Time slot booking plugin. 
 * Version: 7.0.3
 * Author: hitcode.com
 * Author URI: https://www.hitcode.com/
 * Text Domain: joebooking
 * Domain Path: /languages/
*/

include_once( dirname(__FILE__) . '/joebooking7-base.php' );

$configFile = __DIR__ . '/config.php';
$appConfig = file_exists($configFile) ? include( $configFile ) : array();

$dir = isset( $appConfig['code-dir'] ) ? $appConfig['code-dir'] : __DIR__;
if( ! class_exists('HC4_App') ){
	include_once( $dir . '/hc4/app.php' );
}

$hcjb7 = new JoeBooking7_Wordpress( __DIR__, $appConfig );