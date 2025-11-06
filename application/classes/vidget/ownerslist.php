<?php


class Vidget_Ownerslist extends Vidget_List
{
    protected $_owners_list = [];
    protected $_owner_offices = [];
    protected $_office_owners = [];

    public function __construct($name, $model)
    {
        parent::__construct($name, $model);
        $owners_sql = db::query(1, 'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = db::query(1, 'select id,owner from offices where owner is not null')->execute()->as_array('id');


        foreach ($owners_sql as $s) {
            $this->_owners_list[$s['id']] = $s['comment'];
        }

        foreach ($owner_offices_sql as $s) {
            $this->_owner_offices[$s['id']] = arr::get($owners_sql, $s['owner'], ['comment' => ''])['comment'];
            $this->_office_owners[$s['owner']][] = $s['id'];
        }
    }

    public function _list($model)
    {
        $this->param['list'] = $this->_owner_offices;
        return parent::_list($model);
    }

    public function _search($vars)
    {
        $list = array('alldata' => __('All')) + $this->_owners_list;
        return form::select('owner', $list, arr::get($vars, 'owner', ''), ['class' => 'select2']);
    }

    public function handler_search($model, $vars)
    {
        $value = 'alldata';

        if (isset($vars['owner']) and !empty($vars['owner']) and $vars['owner']!='alldata'){
            $value=$this->_office_owners[$vars['owner']];
            $model->where($this->model->object_name().'.'.$this->name,'in',$value);
        }

        $this->search_vars['owner'] = arr::get($vars, 'owner', '');
        return $model;
    }
}
