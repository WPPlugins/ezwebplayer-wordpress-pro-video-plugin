<?php
/**
 * Enter description here...
 *
 * @author  EZWebPlayer 
 * @package EZWebPlayer Pro
 * @Version 3.2
 */
class context
{
    protected $factories = array();
    protected static $instance = null;
    
    /**
     * Enter description here...
     *
     */
    protected function __construct ()
    {
        $this->factories["postVideo"] = postVideo::getInstance();
        $this->factories["soapVideo"] = soapVideo::getInstance();
    }
    
    /**
     * Retrieves the singleton instance of this class.
     *
     * @return context A context implementation instance.
     */
    static public function getInstance ()
    {
        if (! isset(self::$instance)) {
            self::$instance = new context();
        }
        return self::$instance;
    }    

    public function getPostVideo(){
        return $this->factories["postVideo"];
    }
    
    public function getSoapVideo(){
        return $this->factories["soapVideo"];
    }    
}
?>