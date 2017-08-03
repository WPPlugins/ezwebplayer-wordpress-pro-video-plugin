<?php
if (!defined('WP_INSTALLING'))
    define('WP_INSTALLING', TRUE);
require_once( '../../../wp-config.php' );
require_once( '../../../wp-settings.php' );
/**
 * Include configuration 
 */
require_once dirname(__FILE__) . '/config.php';
$context = context::getInstance();
$soapVideo = $context->getSoapVideo();

global $wpdb;

/**
 * We need to check: exist or not category(term) with the same name as current category
 */
$category['Name'] = 'Category 1';

$sql = "SELECT `term_id` FROM {$wpdb->terms} WHERE `name` = '{$category['Name']}'";
$term_tmp = $wpdb->get_var($sql);

$actiontype = $_POST['ezwp-post-type'];
$allInOne = $_POST['ezwpToOnePost']; //if ezwp_form.php runs from post_new.php

$userID = get_option("userID");

/**
 * Check, how video was selected:
 * 									- video select
 * 									- categories and then video 
 */
/**
 * $by_categories flag we use for show category tab after update
 */

$by_categories = false;
if ($actiontype == 'by-videos') {
    $videos = $_POST['ezwp-sbc-list'];
}
if ($actiontype == 'by-categories') {
	$by_categories = true;
    $videos = $_POST['ezwp-videos-by-cats'];

    $selected_categories = (array)$_POST['ezwp-categories'];
    $selected_categories_str = implode(",", $selected_categories);
}

$insertQueryVals = '';
$post = array();


/**
 * Check, Is user create new post or edit exists yet
 * if $allInOne == false, then we create new post 
 */

    if (is_array($videos) && count($videos) > 0) {
        //common post info
        $post['post_status'] = 'publish';
        $title = array();
        $post_categories = array();
                
        foreach ($videos as $video) {
            $post['post_status'] = 'publish';
            $title = array();
            $post_categories = array();
            
            
            $video = explode("|", $video);
            $title[] = stripslashes($video[1]);
            $video = $video[0];
			$video_info   = $soapVideo->get_video($video, $userID);
            /**
             * Generate post tags
             */
            if ($allInOne === 'false') {
                $post['post_content'] = str_replace('"',"'", $video_info['IFrameCode'])."<br />";
                $post['post_content'] .= ( $_POST['ezwpextra'][0] == 'on') ? "Title: [VideoTitle VideoID=$video/] <br />" : ''; 
                $post['post_content'] .= ( $_POST['ezwpextra'][1] == 'on') ? "Description: [VideoDescription VideoID=$video/] <br />" : ''; 
                $post['post_content'] .= ( $_POST['ezwpextra'][2] == 'on') ? "View count: [VideoViewCount VideoID=$video/] <br />" : ''; 
            } else {
				$post['post_content'] .= str_replace('"',"'", $video_info['IFrameCode'])."<br />";
                $post['post_content'] .= ( $_POST['ezwpextra'][0] == 'on') ? "Title: [VideoTitle VideoID=$video/] <br />" : ''; 
                $post['post_content'] .= ( $_POST['ezwpextra'][1] == 'on') ? "Description: [VideoDescription VideoID=$video/] <br />" : ''; 
                $post['post_content'] .= ( $_POST['ezwpextra'][2] == 'on') ? "View count: [VideoViewCount VideoID=$video/] <br />" : ''; 
            }

            if ($allInOne === 'false') {
                
                
                /**
                 * Get category LIST, because one video can related more than one category
                 */
                $categories = $soapVideo->get_categories_for_video($video, $userID);
                if (count($categories) == 0){
                    $categories = array(0 => array("ID" => -1, 'Name' => "uncategorized"));
                }

                /**
                 * Circle on wp_ezcategories
                 */
                foreach ($categories as $category) {
                    $slug = str_replace(" ", "uncategorized", $category['Name']); //replace spaces to -
                    $slug = preg_replace("/[^a-zA-Z1-9\-]/", '', $slug); //delete all symbols which not in (S | S in {a-z,A-Z.0-9})
                    $slug = strtolower($slug);

                    $sql = "SELECT `locID` FROM {$wpdb->prefix}ezcategories WHERE `cID` = {$category['ID']}";
                    $res = $wpdb->get_var($sql);

                    if (!isset($res)) {
                        /**
                         * We need to check: exist or not category(term) with the same name as current category
                         */
                        $sql = "SELECT `term_id` FROM {$wpdb->terms} WHERE `name` = '{$category['Name']}'";
                        $term_tmp = $wpdb->get_var($sql);

                        if ($wpdb->num_rows == 1){
                            $res['term_id'] = $term_tmp;
                        }else{
                            $res = wp_insert_term($category['Name'], "category", array('slug' => "$slug"));
                        }

                        $sql = "INSERT INTO {$wpdb->prefix}ezcategories VALUES({$category['ID']},\"{$category['Name']}\",{$res['term_id']})";

                        $wpdb->query($sql);
                        $res = $res['term_id'];

                    } else {
                        wp_update_term($res, 'category', array('name' => $category['Name']));
                    }

                    $post_categories[] = $res;
                }
                
                $post['post_category'] = $post_categories;
                $post['post_title'] = $title[0];
                
                $pid = wp_insert_post($post);
                
                $tmp["postID"] = $pid;
                $video_t = explode("|", $video);
                $video_t = $video_t[0];
                $tmp["vkey"] = $video_t;
                $wpdb->insert( $wpdb->prefix . "ezvideos", $tmp);
                
            }
        }
//        exit();

        if ($allInOne === 'false') {
//            $post['post_category'] = $post_categories;
//            /**
//             * Generate post title
//             */
//            if (count($videos) > 1){
//                $title = "My videos: " . implode(", ", $title);
//            }else {
//                $title = $title[0];
//            }
//
//            $post['post_title'] = $title;
//            
//            echo '<pre>';
//            print_r($post);
//            exit();
//            
//            $pid = wp_insert_post($post);
//
//            $tmp["postID"] = $pid;
//            foreach ($videos as $video) {
//                $video = explode("|", $video);
//                $video = $video[0];
//                $tmp["vkey"] = $video;
//                $wpdb->insert( $wpdb->prefix . "ezvideos", $tmp);
//            }

        	if (count($videos) == 1){
                $status_message = "success_post_created";
        	}else{
        		$status_message = "success_multiple_post_created";
        	}
        }
    } else {
        $status_message = "error_empty_video_set";
    }


if ($allInOne == 'true'):
?>
    <script type="text/javascript">
        /* <![CDATA[ */
        var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor("<?php echo $post['post_content'] ?>");
        /* ]]> */
    </script>
<?php
    else:
        $url = $_SERVER['HTTP_REFERER'];
        
        //clear url from old message
        if(($pos = strpos($url, "&ezw_message")) !== false){
            $url = substr($url, 0, $pos);
        }
        if ($status_message != '') $url .= "&ezw_message=" . $status_message;
        if ($by_categories){
            $url .= "&by_categories=1&cat_list=" . $selected_categories_str;
        }
        
        header("Location: {$url}");
        exit();
    endif;
?>