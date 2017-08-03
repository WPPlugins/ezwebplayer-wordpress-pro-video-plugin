<?php


/*
  Plugin Name: EZWebPlayer
  Plugin URI: http://wordpress.org/plugins/ezwebplayer-wordpress-pro-video-plugin/
  Version: 3.2
  Description: Connects your Wordpress Blog to the EZWebPlayer.com service for managing your video content.
  Author: EZWebPlayer.com
  Author URI: http://www.ezwebplayer.com
 */

/**
 * Include configuration 
 */
$wp_wpezp_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

require_once dirname(__FILE__) . '/config.php';


/**
 * Hook for pre-executing post content before output
 *
 * @param text $content - post content
 * @return text - modified post content
 */
function ezwebplayer_replace($content)
{
    $context = context::getInstance();
    $postVideo = $context->getPostVideo();
    
    $userID = get_option('userID');
    $content = $postVideo->preOutput($content, $userID);
    return $content;
}

function ezwebplayer_edit_post($postID){
    $context = context::getInstance();
    $postVideo = $context->getPostVideo();
    $content = $postVideo->postParse($postID);    
}

function ezwebplayer_admin_submenu()
{
    $userID = get_option('userID');
    $userEmail = get_option('userEmail');
    if (empty ($userID) || empty ($userEmail)) {
        $url = admin_url() . 'admin.php?page=/ezwebplayer.php';
        $out = <<<EOF
        <script type="text/javascript">
            window.location.href = '{$url}';
        </script>
EOF;
        printf($out);
        exit();
    } ?>

    <style type="text/css">
        body.wp-admin {min-width: 995px;}
        p {padding: 0; margin: 0;}

        .fleft {float: left;}
        .fright {float: right;}
        .clear {float: none; height: 0px;}

        .w_60 {width: 60px !important;}

        .pt_20 {padding-top: 20px !important;}
        .pb_10 {padding-bottom: 10px !important;}
        .pb_20 {padding-bottom: 20px !important;}
        .pb_30 {padding-bottom: 30px !important;}
        .pl_10 {padding-left: 10px !important;}
        .pl_20 {padding-left: 20px !important;}
        .pl_90 {padding-left: 90px !important;}
        .pr_20 {padding-right: 20px !important;}

        ul {padding: 0; margin: 0;}
        ul li {padding: 0; margin: 0;}

        .head_info{background-color: #F1F1F1; border: 1px solid gray; margin: 0px !important; padding: 23px 33px;}
        .content {padding: 10px; background: #F1F1F1; border: 1px solid gray;}
        .info h1 {margin-top: 50px !important;}
    </style>

        <?php require_once dirname(__FILE__) . '/top_head.php'; ?>
    <div class="head_info">
        <?php require_once dirname(__FILE__) . '/headPostLogin.php'; ?>
    </div>
    <div class="info">
    <?php
        $ezwpToOnePost = true;
        require_once dirname(__FILE__) . '/ezwp_form.php'; ?>
    </div>
    <?php
}

function ezwebplayer_recent_page ()
{
    $userID = get_option('userID');
    $userEmail = get_option('userEmail'); 
    $context = context::getInstance();
    $postVideo = $context->getPostVideo();
    $post_list = $postVideo->postList();
    $post_cat_list = $post_list["catlist"];
?>
<style type="text/css">
    .fleft {float: left;}
    .fright {float: right;}
    .clear {float: none; height: 0px;}
    
    .padl_30 {padding-left: 30px !important;}
    .padt_20 {padding-top: 20px !important;}

    .content{background-color: #F1F1F1; border: 1px solid gray; margin: 0px !important; padding: 23px 33px;}

    #filter li {float: left}
    #filter li label {float: left; padding-top: 4px;}

    #results thead {background: #0059ae; color: #fff; }
</style>

<script type="text/javascript" >
    jQuery(document).ready(function(){
        jQuery('#date_from').datepicker();
        jQuery('#date_to').datepicker();
    });
</script>

<h1>Recently Posted Videos</h1>

<p>Logged in as <?php echo $userEmail ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url() . 'admin.php?page=ezwebplayer.php&action=logout'?>">Log Out</a></p><br>
<p>Recently posted videos | <a href="<?php echo admin_url() . 'admin.php?page=ezwebplayer-wordpress-pro-video-plugin/ezwebplayer.php&action=post_form'; ?>" >Post a video</a></p><br>

<div class="content">
    <div class="fleft">
            <p>Search for a recently posted videos</p>
    </div>

    <div class="fleft filter_block padl_30">
        <form id="filter" action="<?php echo admin_url() . 'admin.php?page=/ezwebplayer.php&action=recent'; ?>" method="get">
            <input name="page" value="ezwebplayer.php" type="hidden" />
            <input name="action" value="recent" type="hidden" />
            
            <ul>
                <li>
                    <label for="name">Name</label>
                    <input name="name" id="name" type="text" value="<?php echo $post_list["filter"]["name"]; ?>" />
                </li>
                <li>
                    <label for="date_from">Date From</label>
                    <input size="9" name="date_from" id="date_from" type="text" value="<?php echo $post_list["filter"]["date_from"]; ?>" />
                </li>
                <li>
                    <label for="date_to">Date To</label>
                    <input size="9" name="date_to" id="date_to" type="text" value="<?php echo $post_list["filter"]["date_to"]; ?>" />
                </li>
                <li>
                    <input type="submit" name="search" value="Search" />
                </li>
                <li>
                    <input type="button" value="Clear" onclick='window.location.href = "<?php echo admin_url() . 'admin.php?page=ezwebplayer.php&action=recent'; ?>"' />
                </li>
            </ul>
        </form>
    </div>
    <div class="clear"></div>

    <div id="results" class="padt_20">
        <table width="95%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td width="30px;">&nbsp;</td>
                    <td>Name</td>
                    <td>Channel</td>
                    <td width="11%">Date added</td>
                    <td width="200px;">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;?>
                <?php foreach ($post_list["list"] as $item) :?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <?php if($item->post_status == "trash"):?>
                        	<td><?php edit_post_link($item->post_title, null, null, $item->ID); ?></td>
                        <?php else: ?>	
                    		<td><a href="<?php echo $item->guid ?>" target="_blank"><?php echo $item->post_title; ?></a></td>
                        <?php endif; ?>
                        <td>
                            <?php if (key_exists($item->ID, $post_cat_list)):?>
                        	<?php echo implode(", ", $post_cat_list[$item->ID])?>
                            <?php endif;?>
                        </td>
                    	<td><?php echo date('m/d/Y', strtotime($item->post_date)); ?></td>
                        <td>
                            <?php edit_post_link('Edit', null, null, $item->ID); ?> |
                            <?php if($item->post_status == "trash"):?>
                            <?php edit_post_link('View Post', null, null, $item->ID); ?>
                            <?php else: ?>
                            <a href="<?php echo $item->guid ?>" target="_blank">View Post</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
    <?php
}

function ezwebplayer_options_page()
{
    global $wp_wpezp_plugin_url;
    switch ($_GET['action']) :
        case 'post_form' :
            do_action('post_form');
            exit();
        case 'logout' :
            // Logout user
            update_option('userID', false);
            update_option('userEmail', false);
    endswitch;

    require_once dirname(__FILE__) . '/nusoap/nusoap.php';

  define("EZWP_WSDL_URL", get_option("ezwpWSDLurl"));
  define("EZWP_LOGIN_WSDL_URL", get_option("ezwpLOGINWSDLurl"));
  define("EZWP_DEFAULT_UID", get_option("userID"));

    $msg = "";
    $error = true;
    try {
        $client = new nusoap_client(EZWP_WSDL_URL, true);
        $login = new nusoap_client(EZWP_LOGIN_WSDL_URL, true);
    } catch (Exception $e) {
        echo "couldn't connect to server";
        return;
    }

    if (isset($_POST) && !empty($_POST) && array_key_exists('login', $_POST)) {
        $uL = $_POST['userLogin'];
        $uP = $_POST['userPass'];
//        $userID = $client->call("LoginUser", array("username" => "$uL", "password" => $uP));
        $userID = $login->call("Login", array("username" => "$uL", "password" => $uP));

//        if (strtolower($userID['LoginUserResult']) != 'failed') {
        if (strtolower($userID['LoginResult']['Status']) != 'failed') {
            $error = false;
//            update_option('userID', $userID['LoginUserResult']);
            update_option('userID', $userID['LoginResult']['Result']);
            update_option('userEmail', $uL);
            
//            $msg = "Login Success!";
        } else {
//            $client->call("LogoutUser");
            $login->call("LogoutUser");
            update_option('userID', false);
            update_option('userEmail', false);
            $msg = "There has been an error with your login information.<br/>
                    Please enter your email and password again.";  
                    print_r(error_get_last());                  
        }
    }

    if (isset($_POST) && !empty($_POST) && array_key_exists('change_password', $_POST)) {
        $changePassword = $login->call("ChangePassword", array("username" => $_POST['email'], "oldpassword" => $_POST['oldpassword'], "newpassword" => $_POST['newpassword']));

//        echo '<pre>';
//        print_r($changePassword);
//        exit();

        if (strtolower($changePassword['ChangePasswordResult']['Status']) === 'success') {
//            echo '<pre>';
//            print_r($changePassword);
//            exit();
            update_option('userID', $changePassword['ChangePasswordResult']['Result']);
            update_option('userEmail', $_POST['email']);
            $error = false;
            $msg = "Your password is changed successfully.";
        } else {
            $msg = "There has been an error with your change login information.<br/>
                    Please enter your old and new password again.";
        }
    }

    $userID = get_option('userID');
    $userEmail = get_option('userEmail');

//    echo '<pre>';
//print_r($userID);
//print_r($userEmail);
//exit();
?>
    <style type="text/css">
        body.wp-admin {min-width: 995px;}
        p {padding: 0; margin: 0;}

        .fleft {float: left;}
        .fright {float: right;}
        .clear {float: none; height: 0px;}

        .w_60 {width: 60px !important;}

        .pt_20 {padding-top: 20px !important;}
        .pb_10 {padding-bottom: 10px !important;}
        .pb_20 {padding-bottom: 20px !important;}
        .pb_30 {padding-bottom: 30px !important;}
        .pl_10 {padding-left: 10px !important;}
        .pl_20 {padding-left: 20px !important;}
        .pl_90 {padding-left: 90px !important;}
        .pr_20 {padding-right: 20px !important;}

        ul {padding: 0; margin: 0;}
        ul li {padding: 0; margin: 0;}

        .head_info{background-color: #F1F1F1; border: 1px solid gray; margin: 0px !important; padding: 23px 33px;}
    </style>
    <div class="wrap" >
    <!--    <div style="float:left;margin:15px 0px;"><img title="EZWebPlayer Lite Wordpress Plugin" src="<?php echo get_option('home') ?>/wp-content/plugins/ezwebplayer/logo.png" /><br /></div>
        <div style="float:right">
            <span><a target="_blank" href="http://www.ezwebplayer.com/contact-us/" title="Plug-In home page">Support</a></span> |
            <span><a target="_blank" href="https://www.ezwebplayer.com/Login.aspx" title="Plug-In home page">EZWebPlayer Login</a></span>
            <span><a target="_blank" href="https://www.ezwebplayer.com/login.aspx" title="">Plug-In Home Page</a></span>
        </div>

        <div class="clear"></div>-->

<?php require_once dirname(__FILE__) . '/top_head.php';
  
        switch ($_GET['action']) :
            case 'recent' :
                do_action('recent_page');
                break;
            case 'delete' :
                wp_delete_post($_GET['pid'], true);
                do_action('recent_page');
                break;
            default :
    ?>

    <div class="head_info">
        <?php if ($userID && $userEmail) {
            ?>

            <div class="fleft pl_10">
                <div class="fleft"><img src="<?php echo $wp_wpezp_plugin_url . '/img/thumb_1.png' ?>" alt="" /></div>
                <div class="fleft pl_10 pt_20"><a href="<?php echo admin_url() . 'admin.php?page=ezwebplayer-wordpress-pro-video-plugin/ezwebplayer.php&action=post_form' ?>">Click here</a> to automaticaly<br />
                import and create post <br />
                for you videos.</div>
            </div>
            <div class="fleft pb_10">
                <div class="fleft pl_10"><img src="<?php echo $wp_wpezp_plugin_url . '/img/thumb_2.png' ?>" alt="" /></div>
                <div class="fleft pl_10 pt_20">You can insert videos into your post <br />
                by clicking this play icon in the post toolbar.</div>
            </div>

            <div class="clear"></div>

            <?php
        } else {
            require_once dirname(__FILE__) . '/headPreLogin.php';
        } ?>
    </div>

    <div class="info">
    <?php
    if ($userID && $userEmail) {
        require_once dirname(__FILE__) . '/logged.php';
    } else {
        require_once dirname(__FILE__) . '/loginForm.php';
    } ?>
    </div>

    <?php //require_once dirname(__FILE__) . '/loginForm.php'; ?>
    <?php
    $plopt = 1;
    $ezwpToOnePost = true;
    //require_once dirname(__FILE__) . '/ezwp_form.php' ?>
    <!--<div class="ezwp-h1"><h1>FAQ</h1></div>
    <table class="form-table ezwp-table" style="margin-bottom:15px">
        <tbody>
            <tr>
                <td>
                    <ul class="ezwp-faq-links">
                        <li><a href="#" title="#">How do I find my "User ID"?</a></li>
                        <li><a href="#" title="#">How do I add videos to my EZWebPlayer?</a></li>
                        <li><a href="#" title="#">How do I add categories to my EZWebPlayer?</a></li>
                        <li><a href="#" title="#">How do I add a video to a Channel?</a></li>
                        <li><a href="#" title="#">How do I go to set up my video's "Extra Options"?</a></li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>-->
    </div>


    <?php endswitch;
    
}
/********************************************************/
/*               Plugin management hooks                */
/*                     functions                        */  
/********************************************************/
function ezwebplayer_activate()
{
    /*
     * Activate this plugin
     */
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    add_option('userID', EZWP_DEFAULT_UID);
    add_option('ezwpWSDLurl', EZWP_WSDL_URL);
    $table_name = $wpdb->prefix . "ezvideos";
    $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
        vKey VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        postID MEDIUMINT(8) UNSIGNED NOT NULL,
        PRIMARY KEY (`vKey`, `postID`)
    );";
    dbDelta($sql);

    $table_name = $wpdb->prefix . "ezcategories";
    $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
	  cID mediumint(9) NOT NULL,
          cName varchar(255) default '',
          locID mediumint(9) NOT NULL,
          PRIMARY KEY (`cID`)
	);";
    dbDelta($sql);
}

function ezwebplayer_deactivate()
{
    delete_option('userID');
    delete_option('ezwpWSDLurl');
    delete_option('userEmail');
}

/**
 * Uninstall hook calls before deleting plugin files
 * and other WP typical action plugin remove process
 *
 */
function ezwebplayer_uninstall()
{
    delete_option('userID');
    delete_option('ezwpWSDLurl');
    delete_option('userEmail');

    global $wpdb;
    $sql = "DROP TABLE {$wpdb->prefix}ezvideos";
    $wpdb->query($sql);
    $sql = "DROP TABLE {$wpdb->prefix}ezcategories";
    $wpdb->query($sql);
}

/********************************************************/
/*             End Plugin management hooks              */
/*                     functions                        */  
/********************************************************/

function ezwebplayer_admin_menu()
{
    /*
     * Add new menu at Settings menu
     */
    $page = add_options_page('EZWebPlayer options', 'EZWebPlayer', 8, 'ezwebplayer.php', 'ezwebplayer_options_page');

    // If user logedin that view sub menu EzWebPlayer
//    if (get_option('userID') && get_option('userEmail')) {
//        echo __LINE__ . '<br>';
//        add_menu_page('ezwebplayer', 'EZWebPlayer', 8, __FILE__, 'ezwebplayer_admin_submenu');
//    } else {
//        echo __LINE__ . '<br>';
//        echo '<pre>';
//        print_r('userId' . get_option('userID'));
//        print_r('Email' . get_option('userEmail'));
//    }

    add_menu_page('ezwebplayer', 'EZWebPlayer', 8, __FILE__, 'ezwebplayer_admin_submenu');

    /* Using registered $page handle to hook script load */
    add_action('admin_print_scripts-' . $page, 'ezwebplayer_js');
}

function ezwebplayer_head()
{
    /*
     * Add DynoDb javascript code to wordpress 
     */
    wp_enqueue_script('jquery');
    //wp_enqueue_script( 'ezwebplayer' , 'http://ezwebplayer.com/Scripts/flashplayerhost.min.js' );
}

function ezwebplayer_media_buttons_context($fl)
{
    global $wp_wpezp_plugin_url;
    /*
     * Button at new post page
     */
    $fl .= "
<a href=\"" . $wp_wpezp_plugin_url . "/ezwp_form.php?iframe=true&TB_iframe=true\" id=\"add_ezwp_video\" class=\"thickbox\" title='Add EZWebPlayerVideos' onclick=\"return false;\"><img src='" . $wp_wpezp_plugin_url . "/img/playicon.png' height='27' alt='Add EZWebPlayer Videos' /></a>";
    return $fl;
}

function ezwebplayer_delete_categoty($categoryID)
{
    /*
     * Delete row from ezcategories table when user delete category from wordpress
     */
    global $wpdb;
    $sql = "DELETE FROM {$wpdb->prefix}ezcategories WHERE locID = $categoryID";
    $wpdb->query($sql);
}

function ezwebplayer_delete_post_videos($postID)
{
    global $wpdb;
    if ($postID > 0){
        $sql = "DELETE FROM {$wpdb->prefix}ezvideos WHERE postID = $postID";
        $wpdb->query($sql);
    }
}

function ezwebplayer_admin_init()
{
    /* Register our script and style. */
    wp_register_script('jqueryUI', WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__)) . '/js/jquery-ui-1.7.3.custom.min.js');
    wp_register_style('jqueryUICSS', WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__)) . '/css/ui-lightness/jquery-ui-1.7.3.custom.css');
}

function ezwebplayer_js()
{
    wp_enqueue_script('jqueryUI');
}

function ezwebplayer_style()
{
    wp_enqueue_style('jqueryUICSS');
}

function ezwebplayer_post_form()
{
    require_once dirname(__FILE__) . '/ezwp_form.php';
}

/**
 * Helper for generation ezWebPlayer Message
 * 
 * Another word: admin notice listener
 *
 */
function ezw_message(){
    global $ezw_messages;
   $cur_message = $_GET["ezw_message"];
    if(isset($cur_message) && key_exists($cur_message, $ezw_messages)){
        echo  "<div class='" . $ezw_messages[$cur_message]["style"] . "'>" . $ezw_messages[$cur_message]["text"] . "</div>";
    }
}


add_filter("media_buttons_context", "ezwebplayer_media_buttons_context");
add_action('admin_notices', 'ezw_message');
//Hook for pre-execute output
add_filter('the_content', 'ezwebplayer_replace');
//Hook for update post: create or delete
add_action('edit_post', 'ezwebplayer_edit_post');
//Hook for delete post: delete records related with deleted post
add_action('deleted_post', 'ezwebplayer_delete_post_videos');


/********************************************************/
/*               Plugin management hooks                */
/********************************************************/
register_activation_hook(__FILE__, 'ezwebplayer_activate');
register_deactivation_hook(__FILE__, 'ezwebplayer_deactivate');
register_uninstall_hook(__FILE__, 'ezwebplayer_uninstall');
/********************************************************/

add_action('admin_print_styles', 'ezwebplayer_style');
add_action('admin_init', 'ezwebplayer_admin_init');

add_action('admin_menu', 'ezwebplayer_admin_menu');
add_action('template_redirect', 'ezwebplayer_head');
add_action("delete_category", "ezwebplayer_delete_categoty");
//add_action("delete_post", "ezwebplayer_delete_post");
add_action("recent_page", "ezwebplayer_recent_page");
add_action("post_form", "ezwebplayer_post_form");
add_action("option_page", "ezwebplayer_options_page");

?>