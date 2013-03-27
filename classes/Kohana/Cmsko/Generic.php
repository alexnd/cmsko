<?php defined('SYSPATH') or die('No direct script access.');
/*
 * generic implementation of Cmsko driver. not contain any physical storage i/o operations
 * */

class Kohana_Cmsko_Generic extends Cmsko {

    // url:nested:lang:host:user => id => _map[id] , или хранить одним списком добавив уникальный id
    protected $_map_links = array();


    public function execute()
    {
        //die('executing...');
        // take request params
        // locate node
        // add routing rule
    }


    public function add_node($node)
    {
        $this->_map[] = $node;
    }

    public function add_nodes($nodes)
    {
        if(count($nodes)) foreach($nodes as $n) $this->_map[] = $n;
    }

    public function count_map(){}

    public function unserialize_map(){}

    public function serialize_map(){}

    public function locate($url){}

    public function load_map(){}

    public function save_map(){}


    // this all stuff should be moved to file driver
    public $content_dir = 'content/cnodes/';

    public function load($id)
    {
        $path = APPPATH.$this->content_dir.$id.'.txt';
        return (file_exists($path) && is_file($path)) ? file_get_contents($path) : null;
    }

    public function save($id, $data)
    {
        if($id) {
            $path = APPPATH.$this->content_dir.$id.'.txt';
            file_put_contents($path, $data);
            return true;
        }
        return false;
    }

}
