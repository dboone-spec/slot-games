<?php

class Controller_Admin_Counters extends Controller_Admin_Base
{



public function action_index()
{


    $office_id = arr::get($_GET,'office_id',-1);
    $brand = arr::get($_GET,'brand',(PROJECT==1)?'agt':'all');
    $is_test = arr::get($_GET,'is_test',0);


    $where=' where office_id in :officesEnabled';

    if ($office_id!=-1){
        $where .= " AND office_id =:oid ";
    }

    if ($brand!='not'){
        $where .= " AND g.brand =:brand ";
    }

    $sql='select
            g.id, g.visible_name,g.brand,
            sum(c.in) as "in",sum(c."out"+c.free+c.bonus) as "out",sum(c."count"+c.free_count+c.bonus_count) as "count",
            sum(c.in-(c."out"+c.free+c.bonus) ) as "win",
            sum(c."free") as "free", sum(c.free_count) as free_count,
            sum(c.bonus) as bonus, sum(c.bonus_count) as bonus_count,
            sum(c.double_in) as double_in, sum(c.double_out) as double_out, sum(c.double_count) as double_count,
            sum(c.double_in - c.double_out) as double_win,
            sum(c.fs_api_in) as fs_api_in, sum(c.fs_api_out) as fs_api_out, sum(c.fs_api_count) as fs_api_count,
            sum(c.fs_api_in - c.fs_api_out) as fs_api_win,
            sum(c.fs_cash_in) as fs_cash_in, sum(c.fs_cash_out) as fs_cash_out, sum(c.fs_cash_count) as fs_cash_count,
            sum(c.fs_cash_in - c.fs_cash_out) as fs_cash_win

            from counters c
            join games g on g.id=c.game_id
            '.$where.'
            GROUP BY 1,2
            ORDER BY 2';


    $data = db::query(1, $sql)
            ->param(':oid',$office_id)
            ->param(':brand',$brand)
            ->param(':officesEnabled',Person::user()->offices($is_test))
            ->execute()
            ->as_array();

    $total=[
            'in'=>0,'out'=>0,'win'=>0, 'count'=>0, 'rtp'=>0,
            'free'=>0,'free_count'=>0,'free_rtp'=>0,
            'bonus'=>0, 'bonus_count'=>0,'bonus_rtp'=>0,

            'fs_cash_in'=>0, 'fs_cash_out'=>0, 'fs_cash_win'=>0,'fs_cash_count'=>0, 'fs_cash_rtp'=>0,
            'fs_api_in'=>0, 'fs_api_out'=>0, 'fs_api_win'=>0, 'fs_api_count'=>0, 'fs_api_rtp'=>0,
		'double_in'=>0, 'double_out'=>0, 'double_win'=>0, 'double_count'=>0, 'double_rtp'=>0,
		];

    foreach($data as &$row){
        foreach($total as $k=>$v) {
            if($k=='rtp') {
                if($total['in']>0) {
                    $total[$k]=(round($total['out']/$total['in'],2)*100);
                }
            }
            elseif($k=='free_rtp') {
                if($total['in']>0) {
                    $total[$k]=(round($total['free']/$total['in'],2)*100);
                }
            }
            elseif($k=='bonus_rtp') {
                if($total['in']>0) {
                    $total[$k]=(round($total['bonus']/$total['in'],2)*100);
                }
            }
            elseif($k=='double_rtp') {
                if($total['double_in']>0) {
                    $total[$k]=(round($total['double_out']/$total['double_in'],2)*100);
                }
            }
            elseif($k=='fs_api_rtp') {
                if($total['fs_api_in']>0) {
                    $total[$k]=(round($total['fs_api_out']/$total['fs_api_in'],2)*100);
                }
            }
            elseif($k=='fs_cash_rtp') {
                if($total['fs_cash_in']>0) {
                    $total[$k]=(round($total['fs_cash_out']/$total['fs_cash_in'],2)*100);
                }
            }
            else {
                $total[$k]+=$row[$k];
            }
        }
    }

    $brandList=['not'=>'Not select','agt'=>'AGT'];
    if (PROJECT==2){
        $brandList=['all'=>'All','agt'=>'AGT','egt'=>'EGT', 'novomatic'=>'Novomatic', 'igrosoft'=>'Igrosoft' ];
    }

    $officesList=[-1=>'All']+Person::user()->officesName(null,true);

    $view=new View('admin/counters/index');
    $view->office_id=$office_id;
    $view->is_test=$is_test;
    $view->brand=$brand;
    $view->officesList=$officesList;
    $view->data=$data;
    $view->total=$total;
    $view->brandList=$brandList;
    $this->template->content=$view;

}


}
