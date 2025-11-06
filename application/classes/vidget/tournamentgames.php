<?php

class Vidget_Tournamentgames extends Vidget_Input
{
    /*
     * возвращает включенные игры для текущего турнира
     */
    protected function games($share_id) {
        return orm::factory('tournament_game')->where('share_id', '=', $share_id)->find_all();
    }
    
    protected function my_games() {
        return orm::factory('game')->where('provider', '=', 'our')->find_all();
    }

    public function _item($model)
	{
        $games = $this->games($model->id);
        $my_games = $this->my_games();
        
        $select_data = "<div><select name='{$this->name}[{$model->id}][] style='padding: 10px; margin: 5px'>";
        
        if(!count($games)) {
            $sql_last = <<<SQL
                Select id
                From shares
                Where type = 'tournament'
                Order by time_to
                Limit 1
SQL;
            $res = db::query(1, $sql_last)->execute()->as_array();
            $last_tournament_id = $res[0]['id']??0;
            $games = orm::factory('tournament_game')->where('share_id', '=', $last_tournament_id)->find_all();
        }
        
        foreach ($my_games as $m) {
            $select_data .= "<option value='{$m->id}'>{$m->visible_name}</option>";
        }
        
        $select_data .= "</select><div style='cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>" . __('Удалить') . "</div></div>";
        
        
        $js = <<<JS
            <script>
                $('#add_new_{$this->name}').click(function() {
                    var select = "{$select_data}";
                    $(this).parent().append(select);
                });
            </script>
JS;
        
        $html = '';
        
        foreach ($games as $game) {
            $html .= "<div><select name='{$this->name}[{$model->id}][]' style='padding: 10px; margin: 5px'>";
            foreach ($my_games as $my) {
                $selected = $game->game_id==$my->id?'selected':'';
                $html .= "<option value='{$my->id}' {$selected}>{$my->visible_name}</option>";
            }
            $html .= "</select><div style='cursor:pointer;display: inline-block;margin-left: 10px;' onclick='$(this).parent().remove();'>" . __('Удалить') . "</div></div>";
        }
        
        $html .= '<div id="add_new_'. $this->name .'" style="padding: 20px; cursor:pointer; border: 2px solid red; width:20%; margin-bottom: 20px; text-align: center">' . __('Добавить') . '</div>';
        
        return $html.$js;
	}

	public function _list($model)
	{
		return ' - ';
	}
    
    public function handler_save($data, $old_data, $model) {
        if(isset($data[$this->name])) {
            $games = $data[$this->name];

            foreach ($games as $share_id => $new_games) {
                $old_games = [];
                
                foreach ($this->games($share_id) as $game) {
                    $old_games[] = $game->game_id;
                }
                
                /*
                 * получаем массив игр которые нужно добавить
                 */
                $add_games = array_unique(array_diff($new_games, $old_games));
                /*
                 * получаем массив игр которые нужно удалить
                 */
                $delete_games = array_diff($old_games, $new_games);
                
                if($add_games) {
                    $sql_add = <<<SQL
                        INSERT INTO tournament_games(share_id, game_id) VALUES
SQL;
                    $iter = 1;
                    foreach ($add_games as $add_id) {
                        $sql_add .= "({$share_id}, $add_id)";
                        
                        if($iter!=count($add_games)) {
                            $sql_add .= ',';
                        }
                        $iter++;
                    }
                    
                    db::query(2, $sql_add)->execute();
                }
                
                if($delete_games) {
                    $delete_games = array_map(function($v) {
                        return intval($v);
                    }, $delete_games);
                                    
                    $sql_delete = <<<SQL
                        DELETE From tournament_games 
                        Where share_id = :share_id
                            AND game_id in :game_ids
SQL;
                    db::query(4, $sql_delete)->parameters([
                        ":share_id" => $share_id,
                        ":game_ids" => $delete_games,
                    ])->execute();
                }
            }
        } else {
            /*
             * удаляем все включенные игры
             */
            $sql_delete_all = <<<SQL
                DELETE From tournament_games 
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
