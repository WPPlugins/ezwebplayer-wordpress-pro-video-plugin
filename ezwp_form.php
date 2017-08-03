<?php
//function ezwebplayer_form_page () {
//print_r($_REQUEST);return;

$iframe = $_REQUEST["iframe"] == "true" ? true : false;
ini_set('display_errors', 'On');
if(!isset($plopt)):
    if(!defined('WP_INSTALLING'))
        define('WP_INSTALLING',TRUE);
    
    if (!array_key_exists('page', $_REQUEST)) {
        require_once( '../../../wp-config.php' );
        //require_once( '../../../wp-settings.php' );
    } else {
        //require_once( get_home_path() . 'wp-config.php' );
        //require_once( get_home_path() . 'wp-settings.php' );
    }
    require_once dirname(__FILE__) . '/nusoap/nusoap.php';

    /**
     * Include configuration 
     */
    require_once dirname(__FILE__) . '/config.php';
    
    $wp_wpezp_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
    global $wp_wpezp_plugin_url;
    ?>
<link href="<?php echo admin_url();?>/load-styles.php?c=0&amp;dir=ltr&amp;load=dashicons,admin-bar,wp-admin,buttons,wp-auth-check&amp;ver=3.9.1"  rel="stylesheet" type="text/css" />
<script src="<?php echo includes_url() . '/js/jquery/jquery.js'; ?>" type="text/javascript" ></script>
<?php
endif;

global $wpdb;
$sql = "SELECT vKey, postID FROM {$wpdb->prefix}ezvideos";
$postedVideos = $wpdb->get_results($sql,ARRAY_A);

$newPV = array();
if(isset($postedVideos)) {
    foreach($postedVideos as $pV) {
        $newPV[$pV['vKey']] = $pV['postID']; //sort videos by video ID
    }
}
$postedVideos = $newPV;

$sql = "SELECT cID, locID FROM {$wpdb->prefix}ezcategories";
$addedCategories = $wpdb->get_results($sql,ARRAY_A);

$newAC = array();
if(isset($addedCategories)) {
    foreach($addedCategories as $aC) {
        $psts = get_posts("category={$aC['locID']}");
        if(!empty($psts))
            $newAC[$aC['cID']] = $aC['locID']; //sort categories by category ID
    }
}

$addedCategories = $newAC;
$msg = "";

try {
    $client = new nusoap_client(EZWP_WSDL_URL,true);
}
catch(Exception $e) {
    echo "couldn't connect to server";
    return;
}

$userID = get_option('userID');
$userEmail = get_option('userEmail');
$context = context::getInstance();
$soapVideo = $context->getSoapVideo();
?>
<style type="text/css">

<?php if(!$iframe): ?>
    body{margin:0px !important;}
<?php else: ?>
    body{margin:10px !important;}
<?php endif; ?>   

<?php if(!isset($plopt)): ?>
      /*body{margin:10px !important;}*/
<?php endif; ?>
    .ezwp-faq-links{
        background-color:white;
        border:1px solid gray;
        margin:10px;
        padding:10px
    }
    .ezwp-faq-links li{        
    }
    .ezwp-h1{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .ezwp-tabs{
        margin: 10px 140px 10px 10px;
    }
    .ezwp-select-css{
        height:200px !important;
        /*overflow-y:scroll;*/
        background-color: white;
        width:100%;
    }
    .ezwp-sbv-list-block{
    	<?php if((int)$_REQUEST["by_categories"] == 1): ?>
            <?php if(!isset($plopt)): ?>
                width:50.9% !important;
            <?php else: ?>
                width:50.1% !important;
            <?php endif; ?>
        <?php else: ?>
            <?php if(!$iframe): ?>
        		width: 101% !important;
	        <?php else: ?>	
	        	width: 102% !important;
        	<?php endif; ?>
        <?php endif; ?>    
        
    }
    .ezwp-sbv-list{}

    .ezwp-whitebackground{
    	background-color: white; 
    	overflow: hidden; 
    	border:1px solid gray;
    	width: 100%;
    }
    
    form {
    	width: 100%; 	
    }

	.ezwp-extraoptions{margin-top:10px;min-height:203px;}
    #ezwp-extra{margin-left:20px;}
    .ezwp-rightcolumn{float:right; margin-bottom:10px; margin-right:10px; width:200px;}
    .ezwp-rceo li{display:block !important;}
    .ezwp-rightcolumn div{border:1px solid gray; background-color: #f1f1f1;}
    UL.tabNavigation {
        list-style: none;
        margin:0 0 0 5px;
        padding: 0;
    }

    UL.tabNavigation LI {
        display: inline;
        margin-right:5px;
    }

    UL.tabNavigation LI A {
		padding: 2px 5px;
		background-color: #F1F1F1;
        border: 1px solid gray;
        border-bottom: none;
        color: #000;
        text-decoration: none;
    }

    UL.tabNavigation LI A.selected{
        border-bottom: 2px solid #F1F1F1;
        
    }

    UL.tabNavigation LI A:focus {
        outline: 0;
    }

    div.ezwp-tabs > div {
        <?php if(!isset($plopt)): ?>
            margin-top: 2px;
        <?php else: ?>
            margin-top: 1px;
        <?php endif; ?>        
        border: 1px solid gray;
        background-color: #F1F1F1;
        min-height:283px;
    }

    div.ezwp-tabs > div h2 {
        margin-top: 0;
    }
    .ezwp-sadssa{
        margin-top:10px;
        margin-bottom:10px;
    }
    .ezwp-sadssa li{
        list-style: none;
    }
    .ezwp-submitbtn{
        background-color:transparent !important;
        border:none !important;
        text-align:right;
        margin-right:10px;
        margin-top:10px;
    }
    .ezwp-posted-videos{
        background-color:gray;
        color:white;
    }
    .ezwp-not-posted-videos{
        background-color:white;
        color:black;
    }
    .ezwp-categories{
        width:49.5% !important;
    }
    .ezwp-videos-for-categories{
        float:right;
        width:49.5%;
    }
    .ezwp-rightcolumn li{
        list-style: none;
        margin-right:10px;
    }
    .ezwp-keys-left{
        margin-left: 20px;
        margin-bottom:10px;
        cursor: default;
    }

    .ezwp-keys-left li label{
        cursor: default;
    }
    
    #loginField{
    }
    .ezwp-login-field{
        margin-left: 10px;
        margin-right: 20px;
    }
    .ezwp-seachByCategory{
        margin:10px;
        <?php if(!$iframe): ?>
			width:97.2%;
		<?php else: ?>
			width:95.5%;
		<?php endif; ?>
    }
    .marl_20 {margin-left: 20px;}
    
    <?php if(!$iframe): ?>
        .ezwp-rightcolumn{ width: 21% !important; }
    	.ezwp-leftcolumn{ width: 76% !important; }
	<?php else: ?>
        .ezwp-rightcolumn{ width: 21% !important; }
    	.ezwp-leftcolumn{ width: 74% !important; }	
	<?php endif; ?>
</style>

<!--[if IE]>
	<style type="text/css">
		<?php if($iframe): ?>
	
		
		<?php endif ?>
		
		
        .ezwp-rightcolumn li{
            <?php if(!isset($plopt)): ?>
            margin-left:0px !important;
            <?php endif; ?>
        }
        .ezwp-ie-tabs{
            height:280px;
        }
        <?php if(!isset($plopt)): ?>
        	
            .ezwp-tabs{
            	<?php if(!$iframe): ?>
                width:100%;
                <?php else: ?>
                width:82.5% !important;
                <?php endif; ?>
            }
        	
        	div.ezwp-tabs  .ezwp-ie-tabs {
                margin-top: 3px;
                border: 1px solid gray;
                background-color: #F1F1F1;
                min-height:277px;
                height:290px;
            }
            
        	div.ezwp-tabs div h2 {
                margin-top: 0;
            }
            .ezwp-ie-cats{
                width:99%;
            }
            UL.tabNavigation {
                margin-top:5px;
            }
            .ezwp-sbv-list-block{
                width: 101% !important;
            }
            
            /*******************************/
            /*Begin: small changies - Rinat*/
            /*******************************/
            
            .ezwp-videos-by-categories{
            	width:98% !important;
            }
            
            .ezwp-submitbtn{
            	margin-bottom: 10px;
            }
                
            .ezwp-by-videos{
                width:100% !important;
            }  
         
        	/*end: small changies - Rinat*/
            /*******************************/
            
            .ezwp-rightcolumn{
                margin-top:10px;
            }
            
            .ezwp-seachByCategory{
                margin-bottom:0px;
            }
            

        <?php endif; ?>		
    </style>
<![endif]-->

<?php if(isset($plopt)): ?>
<!--[if IE 6]>
<style type="text/css">
    .ezwp-tabs{
        width:75% !important;
    }
    div.ezwp-tabs  .ezwp-ie-tabs {
        margin-top: 3px;
        border: 1px solid gray;
        background-color: #F1F1F1;
        height:305px;
    }
    UL.tabNavigation {
        margin-top:10px;
    }
</style>
<![endif]-->
<?php endif; ?>

<h1>Post A Video</h1>
<div class="content">
			<?php if (!$iframe): ?>
            <p>Logged in as <?php echo $userEmail ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url() . 'admin.php?page=ezwebplayer.php&action=logout'?>">Log Out</a></p>
            <?php endif; ?>
            <p>
        	<?php if ($iframe): ?>
        		<a target="_blank" href="<?php echo admin_url() . 'admin.php?page=/ezwebplayer.php&action=recent'; ?>" >Recently posted videos</a> | Post a video
        	<?php else: ?>
        		<a href="<?php echo admin_url() . 'admin.php?page=/ezwebplayer.php&action=recent'; ?>" >Recently posted videos</a> | Post a video
        	<?php endif; ?>
                
            <span id="search_ajax_preloader" style="display: none; height:30px; margin-left: 15px; padding-left: 20px; background: url(<?php echo $wp_wpezp_plugin_url .'/img/ajax-loader.gif';?>) 0 no-repeat"> Searching...</span>    
                
            </p>
            <div class="ezwp-whitebackground">
                <form method="post" action="<?php echo $wp_wpezp_plugin_url .'/importandupdateposts.php';?>">                    
                    <input name="ezwpToOnePost" type="hidden" value="<?php echo (isset($ezwpToOnePost))?'false':'true' ?>" />
                    <div class="ezwp-rightcolumn">
                        <div class="ezwp-keys">
                            <h3 style="margin: 10px">Key:</h3>
                            <ul id="ezwp-keys" class="ezwp-keys-left">
                                <li  class="ezwp-posted-videos "><label>A Posted Video</label></li>
                                <li  class="ezwp-not-posted-videos"><label>Not A Posted Video</label></li>
                            </ul>
                        </div>
                        <div class="ezwp-extraoptions">
                            <h3 style="margin: 10px">Extra Options:</h3>
                            <ul id="ezwp-extra">
                                <li><input type="checkbox" readonly name="ezwpextra[]" /><label>Show Video Name</label></li>
                                <li><input type="checkbox" readonly name="ezwpextra[]" /><label>Show Video Description</label></li>
                                <li><input type="checkbox" readonly name="ezwpextra[]" /><label>Show Video View Count</label></li>
                            </ul>
                            <ul class="ezwp-sadssa ezwp-rceo ezwp-keys-left ezwp-k-l-ul-s-d">
                                <li><input type="radio" value="0" readonly name="ezwp-rceo[]" /><label>De-Select All</label></li>
                                <li><input type="radio" value="1" readonly name="ezwp-rceo[]" /><label>Select All</label></li>
                            </ul>
                            <input type="hidden" name="ezwp-post-type" value="by-videos" id="ezwp-post-type" />
                            <div class="ezwp-submitbtn"><input align="right" type="submit" value="Post" class="ezwp-submit" /></div>

                        </div>
                    </div>
                    <div class="ezwp-tabs ezwp-leftcolumn">
                        <ul class="tabNavigation">
                            <li><a href="#seachByVideo"><b>Search by Video</b></a></li>
                            <li><a href="#seachByCategory"><b>Search by Channel</b></a></li>
                        </ul>
                        <div id="seachByVideo" class="ezwp-ie-tabs">
                            <div style="margin:10px 20px 10px 10px;">
                                <div class="w_50p">
                                    <select multiple="multiple" name="ezwp-sbc-list[]" class="ezwp-sbv-list-block ezwp-select-css">
                                        <?php
                                        $videos = $soapVideo->get_all_video($userID);
                                        ?>
                                        <?php if(is_array($videos) && count($videos) > 0): ?>
                                            <?php 
                                            /**
    										* TODO We should find, how we can know what is response: ONE item or ARRAY items 
    										* */
                                            if (key_exists("ID", $videos)){
                                                $tmp = $videos;
                                                unset($videos);
                                                $videos[0] = $tmp;
                                            }
                                            ?>
                                        
                                            <?php foreach($videos as $video):?>
                                            	<option <?php 
                                            	    if (array_key_exists($video['ID'], $postedVideos)) {
                                                        echo "class=\"ezwp-posted-videos\"";
                                                        unset($postedVideos[$video['ID']]);
                                                    } ?> 
                                                    
                                                    value="<?php echo $video['ID']."|".$video['Title'] ?>"><?php echo $video['Title'] ?></option>
                                            <?php endforeach; ?>					
										<?php else: ?>
											<option></option>
										<?php endif; ?>
                                    </select>
                                </div>
                                <ul class="ezwp-sadssa ezwp-rcvl">
                                    <li><input type="radio" value="0" readonly name="ezwp-rcvl[]" /><label>De-Select All</label></li>
                                    <li><input type="radio" value="1" readonly name="ezwp-rcvl[]" /><label>Select All</label></li>                                    
                                </ul>
                            </div>
                        </div>
                        <div id="seachByCategory" class="ezwp-ie-tabs">
                            <div class="ezwp-seachByCategory">
                                <div class="ezwp-videos-for-categories">
                                    <select multiple="multiple" name="ezwp-videos-by-cats[]" class="ezwp-videos-by-categories ezwp-select-css"></select>
                                    <ul class="ezwp-sadssa ezwp-vfcc">
                                        <li><input type="radio" value="0" readonly name="ezwp-vfcc[]" /><label>De-Select All</label></li>
                                        <li><input type="radio" value="1" readonly name="ezwp-vfcc[]" /><label>Select All</label></li>
                                    </ul>
                                </div>
                                <div class="ezwp-categories ezwp-ie-cats">
                                    <select name="ezwp-categories[]" multiple="multiple" class="ezwp-by-videos ezwp-select-css">
                                        <?php
                                        /**
                                         * If post was posted by video(s), we should select needed categories.
                                         * This info we get from GET array param cat_list. Category write in
                                         * string, divided by common(","). Otherwise - we have empty selected
                                         * array
                                         *  
                                         */
                                        
                                        $selected_categories_array = array();
                                        if((int)$_REQUEST["by_categories"] == 1){
                                            $cat_list = (string)$_REQUEST['cat_list'];
                                            $selected_categories_array = explode(",", $cat_list);
                                        }
                                        //None Categorized
                                        $categories = $soapVideo->get_all_categories($userID);
                                        
                                        if(!is_array($categories) || (is_array($categories) && count($categories) == 0)){
                                            $categories = array();
                                        }   
                                        
                                        $categories[] = array("ID" => 0, "Name" => "Non-Categorized");
                                        ?>
                                        <?php 
                                        /**
										* TODO We should find, how we can know what is response: ONE item or ARRAY items 
										* */
                                        if (key_exists("ID", $categories)){
                                            $tmp = $categories;
                                            unset($categories);
                                            $categories[0] = $tmp;
                                        }
                                        ?>
                                        
                                        <?php foreach($categories as $key => $cat): ?>
                                        <option <?php if(isset($addedCategories[$cat['ID']])) {
                                                echo "class=\"ezwp-posted-videos\"";
                                                unset($addedCategories[$cat['ID']]);
                                            } ?> value="<?php echo $cat['ID'] ?>" <?php if(in_array($cat['ID'], $selected_categories_array)): ?> selected="selected" <?php endif; ?>><?php echo $cat['Name'] ?></option>
                                        <?php endforeach;?>								
                                    </select>
                                    <ul class="ezwp-sadssa ezwp-rccs">
                                        <li><input type="radio" value="0" readonly name="ezwp-rccs[]" /><label>De-Select All</label></li>
                                        <li><input type="radio" value="1" readonly name="ezwp-rccs[]" /><label>Select All</label></li>
                                    </ul>
                                </div>
                        </div>
                        </div>
                    </div>
                </form>
            </div>

</div>

<script type="text/javascript" charset="utf-8">
	var wp_home = '<?php echo $wp_wpezp_plugin_url; ?>';
	var by_categories = '<?php echo (int)$_REQUEST["by_categories"]; ?>' == 1;
</script>    
<script src="<?php echo $wp_wpezp_plugin_url . '/js/ezw_lib.js';?>" type="text/javascript"></script>

<?php
//print_r($postedVideos);
//return;

foreach($postedVideos as $pV) {
    wp_delete_post((int)$pV);
}
foreach($addedCategories as $aC) {
    wp_delete_category($aC);
}
//}

?>