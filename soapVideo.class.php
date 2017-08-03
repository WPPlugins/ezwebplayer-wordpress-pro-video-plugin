<?php
/**
 * Enter description here...
 *
 * @author  EZWebPlayer 
 * @package EZWebPlayer Pro
 * @Version 3.2
 */
class soapVideo
{
    protected $client = false; //nusoap_client
    protected static $instance = null;
    
    /**
     * Enter description here...
     *
     */
    protected function __construct ()
    {
        try {
            $this->client = new nusoap_client(EZWP_WSDL_URL, true);
        } catch (Exception $e) {
            echo "couldn't connect to server";
        }
    }

    /**
     * Retrieves the singleton instance of this class.
     *
     * @return soapVideo A soapVideo implementation instance.
     */
    static public function getInstance ()
    {
        if (! isset(self::$instance)) {
            self::$instance = new soapVideo();
        }
        return self::$instance;
    }    

    /**
     * Enter description here...
     *
     * @param string $videoID
     * @param string $userID
     * 
     * Response format
            [GetVideoResult] => Array
                (
                    [ID] => 57930ca0-88de-4668-88bb-1a1d9d10cd91
                    [Title] => test
                    [Description] => 
                    [DateCreated] => 2010-03-23T03:19:43.883
                    [Views] => 1
                    [Rating] => 0
                    [DefaultCategory] => 169
                    [Width] => 700
                    [Height] => 417
                )
     * 
     * @return array
     */
    public function get_video($videoID, $userID){
        $result = $this->client->call("GetVideo", array("videoID" => $videoID, 'userID' => $userID));
        return $result["GetVideoResult"];
		echo $result;
    }
    
    public function get_player($videoID, $userID){
        $result = $this->client->call("GetEmbedPlayer", array("videoID" => $videoID, 'userGuid' => $userID));
        return $result["GetEmbedPlayerResult"];        
    }
    
    public function get_all_video($userID){
        $result = $this->client->call("GetAllVideos", array("userGuid" => "$userID"));
print_r($this->client->request);
print_r($this->client->response);
        return $result["GetAllVideosResult"]["Video"];          
    }

    public function get_all_categories($userID){
        $result = $this->client->call("GetCategories", array("userGuid" => "$userID"));
        return $result["GetCategoriesResult"]["Category"];          
    }  

    //////////////////////////////////////////////////////////////
    /**
     * Получение списка категорий для видео, то есть получается, 
     * что одно видео может быть в нескольких категориях
     */    
    //////////////////////////////////////////////////////////////
    public function get_categories_for_video($videoID, $userID){
        $result = $this->client->call("GetCategoriesForVideo", array("videoID" => $videoID, "userGuid" => $userID));
        $result = $result["GetCategoriesForVideoResult"];

        if (is_array($result) && key_exists("Category", $result)){
            if (key_exists("ID", $result["Category"])){
                $tmp = $result["Category"];
                unset($result);
                $result[0] = $tmp;
            }else{
                $result = $result["Category"];
            }
        }else{
            $result = array();
        }
        
        return $result;
    }
    
}
?>