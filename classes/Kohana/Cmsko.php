<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Kohana_Cmsko {

    // Cmsko instances
    protected static $_singleton;
    protected static $_instances = array();

    /**
     * Cmsko router singleton (for module init)
     */
    public static function instance($config = null)
    {
        // support for 1-array param usage
        if( is_array($config) && array_key_exists('id', $config) && is_scalar($config['id']) )
        {
            return Cmsko::factory($config['id'], $config);
        }
        else
        {
            if ( !isset(Cmsko::$_singleton) )
            {
                if( !(is_string($config) && strlen($config)) )
                {
                    $config = 'cmsko';
                }
                $config = Kohana::$config->load($config);
                if(!is_array($config)) {
                    $config = $config->get('router');
                }
                $type = isset($config['driver']) ? $config['driver'] : 'generic';
                //TODO: try-catch ?
                $class = 'Cmsko_'.ucfirst($type);
                Cmsko::$_singleton = new $class($config);
            }
            return Cmsko::$_singleton;
        }
    }

    /**
     * Cmsko instances factory method
     *
     * @return Cmsko
     */
    public static function factory($id = null, $config = null)
    {
        if ( is_scalar($id) )
        {
            if ( !isset(Cmsko::$_instances[$id]) )
            {
                //TODO: refactor config- stuff
                if ( is_scalar($config) )
                {
                    $config = Kohana::$config->load($config);
                    if(!is_array($config)) {
                        $config = $config->get('cms_nodes');
                    }
                }
                if ( is_array($config) )
                {
                    $type = isset($config['driver']) ? $config['driver'] : 'generic';
                }
                else
                {
                    //TODO: what is that? why we here...
                    $type = 'generic';
                }
                //TODO: try-catch ?
                $class = 'Cmsko_'.ucfirst($type);
                Cmsko::$_instances[$id] = new $class($id, $config);
            }
            return Cmsko::$_instances[$id];
        }
    }

    protected $_config;
    protected $_id;


    public function __construct($id, $config = array())
    {
        $this->_id = $id;
        $this->_config = $config;
    }

    public function get_id()
    {
        return $this->_id;
    }

    protected static $_map = array();

    abstract function load($id);
    abstract function save($id, $data);

    abstract function add_node($node);

    abstract function add_nodes($nodes);

    /*map node by url*/
    abstract function locate($url);

    /*obtain a map length*/
    abstract function count_map();

    /*return all map in one struct (array or object)*/
    abstract function load_map();

    /*save map struct to store*/
    abstract function save_map();

    /*load map from cache (php file)*/
    abstract function unserialize_map();

    /*save map to cache*/
    abstract function serialize_map();

    /*caching. delete this*/
    public function _serialize(){
        $path = APPPATH."cache/cms-".$this->_id.".srz";
        echo $path;
        //file_put_contents($path, serialize(self::$_map));
    }
    public function _unserialize(){

    }

    /*process request routing*/
    abstract function execute();


} // End Cmsko
