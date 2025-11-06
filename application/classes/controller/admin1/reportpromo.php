<?php

class Controller_Admin1_Reportpromo extends Controller_Admin1_Base
{


    public function action_index()
    {


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', false);
        $by_month = arr::get($_GET, 'by_month', false);
        $only_total = arr::get($_GET, 'only_total', false);
        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        if (!isset($time_from)) {
            $time_from = date("Y-m-d", strtotime("-1 months"));
        }
        if (!isset($time_to)) {
            $time_to = date("Y-m-d");
        }

        //конвертация
        $c = '1';
        if ($convert = arr::get($_GET, 'convert')) {
            $c = 'c.val';
        }


        $sql = "select s.date,s.office_id,o.visible_name ,s.in * $c as in ,s.out * $c as out ,s.count,s.users,
                s.max_promo_out * $c as max_promo_out,s.max_promo_count ,s.cancel_count,
                e.h, e.m, e.duration, c.mult
        from statistic_events s
        join offices o on o.id=s.office_id
        join events e on e.id=s.event_id
        join currencies c on c.id=o.currency_id
        where date>=:from
        and date<=:to
        AND s.office_id in :officesEnabled";

        $sqlPromo = "select date||'-'||office_id as id,sum(amount_out * $c) as amount_out,sum(count) as count
                From statistics s
                join offices o on o.id=s.office_id
                join currencies c on o.currency_id=c.id
                Where
                    date >= :from
                    AND date <= :to
                    AND bettype = 'prize'
                    AND s.office_id in :officesEnabled 
                    and s.test = 0";


        if ($office_id != -1) {
            $sql .= " AND s.office_id =:oid ";
            $sqlPromo .= " AND s.office_id =:oid ";
        }

        if ($owner != -1) {

            $sql .= " AND o.owner =:ownerid ";
            $sqlPromo .= " AND o.owner =:ownerid ";
        }


        $sql .= ' order by 1,2';
        $sqlPromo .= '  group by 1';


        if (person::$role == 'report') {
            $o_id = person::user()->office_id;
            $enabledOffices = [$o_id => $o_id];
        } else {
            $enabledOffices = Person::user()->offices($is_test);
        }


        $data = db::query(1, $sql)->param(':from', $time_from)
            ->param(':to', $time_to)
            ->param(':oid', $office_id)
            ->param(':ownerid', $owner)
            ->param(':officesEnabled', $enabledOffices)
            ->execute()
            ->as_array();


        $promo = db::query(1, $sqlPromo)->param(':from', $time_from)
            ->param(':to', $time_to)
            ->param(':oid', $office_id)
            ->param(':ownerid', $owner)
            ->param(':officesEnabled', $enabledOffices)
            ->execute()
            ->as_array('id');

        $total = [];
        $dayTotal = [];

        foreach ($data as &$row) {

            $id = "{$row['date']}-{$row['office_id']}";

            $from = $row['h'] * 60 * 60 + $row['m'] * 60;

            $fromH = date('H', $from);
            $fromM = date('i', $from);

            $to = $from + $row['duration'];

            $toH = date('H', $to);
            $toM = date('i', $to);

            $row['time'] = "$fromH:$fromM - $toH:$toM";
            $row['win'] = $row['in'] - $row['out'];
            $row['promo_out'] = $promo[$id]['amount_out'] ?? '0';
            $row['promo_count'] = $promo[$id]['count'] ?? '0';
            $row['max_promo_win'] = $row['win'] - $row['max_promo_out'] + $row['promo_out'];
        }

        $officesList = [-1 => 'All'] + Person::user()->officesName(null, true);

        $owners_sql = db::query(1, 'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = db::query(1, 'select id,owner from offices where owner is not null')->execute()->as_array('id');

        $owners = [-1 => 'All'];
        $owner_offices = [];

        foreach ($owners_sql as $s) {
            $owners[$s['id']] = $s['comment'];
        }

        foreach ($owner_offices_sql as $s) {
            $owner_offices[$s['id']] = arr::get($owners_sql, $s['owner'], ['comment' => ''])['comment'];
        }


        $view = new View('admin1/reportpromo/index');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->convert = $convert;
        $view->only_total = $only_total;
        $view->by_month = $by_month;
        $view->officesList = $officesList;
        $view->data = $data;
        //$view->total=$total;
        //$view->totalOffice=$totalOffice;
        $view->owners = $owners;
        $view->owner_offices = $owner_offices;
        $this->template->content = $view;

    }


}
