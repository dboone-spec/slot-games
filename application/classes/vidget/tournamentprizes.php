<?php

class Vidget_Tournamentprizes extends Vidget_Input {
    /*
     * возвращает включенные игры для текущего турнира
     */
    protected function prizes($share_id) {
        return orm::factory('tournament_prize')->where('share_id', '=', $share_id)->order_by('place')->find_all();
    }

    public function _item($model)
	{
        $prizes = $this->prizes($model->id);
        
        if(!count($prizes)) {
            $sql_last = <<<SQL
                Select id
                From shares
                Where type = 'tournament'
                Order by time_to
                Limit 1
SQL;
            $res = db::query(1, $sql_last)->execute()->as_array();
            $last_tournament_id = $res[0]['id']??0;
            $prizes = orm::factory('tournament_prize')->where('share_id', '=', $last_tournament_id)->order_by('place')->find_all();
        }
        
        $max_place=1;
        
        $css = <<<CSS
            <style>
                .prize_table {
                    text-align: center; 
                    border: 1px solid black; 
                    border-collapse: collapse; 
                    cellspacing: 2px;
                    width: 70%;
                }
                .prize_table th,
                .prize_table td {
                    padding: 3px;
                    border: 1px solid black;
                }
                .prize_table td input {
                    width:100%;
                }
            </style>
CSS;
        
        $html = '<table class="prize_table"><tr><td>' . __('Место') . '</td><td>' . __('Приз') . '</td><td></td></tr>';
        
        foreach ($prizes as $p) {
            $html .= "<tr><td>{$p->place}</td><td><input name='{$this->name}[{$p->place}]' value='{$p->prize}' /></td><td onclick='$(this).parent().remove();' style='cursor: pointer'>" . __('Удалить') . "</td></tr>";
            
            if($max_place < $p->place) {
                $max_place = $p->place + 1;
            }
        }

        $html .= '</table>';
        $html .= '<div id="add_new_'. $this->name .'" style="padding: 20px; cursor:pointer; border: 2px solid red; width:20%; margin: 20px 0; text-align: center">'. __('Добавить') . '</div>';
        
        $select_data = "<td onclick='$(this).parent().remove();' style='cursor: pointer'>" . __('Удалить') . "</td></tr>";
        
        $js = <<<JS
            <script>
                var place = $max_place;
                
                $('#add_new_{$this->name}').click(function() {
                    var select = "<tr><td>";
                    select += place;
                    select += "</td><td><input name='{$this->name}[" + place + "]' value='' /></td>{$select_data}";
                    
                    $(this).siblings('table').append(select);
                    
                    place++;
                });
            </script>
JS;
        
        return $css.$html.$js;
	}

	public function _list($model)
	{
		return ' - ';
	}
    
    public function handler_save($data, $old_data, $model) {
        if(isset($data[$this->name])) {
            $prizes = $data[$this->name];
            
            foreach ($prizes as $place => $prize_text) {
                $prize_model = new Model_Tournament_Prize(["share_id"=>$model->id, "place"=>$place]);
                
                if(!$prize_model->loaded()) {
                    $prize_model->share_id = $model->id;
                    $prize_model->place = $place;
                } 
                $prize_model->prize = $prize_text;
                
                $prize_model->save();
            }
            
            $new_places = array_keys($prizes);
            $old_places = [];
            
            foreach ($this->prizes($model->id) as $p) {
                $old_places[] = $p->place;
            }
            
            $delete_places = array_diff($old_places, $new_places);
            
            if($delete_places) {
                $delete_places = array_map(function($v) {
                    return intval($v);
                }, $delete_places);

                $sql_delete = <<<SQL
                    DELETE From tournament_prizes
                    Where share_id = :share_id
                        AND place in :places
SQL;
                db::query(4, $sql_delete)->parameters([
                    ":share_id" => $model->id,
                    ":places" => $delete_places,
                ])->execute();
            }
        } else {
            /*
             * удаляем все включенные игры
             */
            $sql_delete_all = <<<SQL
                DELETE From tournament_prizes 
                Where share_id = :share_id
SQL;
            db::query(4, $sql_delete_all)->param(':share_id', $model->id)->execute();
        }
        
        return $model;
    }
    
    public function handler_search($model, $vars) {
        return '';
    }
}
