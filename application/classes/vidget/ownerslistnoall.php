<?php


class Vidget_Ownerslistnoall extends Vidget_Ownerslist
{
    public function _search($vars)
    {
        $list = [''=>'--']+$this->_owners_list;
        return form::select('owner', $list, arr::get($vars, 'owner', ''), ['required'=>'required','class' => 'select2','style'=>'min-width: 0; width: 100%;']);
    }


}
