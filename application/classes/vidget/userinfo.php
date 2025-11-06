<?php

class Vidget_Userinfo extends Vidget_Echo
{
    function _list($model){
        $html='<div style="background: #e4e7ea;">';
        $route = Request::current()->route();
        $href = $route->uri(['controller'=>'userbet']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'" title="Статистика"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i></a>';
        $html.='<br />';

        $href = $route->uri(['controller'=>'userhistory']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'" title="История"><i class="fa fa-history fa-fw" aria-hidden="true"></i></a>';
        $html.='<br />';

        $href = $route->uri(['controller'=>'bet']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'">Ставки</a>';
        $html.='<br />';

        $href = $route->uri(['controller'=>'bonus']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'">Бонусы</a>';
        $html.='<br />';

        $href = $route->uri(['controller'=>'payment']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'">Платежи</a>';
        $html.='</div>';

        $href = $route->uri(['controller'=>'userprofile']).'?user_id='. $model->__get($this->name);
        $html.='<a href="/'.$href.'">Профиль</a>';
        $html.='</div>';

        return $html;
    }


}

