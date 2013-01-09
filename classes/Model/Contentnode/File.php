<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contentnode_File extends Model_Contentnode_Driver
{
    const type_text = 0;
    const type_serialized = 1;
    const type_json = 2;
    const type_xml = 3;
    const type_yaml = 4;
    const type_ini = 5;

    const ext_text = 'txt';
    const ext_serialized = 'srz';
    const ext_json = 'json';

    static $path_dirname = "content/";
    static $path_dir = "";
    protected $_path = "";
    protected $_basename = "";
    protected $_ext = true;
    protected $_path_ready = false;
    protected $_type = "";
    protected $_options = array();
    protected $_data = null;

    public function __construct($ref=null)
    {
        parent::__construct();
        $this->set_ref($ref);
        $this->load();
    }


    public function __set($name, $value)
    {
        if(!is_array($this->_data)) $this->_data = array();
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if(is_array($this->_data) && array_key_exists($name, $this->_data))
        {
            return $this->_data[$name];
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name)
    {
        return (is_array($this->_data) && isset($this->_data[$name]));
    }


    public function load($ref = null)
    {
        if(!is_null($ref)) $this->set_ref($ref);
        if(!(file_exists($this->_path) && is_file($this->_path))) return false;
        //TODO: try-catch ?
        switch($this->_type)
        {
            case self::type_text :
                @$this->_data = file_get_contents($this->_path);
                return true;
                break;

            case self::type_serialized :
                @$this->_data = unserialize(file_get_contents($this->_path));
                return true;
                break;

            case self::type_json :
                @$this->_data = json_decode(file_get_contents($this->_path), true);
                return true;
                break;
        }
    }


    public function save($ref = null)
    {
        if(!is_null($ref)) $this->set_ref($ref);
        if($this->_path_ready)
        {
            switch($this->_type)
            {
                case self::type_text :
                    @file_put_contents($this->_path, $this->_data, LOCK_EX);
                    break;

                case self::type_serialized :
                    @file_put_contents($this->_path, serialize($this->_data), LOCK_EX);
                    break;

                case self::type_json :
                    @file_put_contents($this->_path, json_encode($this->_data), LOCK_EX);
                    break;
            }
        }
    }


    public function delete()
    {
        if(file_exists($this->_path) && is_file($this->_path)) @unlink($this->_path);
    }


    public function get_data()
    {
        return $this->_data;
    }


    public function set_data($data)
    {
        $this->_data = $data;
    }


    /*
     * array params:
     * - type - mixed constant text, serialized, json
     * - file - string, relative to Model_Contentnode_File::$path_dir
     *  or
     * - ext - true, false or string (eg 'html')
     * - basename - string (eg 'en/main.htm')
     * */
    public function set_ref($ref)
    {
        $type = $basename = null;
        $ext = true;
        if(is_array($ref))
        {
            if(isset($ref['type'])) $type = $ref['type'];
            if(isset($ref['file']) && is_string($ref['file']) && strlen($ref['file']))
            {
                $path = self::$path_dir.$ref['file'];
                echo $path;
                $pi = (file_exists($path) && is_file($path)) ? pathinfo($path) : null;
                if(isset($pi['extension']) && strlen($pi['extension']))
                {
                    $ext = $pi['extension'];
                    $basename = substr($ref['file'], 0, -(strlen($ext)+1));
                }
                else
                {
                    if(isset($ref['ext']))
                    {
                        $ext = (false === $ref['ext']) ? false : $ref['ext'];
                    }
                    else
                    {
                        $ext = true;
                    }
                    $basename = $ref['file'];
                }
            }
            else
            {
                $basename = (isset($ref['basename'])) ? $ref['basename'] : null;
                if(isset($ref['ext'])) $ext = $ref['ext'];
                elseif(!is_null($type))
                {
                    $ext = true;
                }
                else
                {
                    $ext = false;
                }
            }
        }
        elseif(is_scalar($ref))
        {
            $basename = $ref;
        }
        if(isset($ref['path']) && is_string($ref['path']) && strlen($ref['path']))
        {
            $this->set_path($ref['path']);
        }
        else
        {
            $this->set_file($basename, $ext);
        }
        $this->set_type($type);
    }


    public function set_type($type)
    {
        if(is_string($type))
        {
            $type=strtolower($type);
            if($type === 'text')
            {
                $this->_type = self::type_text;
                $this->_path_ready = false;
            }
            elseif($type === 'serialize' || $type === 'serialized')
            {
                $this->_type = self::type_serialized;
                $this->_path_ready = false;
            }
            elseif($type === 'json')
            {
                $this->_type = self::type_json;
                $this->_path_ready = false;
            }
        }
        elseif($type === self::type_text || $type === self::type_serialized || $type === self::type_json)
        {
            $this->_type = $type;
            $this->_path_ready = false;
        }
        if(!$this->_path_ready)
        {
            $this->set_file($this->_basename, $this->_ext);
        }
    }


    public function set_file($basename, $ext = true)
    {
        if(is_string($basename) && strlen($basename))
        {
            $this->_basename = $basename;
            $this->_path = self::$path_dir.$basename;
            if($ext === true)
            {
                $this->_ext = true;
                switch($this->_type)
                {
                    case self::type_text :
                        $this->_path .= "." . self::ext_text;
                        break;
                    case self::type_serialized :
                        $this->_path .= "." . self::ext_serialized;
                        break;
                    case self::type_json :
                        $this->_path .= "." . self::ext_json;
                        break;
                }
            }
            elseif(is_string($ext) && strlen($ext))
            {
                $this->_ext = $ext;
                $this->_path .= "." . $ext;
            }
            else
            {
                $this->_ext = true;
            }
            $this->_path_ready = true;
        }
        elseif(strlen($this->_path) && file_exists($this->_path) && is_file($this->_path))
        {
            $this->_path_ready = true;
        }
    }


    /*
     * this method provides setting an external file path (outside of Model_Contentnode_File::$path_dir)
    */
    public function set_path($path)
    {
        $this->_path = $path;
        //TODO: check if path inside self::$path_dir and set appropriate ext/basename
        $this->_ext = false;
        $this->_basename = "";
        $this->_path_ready = true;
    }


    public static function configure()
    {
        //TODO: implement static method for configuration loading
    }
}

//TODO: make static method to do this to make possibility to rule it from config
Model_Contentnode_File::$path_dir = APPPATH.Model_Contentnode_File::$path_dirname;
