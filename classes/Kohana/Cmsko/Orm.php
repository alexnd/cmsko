<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * wraps Model_Contentnode
 */
class Kohana_Cmsko_Orm extends Cmsko {

    public function execute()
    {
        //die('executing using orm...');
    }


    //array('type' => Model_Contentnode_File::type_json, 'file' => 'cnodes/bob')
    public function add_node($node_file)
    {

    }


    public function add_nodes($nodes_file)
    {

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
