<?php

class Vidget_Blockuser extends Vidget_Input
{
    function _list($model){
        $list = arr::get($this->param, 'list');
        
        return $list[$model->__get($this->name)]??0;
    }

    function _item($model){
        $list = arr::get($this->param, 'list');
        $selected = $model->__get($this->name);
        
        $html = '';
        if($selected == 0) {
            $admin_dir = ADMINR;
            $html .= <<<HTML
                <span class="btn btn-danger" id="dialog_button">Заблокировать?</span><div id="dialog" style='display:none'><textarea type='text' id='dialog_message' name='dialog_message' style='width: 465px; height:65px'></textarea></div>
                <script>
                    $( "#dialog_button" ).click(function() {
                        $('#dialog').dialog({
                            title: 'Почему заблокирован пользователь?',
                            minWidth: 500,
                            minHeight: 200,
                            buttons: [
                            {
                                text: "Да",
                                click: function() {
                                    $(this).dialog( "close" );
                                    $.ajax({
                                        method: 'POST',
                                        url: '/$admin_dir/user/block/{$model->id}',
                                        data: {
                                            'blocked_text': $('#dialog_message').val()
                                        },
                                        success: function (data) {
                                            window.location = window.location;
                                        },
                                        error: function () {
                                            alert('Ошибка при блокировке игрока');
                                        }
                                    })
                                }
                            },
                            {
                                text: "Нет",
                                click: function() {
                                    $(this).dialog( "close" );
                                }
                            }    
                            ]
                        });
                    });
                </script>
HTML;
        }
        
        return form::select($this->name($model), $list, $selected, []) . $html;
    }

    function _search($vars){

        return form::input($this->name,$vars[$this->name]);
    }

    function handler_search($model,$vars){
        if (isset($vars[$this->name]) and !empty($vars[$this->name])){
                $this->search_vars[$this->name] = $vars[$this->name];
                return $model->where($this->name, '=', (int)$vars[$this->name]);
        }
        $this->search_vars[$this->name]='';
        return $model;
    }
}

