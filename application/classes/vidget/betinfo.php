<?php

class Vidget_BetInfo extends Vidget_Echo
{
    public function _list($model) {
        $info=$model->__get($this->name);
        if(in_array($model->__get('game'),['pharaoh2','aislot','bookofset'])) {

            $imgurl=kohana::$config->load('static.static_domain');
            $imgurl.='/games/agt/images/games/';
            $imgurl .= $model->__get('game') . '/icons/small_$1.png';

            $imgurl=HTML::image($imgurl,['width' => '30px']);

            $info=preg_replace("/\(([0-9]+)\)/",$imgurl,$info);
        }
        return $info;
    }
}
