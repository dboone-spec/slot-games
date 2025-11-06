<?php
class Office {
    protected static $_instance=[];
    protected $_settings = NULL;
    protected $_o;

    /**
     *
     * @param type $id
     * @return Office
     */
    public static function instance($id=null) {
        if(!$id && defined('OFFICE')) {
            $id = OFFICE;
        }

        if (!isset(self::$_instance[$id])) {
            self::$_instance[$id] = new Office($id);
        }
        return self::$_instance[$id];
    }

    /**
     * @return  Model_Office
     */
    public function office() {
        return $this->_o;
    }

    public function __construct($id) {

        $this->_o = new Model_Office($id);

        $settings = [];

        if(PROJECT!=1) {
            foreach ($this->_o->settings->find_all() as $v) {
                $settings[$v->param] = $v->enabled;
            }
        }

        $this->_settings = $settings;
    }

    public function settings($name=null) {
        if($name) {
            return arr::get($this->_settings, $name, 1);
        }

        return $this->_settings;
    }

    public static function stFile($id,$st=0) {
        $path = DOCROOT . "/o";
        if( !is_dir($path)) {
            mkdir($path, 02777);
            chmod($path, 02777);
        }
        

        $file = $path .DIRECTORY_SEPARATOR. $id . ".json";
        file_put_contents($file, $st);
    }
    
    public static function block($id) {
        $office = self::instance($id)->office();
        $office->blocked = time();
        $office->save();
        
        self::stFile($id, 1);
    }

    public static function unblock($id) {
        $office = self::instance($id)->office();
        $office->blocked = 0;
        $office->save();
        
        self::stFile($id, 0);
    }

    public static function isBlocked($id=null) {
        $office = self::instance($id)->office();
		
        if($office->loaded() && $office->blocked>0) {
            return true;
        }

        return false;
    }
}


