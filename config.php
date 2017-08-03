<?php
/**
 * In this file we define constants for EZWebPlayer Plugin
 * 
 * @author  EZWebPlayer 
 * @package EZWebPlayer Pro
 * @Version 3.2
 */
//live
define("EZWP_LOGIN_WSDL_URL","https://www.ezwebplayer.com/webservices/v1/AuthorizationService.asmx?wsdl");
define("EZWP_WSDL_URL","http://www.ezwebplayer.com/webservices/v5/videoservice.asmx?wsdl");

define('WP_EZWEB_PLUGIN_DIR', dirname(__FILE__));
define('WP_POST_REVISIONS', false);

define("EZWP_DEFAULT_UID", "00000000-0000-0000-0000-000000000000");
require_once WP_EZWEB_PLUGIN_DIR . '/nusoap/nusoap.php';
require_once WP_EZWEB_PLUGIN_DIR . '/context.class.php';
require_once WP_EZWEB_PLUGIN_DIR . '/postVideo.class.php';
require_once WP_EZWEB_PLUGIN_DIR . '/soapVideo.class.php';

if ( function_exists('register_uninstall_hook') ){
    register_uninstall_hook(__FILE__, 'my_uninstall_hook');
}

/**
 * success_post_created
 * error_post_created
 * error_empty_video_set
 */

$ezw_messages = array(
    "xml_error" => array(),
    "success_post_created" => array(
        "status"  => "success",
        "style"   => "updated",
        "text"    => "A post of your selected video has been created"
    ),
    "success_multiple_post_created" => array(
        "status"  => "success",
        "style"   => "updated",
        "text"    => "Posts of your selected videos have been created"
    ),                                                   
    "error_post_created" => array(
        "status"  => "error",
        "style"   => "error",
        "text"    => "Fault in post creation. Please try again."
    ),
    "error_empty_video_set" => array(
        "status"  => "error",
        "style"   => "error",
        "text"    => "You should select one or more videos."
    )                                                                                                          
);

?>