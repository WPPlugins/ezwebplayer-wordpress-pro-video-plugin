<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-type: text/html; charset=utf-8');

if(!defined('WP_INSTALLING'))
    define('WP_INSTALLING',TRUE);

require_once( '../../../wp-config.php' );
require_once( '../../../wp-settings.php' );
require_once dirname(__FILE__) . '/nusoap/nusoap.php';
require_once dirname(__FILE__) . '/config.php';

global $wpdb;
$sql = "SELECT vKey, postID FROM {$wpdb->prefix}ezvideos";
$postedVideos = $wpdb->get_results($sql,ARRAY_A);
$newPV = array();
if(isset($postedVideos)){
    foreach($postedVideos as $pV){
        $pst = get_post($pV['postID']);        
        if(!empty($pst))
            $newPV[$pV['vKey']] = $pV;
    }
}
$postedVideos = $newPV;

try {
    $client = new nusoap_client(EZWP_WSDL_URL,true);
}
catch(Exception $e) {
    echo "couldn't connect to server";
    return;
}
$cats = ($_POST['cats']) ? $_POST['cats'] : array('0');

$userID = get_option('userID');
$tmp = array();

/*
This array we use, that show one video one time!
*/
$videos_shown = array();

foreach($cats as $cat) {
//    $videos = $client->call("GetVideos", array("userGuid" => "$userID","categoryID" => "$cat"));
//    $videos = $videos['GetVideosResult'];
//    $videos = explode("|",$videos);
    $videos = $client->call("GetVideos", array("userGuid" => "$userID","categoryID" => "$cat"));
    $videos = $videos['GetVideosResult']['Video'];
    
    /**
    * TODO We should find, how we can know what is response: ONE item or ARRAY items 
    * */
    ?>
    <?php if(is_array($videos) && count($videos) > 0): ?>
    
    <?php
    if (key_exists("ID", $videos)){
        $tmp = $videos;
        unset($videos);
        $videos[0] = $tmp;
    } 
?>
	    <?php foreach ($videos as $video) :?>
	    	<?php if(!isset($tmp[$video['ID']]) && empty($tmp[$video['ID']]) && !in_array($video["ID"], $videos_shown)):?>
            	<?php $videos_shown[] = $video['ID'];?>
                <option <?php if(isset($postedVideos[$video['ID']])){echo "class=\"ezwp-posted-videos\""; unset($postedVideos[$video['ID']]);} ?> value="<?php echo $video['ID']."|".$video['Title'] ?>"><?php echo $video['Title'] ?></option>
                <?php $tmp[$video['ID']] = 1;?>
            <?php endif; ?>
        <?php endforeach; ?>
		<?php else: ?>
		<option></option>
	<?php endif; ?>
<?php } ?>