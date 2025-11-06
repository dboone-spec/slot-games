<?php

class Controller_Admin1_Discount extends Controller_Admin1_Base
{


    public function action_index()
    {

        $m = $_GET['m'] ?? date('m', time() - 28 * 24 * 60 * 60);
        $y = $_GET['y'] ?? date('Y', time() - 28 * 24 * 60 * 60);

        $mNext = $m + 1;
        $yNext = $y;
        if ($mNext > 12) {
            $mNext = 1;
            $yNext++;
        }
        //time for currency rates in currency_rate table
        $crdate = mktime(0, 0, 0, $mNext, 1, $yNext);

        $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

        $nowYear = date('Y');

        for ($i = 2022; $i <= $nowYear; $i++) {
            $years[$i] = $i;
        }


        $office_id = arr::get($_GET, 'office_id', -1);
        $owner = arr::get($_GET, 'owner', -1);
        $is_test = arr::get($_GET, 'is_test', false);
        //List of games
        $gameOn = arr::get($_GET, 'gameOn', []);


        $time_from = isset($_GET['time_from']) ? $_GET['time_from'] : null;
        $time_to = isset($_GET['time_to']) ? $_GET['time_to'] : null;
        if (!isset($time_from)) {
            $time_from = date("Y-m-d", strtotime("-1 months"));
        }
        if (!isset($time_to)) {
            $time_to = date("Y-m-d");
        }


        $sql = "SELECT office_id, o.visible_name , game,  COALESCE(cr.value,0) as value, c.code as cur_name,
            sum(
                case when  bettype = 'norcfs' then 0 else amount_in end
            ) as in,
            sum(
                case when  bettype = 'norcfs' then  amount_in else 0 end
            ) as in_fs
			,sum(amount_out) as out,
			sum(
                case when  bettype = 'norcfs' then 0 else amount_in end
            ) -  sum(amount_out) as win
						
from statistics s
join offices o on s.office_id=o.id
join currencies c on o.currency_id=c.id
left join currency_rates cr on cr.currency=c.code  and cr.date=:crdate
where
    s.date>=:timeFrom
and s.date<=:timeTo
and s.test=0
";

        if (count($gameOn) > 0) {
            $sql .= ' and game in :games ';
        } else {
            $sql .= ' and 1=2';
        }


        $sql .= " AND s.office_id in :officesEnabled ";

        if ($office_id != -1) {
            $sql .= " AND s.office_id =:oid ";

        }

        if ($owner != -1) {
            $sql .= " AND o.owner =:ownerid ";
        }

        $sql .= 'GROUP BY 1,2,3,4,5
            order by 2,3';


        if (person::$role == 'report') {
            $o_id = person::user()->office_id;
            $enabledOffices = [$o_id => $o_id];
        } else {
            $enabledOffices = Person::user()->offices($is_test);
        }


        $data = db::query(1, $sql)->parameters([
            ':timeFrom' => $time_from,
            ':timeTo' => $time_to,
            ':office_id' => $office_id,
            ':ownerid' => $owner,
            ':officesEnabled' => $enabledOffices,
            ':crdate' => $crdate,
            ':games' => $gameOn,
            ':oid' => $office_id

        ])->execute()->as_array();


        $officesList = [-1 => 'All'] + Person::user()->officesName(null, true);

        $games = db::query(1, 'select name,visible_name from games where show>0 order by visible_name')->execute()->as_array('name');


        //export to xslx
        if (arr::get($_GET, 'xls') == 'go') {
            $time_to = date('d-m-Y', strtotime($time_to));
            $time_from = date('d-m-Y', strtotime($time_from));

            $xlsx = [];
            $xlsx[] = ["Discount report from $time_from to $time_to"];
            $xlsx[] = ['Date from', 'Date to', 'Office id', 'Office name', 'Game', 'Currency', 'Rate', 'Day of rate', 'In', 'In DS', 'Out', 'Win'];


            foreach ($data as $row) {

                $xlsx[] = [
                    $time_from,
                    $time_to,
                    $row['office_id'],
                    $row['visible_name'],
                    $games[$row['game']]['visible_name'],
                    $row['cur_name'],
                    $row['value'],
                    date('d-m-Y', $crdate - 60 * 60 * 24),
                    $row['in'] ?? 0,
                    $row['in_fs'] ?? 0,
                    $row['out'] ?? 0,
                    $row['win'] ?? 0,
                ];

            }

            $writer = new XLSXWriter();
            $writer->writeSheet($xlsx, 'Sheet1');            // no headers
            $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->response->headers('Content-Disposition', 'attachment;filename="discount_' . $time_from . '_' . $time_to . '.xlsx"');
            $this->response->headers('Cache-Control', 'max-age=0');
            $this->response->body($writer->writeToString());
            $this->auto_render = false;
            return null;
        }


        $view = new View('admin1/discount/index');
        $view->time_from = $time_from;
        $view->time_to = $time_to;
        $view->office_id = $office_id;
        $view->owner = $owner;
        $view->is_test = $is_test;
        $view->officesList = $officesList;
        $view->data = $data;
        $view->owners =  Person::user()->owners([-1 => 'All']);;
        $view->games = $games;
        $view->gameOn = $gameOn;
        $view->crdate = $crdate;
        //$view->owner_offices = $owner_offices;


        $view->m = $m;
        $view->y = $y;
        $view->months = $months;
        $view->years = $years;


        $this->template->content = $view;

    }


}
