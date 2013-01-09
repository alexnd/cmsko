<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * wraps Model_Contentnode_File
 */
class Kohana_Cmsko_File extends Cmsko {

    public function execute()
    {
        //die('executing...');
    }


    //array('type' => Model_Contentnode_File::type_json, 'file' => 'cnodes/bob')
    public function add_node($node_file)
    {
        $this->_map[] = Model::factory('contentnode_file', $node_file);
    }


    public function add_nodes($nodes_file)
    {
        $nodes = Model::factory('contentnode_file', $nodes_file)->get_data();
        if(count($nodes)) foreach($nodes as $n) $this->_map[] = $n;
    }


    public function count_map(){}

    public function unserialize_map(){}

    public function serialize_map(){}

    public function locate($url){
        //TODO: search by existing path
    }

    public function load_map(){}

    public function save_map(){}

}
