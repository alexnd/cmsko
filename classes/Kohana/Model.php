<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Model
{
    public static function factory($name)
    {
        $class = 'Model_'.ucfirst($name);
        if(func_num_args()>1)
        {
            return new $class(func_get_arg(1));
        }
        else
        {
            return new $class;
        }
    }
}
