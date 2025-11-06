<?php

//require jquery.durationpicker.js

class paramsbuilder_selecttimediff extends paramsbuilder_base
{

    public function render($data)
    {

        $type_name = get_class($this);



        $this->_html = [];
        $this->_js   = [];

        $labels = $this->_model->labels();

        $label = '"' . ($labels[$this->_field] ?? $this->_field) . '" older than';

        $btn_body = '<i class="fa fa-plus-circle"></i>&nbsp;';
        $btn_body .= $label;

        $this->_html[] = form::button($this->_field . '_btn',$btn_body,[
                        'class' => 'create_button_' . $type_name . ' btn btn-primary',
                        'type' => 'button',
        ]);

        $btn_del_body = '<i class="fa fa-minus-circle"></i>&nbsp;';
        $btn_del_body .= $label;

        $del_btn = form::button($this->_field . '_btn_delete',$btn_del_body,[
                        'class' => 'create_button_' . $type_name . ' btn btn-danger',
                        'type' => 'button',
        ]);

        $input_value = 0;

        if(!empty($data))
        {
            foreach($data as $param)
            {

                if(isset($param->$type_name))
                {
                    foreach($param->$type_name as $one_builder)
                    {
                        if($one_builder->field == $this->_field)
                        {

                            if($one_builder->op == '<=')
                            {
                                //start
                                $input_value = $one_builder->val;
                            }
                        }
                    }
                }
            }
        }

        $js_input = $input_value;



        $this->_js[] = <<<JS

                $('.create_button_{$type_name}[name={$this->_field}_btn]').click(function() {

                    var paramsbuilder_form = $('#paramsbuilder');
                    var name = '$this->_field';
                    console.log(name);
                    if(paramsbuilder_form.find('[builder_field=$this->_field][builder_type=$type_name]').length>0) {
                        return;
                    }

                    var div = $('<div>').appendTo(paramsbuilder_form);
                    div.attr('builder_type','$type_name');
                    div.attr('builder_field','$this->_field');
                    div.css('display','flex');
                    div.css('line-height','36px');

                    div.html('$label&nbsp;');

                    var input = $('<input>').appendTo(div);
                    input.attr('name','{$type_name}['+name+'][]');
                    input.attr('size',28);
                    input.val('$js_input');

                    input.durationpicker({allowZeroTime: false})

                    var del_btn = $('$del_btn').appendTo(div);

                    del_btn.click(function() {
                        paramsbuilder_form.find('[builder_field=$this->_field][builder_type=$type_name]').remove();
                    });
                });

JS;

        if(!empty($js_input))
        {
            $this->_js[] = <<<JS
                    $('.create_button_{$type_name}[name={$this->_field}_btn]').click();
JS;
        }


        return parent::render($data);
    }

    public function save($data)
    {


        $type_name = get_class($this);

        if(isset($data[$type_name]))
        {
            if(isset($data[$type_name][$this->_field]))
            {

                $val = $data[$type_name][$this->_field][0];

                return [
                        $type_name => [
                                [
                                        'field' => $this->_field,
                                        'op' => '<=',
                                        'val' => $val,
                                ],
                        ],
                ];
            }
        }

        return [];
    }

    public function sqlval($v)
    {
        return DB::expr('extract(epoch from now() at time zone \'UTC\')::int-'.$v);
    }

}
