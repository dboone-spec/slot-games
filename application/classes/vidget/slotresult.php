<?php

/**
 * параметры
 * imgdir - web-каталог где лежат картинки, которые показывать в админке
 */
class Vidget_Slotresult extends Vidget_Echo {

	private function _getAGTIconLink($model,$sym) {
        $imgurl=kohana::$config->load('static.static_domain');
        $imgurl.='/games/agt/images/games/';

        $game=$model->__get('game');

        return $imgurl . $game . '/icons/small_' . $sym . '.png';
    }

    private function _getImg($model,$results,$index) {

        $game=$model->__get('game');
        $game_type='agt';

        if($model->method == 'api' && $game_type) {
            return 'https://content.gameconnectapi.com/games/agt/images/games/' . $game.'/icons/small_'.$results[$index].'.png';
        }

        $r=$results[$index];

        if($game=='1win' && $r==9) {
            $r+=($index-1)%4;
        }

        return $this->_getAGTIconLink($model,$r);
    }


    protected function _roshamboResult($results)
    {
        $html='<table>';

        $html.= '<tr>';
        foreach($results as $result)
        {
            $url = kohana::$config->load('static.static_domain') . '/games/agt/images/games/roshambo/ui/hand' . $result . '.png';

            $html .= '<td style="padding:0 !important; border: none !important;">' .
                    HTML::image($url,['width' => '24px']) .
                    '</td>';
        }
        $html.= '</tr>';
        $html.= '</table> ';

        return $html;
    }

    protected function _minerResult($results) {
         $html='<table>';

        $html.= '<tr>';
        foreach($results as $result) {
            $img='smallgold';
            if($result==1) {
                $img='smallboom';
            }

            $url=kohana::$config->load('static.static_domain').'/games/agt/images/games/sapper/ui/'.$img.'.png';

            $html .= '<td style="padding:0 !important; border: none !important;">' .
                HTML::image($url, ['width' => '24px']).
                '</td>';
        }
        $html.= '</tr>';
        $html.= '</table> ';

        return $html;
     }

     protected function _convertToImage($model, $size = '50px') {

            $r=$model->__get($this->name);
			$game = $model->__get('game');
            $game_type = 'agt';

            $results = json_decode($r, 1);

            if(is_null($results)) {
                return $r;
            }

            if(th::isMoonGame($game) && $results==0) {
                return 'bet';
            }

            if(th::isMoonGame($game)) {
                return $results;
            }

			if($game=='jp') {
                return jpcard::print_card($results);
            }

            if (!is_array($results)) {
                if (in_array($results, [0, 1])) {
                    return 'red';
                } elseif (in_array($results, [2, 3])) {
                    return 'black';
                } else {
                    return $results;
                }
            }

            if($model->__get('game')=='keno') {
                return implode(',',$results);
            }

            if($game=='sapper') {
                return $this->_minerResult($results);
            }

            if(in_array($game,['roshambo','spinners'])) {
                return $this->_roshamboResult($results);
            }


            if ($model->method == 'api' && $game_type=='agt') {
                $bars=5;
                $heigth=3;
                if(count($results)==9) {
                    $bars=3;
                    $heigth=3;
                }
                if(count($results)==20) {
                    $bars=5;
                    $heigth=4;
                }
                if(count($results)==24) {
                    $bars=6;
                    $heigth=4;
                }
            }
            elseif (!empty($model->method) && $model->method != 'random' && $model->method != 'zero' && $model->method != 'bank') { //убираем апи и прочее
                return $model->__get($this->name);
            }

            if(!isset($bars) && !isset($heigth)) {

                $config = Kohana::$config->load($game_type . '/' . $game );

                if(($config['pay_rule']??'')=='payways6') {
                    $bars=count($config['bars']);
                }
                elseif(isset($config['width'])) {
                    $bars=$config['width'];
                }
                else {
                    if (!isset($config['lines'][1][0])){
                       return '';
                    }

                    $bars=count($config['lines'][1][0]);

                    if(in_array($game,['alwayshot','hotcherry','ultrahot','threee'])){
                        $bars=3;
                    }
                }

                $heigth=arr::get($config,'heigth',3);
            }

            $index=0;

            $images = '<table> ';


            $imgurl=kohana::$config->load('static.static_domain');
            $imgurl.='/games/agt/images/games/';

            $class_name  = "Slot_Agt_" . ucfirst($game);

            $can_calc=false;

            if(!in_array($game,['pharaoh2','aislot','tesla','bookofset'])) {
                if(class_exists($class_name))
                {
                    $calc = new $class_name($game);

                    $can_calc=!$calc instanceof Slot_Agt_Vangogh;

                }
                else
                {
                    $slotClass='Slot_Agt';
                    $calc = new $slotClass($game);
                    $can_calc=true;
                }
				
				if($calc instanceof Slot_Agt_Hotways) {
					$can_calc=false;
				}
            }
			

            $wins=[];

            if($can_calc) {

                $results_vals=array_values($results);

                $calc->amount_line = $model->real_amount/$model->come;
                $calc->cline       = $model->come;
                $calc->amount      = $calc->amount_line * $calc->cline;

                foreach($calc->bars as $num=>$bar) {
                    $iBar=implode(',',$bar).','.implode(',',$bar);
                    $rPart=[];
                    for($i=0;$i<$heigth;$i++) {
                        $rPart[]=$results_vals[$num-1 + $i*$bars];
                    }
                    $iRPart=implode(',',$rPart);
                    $f=strpos($iBar,$iRPart);
                    $first=substr($iBar,0,$f);
                    $calc->pos[$num]=count(explode(',',$first))-1;
                }

                $calc->correct_pos();
                $calc->win();

                $wins=$calc->win;

                $wins=array_filter($wins,function($v) {return $v>0;});

                if(!empty($wins)) {
                    $images = '<table style="cursor:pointer;" onclick="$(\'.lines_hidden'.$model->id.'\').toggle();"> ';
                }
            }

            if(isset($calc) && $calc instanceof Slot_Agt_Hotways) {
                 $images='<table style="">';
                    foreach($results as $barNum=>$res) {
                    $images.= '<tr style="display: table-cell">';
                    foreach($res as $sym) {
                        $images .= '<td style="padding:0 !important; display: block;">' .HTML::image($this->_getAGTIconLink($model,$sym), ['width' => $size]).'</td>';
                    }
                    $images.= '</tr>';
                }
             }
             else {
                for ($i = 1; $i <= $heigth; $i++) {
                    $images.= '<tr>';
                    for ($y = 1; $y <= $bars; $y++) {
                        $index++;
                        $images .= '<td style="padding:0 !important;">' .HTML::image($this->_getImg($model,$results,$index), ['width' => $size]).'</td>';
                    }
                    $images.= '</tr>';
                }
             }

            $images.= '</table> ';

            if(!empty($wins)) {
                $images.=$this->_printWinLines($wins,$model);
            }

            return $images;

    }

    protected function _printWinLines($wins,$model) {
        $t='<div class="lines_hidden'.$model->id.'" style="display: none;">';
        $total=['lines'=>0,'win'=>0];
        foreach($wins as $l=>$v) {
            if($l<=0) {
                $t.='Scatters win: '.$v.'<br>';
                continue;
            }
            $total['lines']++;
            $total['win']+=$v;
            $t.='Line: '.$l.'; win: '.$v.'<br>';
        }

        if($total['lines']>0) {
            $t.='Total lines: '.$total['lines'].'<br>';
        }

        if($total['win']>0) {
            $t.='Total win: '.$total['win'].'<br>';
        }

        return $t.'</div>';
    }

    public function _item($model) {
        return $this->_convertToImage($model);
    }


    protected function _roulettenums($model) {
        $c = kohana::$config->load($model->__get('game_type').'/'.$model->__get('game').'.betnum');
        $result = json_decode($model->__get($this->name),1);

        if(!$result) {
            return $model->__get($this->name);
        }

        $color='green';
        $textcolor='#fff';
        if(in_array($result['num'],$c[149])) {
            $textcolor='#000';
            $color='red';
        }
        else if(in_array($result['num'],$c[150])) {
            $color='black';
        }

        $r = '<span style="padding: 5%; background: '.$color.'; color: '.$textcolor.'">'.$result['num'].'</span>';

        return $r;
    }


    public function _list($model) {
        if($model->__get('game')=='virtualroulette') {
            return $this->_roulettenums($model);
        }
        return $this->_convertToImage($model, '30px');
    }

}
