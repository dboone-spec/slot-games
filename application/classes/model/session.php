<?php

class Model_Session extends ORM {

    protected $_serialize_columns = array('data');

    public function start() {
        if (!$this->loaded()) {
            return false;
        }

        $this->start = time();
        $this->save();
        return true;
    }

    public function flash($data = null) {


        if (!$this->loaded()) {
            return false;
        }

        if (is_array($data)) {
            $r = th::ObjectToArray($this->data);

            if (is_array($r)) {
                foreach ($r as $key => $value) {
                    if (!isset($data[$key])) {
                        $data[$key] = $value;
                    }
                }
            }
        }

        $this->last = time();
        $this->data = $data;
        $this->save();

        return true;
    }

    public function end() {

        $this->data = null;
        $this->save();
    }

}
