<?php
/**
 * @author  EZWebPlayer 
 * @package EZWebPlayer Pro
 * @Version 3.2
 */
class postVideo
{
    protected $db; //link WP DB object
    protected static $registered = false;
    protected static $instance = null;
    protected $pattern_array = array(
        "Title"       => array(
            "tag"   => "[VideoTitle VideoID=###video_id###/]",
            "title" => "Title"
        ),
        "Description" => array(
            "tag"   => "[VideoDescription VideoID=###video_id###/]",
            "title" => "Description"
        ),  
        
        "Views"       => array(
            "tag"   => "[VideoViewCount VideoID=###video_id###/]",
            "title" => "View count"
        )
    );

    const MAIN_VIDEO_TAG = "[EZWebPlayer VideoID=###video_id###/]";
    const REGEXP_FORMAT  = "\[(\w+)\sVideoID=([^\/]*)\/\]";
    
    protected function __construct ()
    {
        global $wpdb;
        $this->db = $wpdb;
    }
    
    /**
     * Retrieves the singleton instance of this class.
     *
     * @return postVideo A postVideo implementation instance.
     */
    static public function getInstance ()
    {
        if (! isset(self::$instance)) {
            self::$instance = new postVideo();
        }
        return self::$instance;
    }
    
	/**
     * Parsing for save
     * 
     * Before save, we should parse post and find in it all videos
     *
     * @param integer $post_id - integer. If $post_id = null, then 
     *                           we work with new post, else - already exists
     */
    public function postParse ($postID)
    {
        //before exists video in DB
        $before_exist_videos = $this->db->get_results("SELECT * FROM " . $this->db->prefix . "ezvideos" . " WHERE postID=$postID");
        
        $before_exist_videos_list = array();
        if (count($before_exist_videos) > 0){
            foreach ($before_exist_videos as $item){
                $before_exist_videos_list[] = $item->vKey;
            }
        }
        
        //get videos from current content
        $content = $this->db->get_var($this->db->prepare("SELECT post_content FROM " . $this->db->posts . " WHERE ID=%d", $postID));
        $matches = array();
        preg_match_all("/" . self::REGEXP_FORMAT . "/i", $content, $matches);
        
        $video_list = array_unique($matches[2]);
        
        //should be add
        $new_videos = array_diff($video_list, $before_exist_videos_list);
        //should be delete
        //NOTE: this video correct deleted(main tag and all related tags)
        $old_videos = array_diff($before_exist_videos_list, $video_list);
        
        $content_is_modify = false;
        
        if (count($new_videos) > 0){
            $tmp = array();
            $tmp["postID"] = $postID;
            foreach ($new_videos as $item){
                //Check correct adding new video
                $tmp_main_tag = str_replace("###video_id###", $item, self::MAIN_VIDEO_TAG);
                if(array_search($tmp_main_tag, $matches[0]) !== false){
                    $tmp["vkey"] = $item;
                    $this->db->insert( $this->db->prefix . "ezvideos", $tmp);
                }else{
                    //Delete related tags(if it exists, of cource), because video was add incorrect 
                    /*$pattern_array = $this->_get_pattern_array($item);
                    foreach ($pattern_array as $key_in => $item_in){
                        $content = str_replace($this->pattern_array[$key_in]["title"] . ": " . $item_in, "", $content);
                        $content_is_modify = true;
                    }*/
                }
            }
        }
        
        /**if (count($old_videos)){
            foreach ($old_videos as $item){
                $this->db->query( "DELETE FROM " . $this->db->prefix . "ezvideos WHERE vkey = '$item'" );
            }            
        }

        
         * Posts, for which exist additional tags without main -
         * this tags should be delete. Video record from DB
         * must be delete too
                 
        foreach ($video_list as $item){
            $tmp_main_tag = str_replace("###video_id###", $item, self::MAIN_VIDEO_TAG); 
            
            if(array_search($tmp_main_tag, $matches[0]) === false && array_search($tmp_main_tag, $new_videos) === false){
                 //Delete related tags(if it exists, of cource), because video was add incorrect 
                 $pattern_array = $this->_get_pattern_array($item);
                 foreach ($pattern_array as $key_in => $item_in){
                     $content = str_replace($this->pattern_array[$key_in]["title"] . ": " . $item_in, "", $content);
                     $content_is_modify = true;
                 }
                 $this->db->query("DELETE FROM " . $this->db->prefix . "ezvideos WHERE vkey = '$item'");
             }      
        }*/

        if ($content_is_modify == true){
            $this->db->update($this->db->posts, array('post_content' => $content), array('ID' => $postID));
        }
        
    }
    
    public function postList(){
        $name = '';
        $dateFrom = '';
        $dateTo = '';
        
        $where_str = '1';
        
        if( ((isset ($_GET['name']) && !empty($_GET['name'])) || 
            (isset($_GET['date_from']) && !empty($_GET['date_from']))|| 
            (isset($_GET['date_to']) && !empty($_GET['date_to']))) 
            && 
            array_key_exists('search', $_GET)) {
            
            $tmp = array(
            				'name'      => $_GET['name'],
                            'date_from' => $_GET['date_from'],
                            'date_to'   => $_GET['date_to'],
                        );    
                
            $tmp = $this->db->escape($tmp);
            
            $where = array();
            
            if (!empty($tmp['name'])){
                $name = $tmp['name'];
                $where[] = "{$this->db->prefix}posts.post_title like '%" . $name . "%'";
            }
            
            if (!empty($tmp['date_from'])){
                $formatDateFrom = date('Y-m-d', strtotime($tmp['date_from']));
                $dateFrom       = $tmp['date_from'];
                $where[] = "{$this->db->prefix}posts.post_date >= '" . $formatDateFrom . "'";
            }
            
            if (!empty($tmp['date_to'])){
                $formatDateTo = date('Y-m-d', strtotime($tmp['date_to']));
                $dateTo       = $tmp['date_to'];
                $where[] = "{$this->db->prefix}posts.post_date <= '" . $formatDateTo . "'";
            }            
            
            $where_str = implode(" AND ", $where);
            
            $query = "SELECT * 
                      FROM {$this->db->prefix}ezvideos 
                      LEFT JOIN {$this->db->prefix}posts ON {$this->db->prefix}ezvideos.postID={$this->db->prefix}posts.id  
                      WHERE " . $where_str . "
                      GROUP BY {$this->db->posts}.ID";
        } else {
            $query = "SELECT * 
                      FROM {$this->db->prefix}ezvideos 
                      LEFT JOIN {$this->db->prefix}posts ON {$this->db->prefix}ezvideos.postID={$this->db->prefix}posts.id
                      GROUP BY {$this->db->posts}.ID";
        }
        
        
        /**
		Get related categories
		*/
        $query_cat = "SELECT post_ids.id as post_id, term_id, terms.name AS name 
                      FROM
                          (SELECT {$this->db->prefix}posts.ID as id 
                          FROM {$this->db->prefix}ezvideos 
                          LEFT JOIN {$this->db->prefix}posts ON {$this->db->prefix}ezvideos.postID={$this->db->prefix}posts.id
                          WHERE " . $where_str . "
                          GROUP BY {$this->db->posts}.ID) as post_ids
                      LEFT JOIN {$this->db->term_relationships} AS term_relationships ON post_ids.id=term_relationships.object_id 
                      INNER JOIN {$this->db->prefix}ezcategories AS categories ON categories.locID=term_relationships.term_taxonomy_id  
                      INNER JOIN {$this->db->terms} AS terms ON term_relationships.term_taxonomy_id=terms.term_id  
                      ORDER BY post_id
                      ";

        $res = $this->db->get_results($query_cat);  
        
        $catList = array();
        if (is_array($res) && count($res) > 0){
            foreach($res as $key => $item){
                $catList[$item->post_id][] = $item->name;
            }
        }
        
        $postList = $this->db->get_results($query);
        return array(
                        "list" => $postList,
                        "filter" => array(
                                            "name"      => $name,        
                        					"date_from" => $dateFrom,
                                            "date_to"   => $dateTo
                                         ),
                        "catlist" => $catList                 
                    );
    } 
    
    public function postDelete ($postID){

    }

	/**
     * Parsing before show post in frontend
     *
     */
    public function preOutput ($content, $userID)
    {
        $context = context::getInstance();
        $soapVideo = $context->getSoapVideo();
        
        $matches = array();
        preg_match_all("/" . self::REGEXP_FORMAT . "/i", $content, $matches);
        
        $video_list = array_unique($matches[2]);
        
        if (count($video_list) > 0){
            foreach ($video_list as $key => $videoID){
                $video_info   = $soapVideo->get_video($videoID, $userID);
				$video_player = $soapVideo->get_player($videoID, $userID);
                $content = $this->_get_video_player($videoID, $content, $video_player, $video_info);
                $tmp = $this->_get_pattern_array($videoID);
                foreach ($tmp as $key => $item){
                    $content = str_replace($item, $video_info[$key], $content);
                }
            }
        }

        return $content;
    }
        
	/**
     * 	
     	[0] => [EZWebPlayer VideoID=57930ca0-88de-4668-88bb-1a1d9d10cd91/]
        [1] => [VideoTitle VideoID=57930ca0-88de-4668-88bb-1a1d9d10cd91/]
        [2] => [VideoDescription VideoID=57930ca0-88de-4668-88bb-1a1d9d10cd91/]
        [3] => [VideoViewCount VideoID=57930ca0-88de-4668-88bb-1a1d9d10cd91/]
     */
    public function _get_pattern_array($video_id){
        $output = array();
        foreach ($this->pattern_array as $key => $item){
            $output[$key] = str_replace("###video_id###", $video_id, $item["tag"]);
        }
        
        return $output;
    }
    
    public function _get_video_player($video_id, $text, $video_player, $video_info){
        $rand = rand(1, 100000);	
        $userID = get_option('userID');
        //http://dynodb.deviz.sibers.com/Scripts/flashplayerhost.min.js
        //http://cloud.ezwebplayer.com/Scripts/flashplayerhost.min.js
        $script = $video_info["IFrameCode"];  
    	$tmp = str_replace("###video_id###", $video_id, self::MAIN_VIDEO_TAG);
        return str_replace($tmp, $script, $text);
    }

}
?>