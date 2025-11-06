<?php

class paramsbuilder
{


    protected $_comps=[];

    public function add($model,$field,$type) {

        $key = strtolower(implode('_',[$model,$field,$type]));

        $orm = ORM::factory($model);

        if(isset($this->_comps[$key])) {
            throw new Exception('not unique vidget');
        }

        $class='paramsbuilder_'.$type;
        $this->_comps[$key]=new $class($orm,$field);
    }

    public function save($data) {
        $json = [];
        foreach($this->_comps as $comp) {
            $json[]=$comp->save($data);
        }
        return $json;
    }

    public function render($data) {
        $content = '';
        foreach($this->_comps as $comp) {
            $content.=$comp->render($data);
        }

        return $content;
    }

}
