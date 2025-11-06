<?php

class Controller_Admin1_Activity extends Controller_Admin1_Base
{


    public function action_index()
    {

        $sql = "select vdate(created) as created, count(id) as count,sum(amount)/count(id) as avgbet
        from bets
        where  created >extract(epoch from now() at time zone 'UTC')::int-30
        group by 1
        order by 1 desc";

        $sql = "select vdate(b.created) as created, COALESCE(p.comment,'other') as owner, count(b.id) as count,sum(b.amount)/count(b.id) as avgbet
        from bets b
				join offices o
				on o.id=b.office_id
				left join persons p on p.id=o.owner
        where  b.created >extract(epoch from now() at time zone 'UTC')::int-30
            and b.created <extract(epoch from now() at time zone 'UTC')::int
        group by 1,2
        order by 1 desc";

/*
        $sql = "select vdate(b.created) as created, COALESCE(p.comment,'other') as owner, count(b.id) as count,sum(b.amount)/count(b.id) as avgbet
        from bets b
				join offices o
				on o.id=b.office_id
				left join persons p on p.id=o.owner
        where  b.created >1687824039-30
            and b.created <1687824039
        group by 1,2
        order by 1 desc";
*/
        $rows = db::query(1, $sql)->execute()->as_array();


        if ($rows) {

            $countable = ['ADVBET', 'INFIN','PINUP','SoftGamings','BETCONSTRUCT','Olimp','PINCO','TVBET','BETB2B','EvenBet'];			
				
            $avgArr = [];
            foreach ($countable as $name) {
                $avgArr[$name] = 0;
            }
            $avg=0;
            $count = 0;
            $avgamount = 0;
            $data = [];

            foreach ($rows as $r) {
                if (isset($data[$r['created']])) {
                    $data[$r['created']]['count']+=$r['count'];
                    $data[$r['created']]['avgbet']+=$r['avgbet'];
                } else {
                    $count++;
                    $data[$r['created']] = ['created' => $r['created'], 'count' => $r['count'], 'avgbet' => $r['avgbet']];
                }

                if (in_array($r['owner'],$countable)){
                    $avgArr[$r['owner']]+=$r['count'];
                }
                $avg += $r['count'];
                $avgamount += $r['avgbet'];
            }


            $avg /= $count;
            $avgamount /= $count;
            foreach ($countable as $owner){
                $avgArr[$owner]=$avgArr[$owner]/$count;
            }

            $last = end($data);
            $last = $last['created'];


            $view = new View('admin1/activity/index');
            $view->data = $data;
            $view->avg = round($avg);
            $view->avgamount = $avgamount;
            $view->count = $count;
            $view->last = $last;
            $view->avgArr=$avgArr;


        } else {
            $view = new View('admin1/activity/no');
        }


        $this->template->content = $view;

    }


}
