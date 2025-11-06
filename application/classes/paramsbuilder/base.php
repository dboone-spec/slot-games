<?php

//require jquery

class paramsbuilder_base
{

    protected $_model;
    protected $_field;

    public function __construct($model,$field)
    {
        $this->_model=$model;
        $this->_field=$field;
    }


    protected $_html=[];
    protected $_js=[];

    public function render($data) {
        return implode("\n",$this->_html)."\n".'<script>'.implode("\n",$this->_js).'</script>';
    }

    public function sqlval($v) {
        return $v;
    }


}
