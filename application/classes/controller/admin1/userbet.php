<?php

class Controller_Admin1_Userbet extends Super
{

    public $mark = 'User bets '; //имя
    public $model_name = 'bet'; //имя модели
    public $sh = 'admin1/userbet'; //шаблон
    public $per_page = 30;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {


        $export = isset($_GET['export']) ? $_GET['export'] : null;

        $user_id = arr::get($_GET, 'user_id', -1);
        $external_id = arr::get($_GET, 'external_id', -1);
        $game = isset($_GET['game']) ? $_GET['game'] : null;
        $no_fs = isset($_GET['no_fs']) ? $_GET['no_fs'] : null;

        $user = new Model_User($user_id);

        $timeVidget = new Vidget_Timestamp('created', $user);
        $timeVidget->param('encashment_time', 0);
        $timeVidget->param('zone_time', 0);

        $timeVidget->handler_search($user, $_GET);

        $time_from_date = explode('-', $timeVidget->search_vars['created_start']);
        $time_from = mktime(
            $timeVidget->search_vars['created_h_start'],
            $timeVidget->search_vars['created_m_start'],
            $timeVidget->search_vars['created_s_start'],

            $time_from_date[1],
            $time_from_date[2],
            $time_from_date[0]
        );

        $time_to_date = explode('-', $timeVidget->search_vars['created_end']);
        $time_to = mktime(
            $timeVidget->search_vars['created_h_end'],
            $timeVidget->search_vars['created_m_end'],
            $timeVidget->search_vars['created_s_end'],

            $time_to_date[1],
            $time_to_date[2],
            $time_to_date[0]
        );


        $borderDate = status::instance()->usersMH;


        //Данные
        $headers = [
//                "created"    => "Дата",
            "user_id" => "User_id",
            "game" => "Game",
            "sum_amount" => "In",
            "sum_win" => "Out",
            "win" => "Win",
            "count_bets" => "Bets count",
            "percent" => "RTP",
        ];


        $sql_stat = 'select user_id, game, sum(count_bets) AS count_bets, SUM (sum_amount) as sum_amount, sum(sum_win) as sum_win, sum (win) as win
                    from
                    (
                    SELECT
                            b.user_id AS user_id,
                            b.game as game,
                            COUNT ( b.ID ) AS count_bets,
                            SUM ( CASE WHEN b.external_id > 0 then p.amount else b.amount end ) AS sum_amount,
                            SUM ( b.win ) AS sum_win,
                            SUM ( (CASE WHEN b.external_id > 0 then p.amount else b.amount end) - b.win ) AS win

                    FROM
                            bets b
                            left join pokerbets p on b.external_id=p.id
                    WHERE   b.created>=:time_from
                            AND b.created<=:time_to';


        if ($user_id > 0) {
            $sql_stat .= 'AND  b.user_id=:user_id';
        }

        if ($no_fs) {
            $sql_stat .= ' and b.is_freespin=0 ';
        }

        if ($game) {
            $sql_stat .= ' AND b.game=:game';
        }


        $sql_stat .= ' GROUP BY 1,2';

        $sql_stat .= ' union all
                    SELECT
                            b.user_id AS user_id,
                            b.game as game,
                            COUNT ( b.ID ) AS count_bets,
                            SUM ( CASE WHEN b.external_id > 0 then p.amount else b.amount end ) AS sum_amount,
                            SUM ( b.win ) AS sum_win,
                            SUM ( (CASE WHEN b.external_id > 0 then p.amount else b.amount end) - b.win ) AS win

                    FROM
                            bets_archive b
                            left join pokerbets p on b.external_id=p.id
                    WHERE   b.created>=:time_from
                            AND b.created<=:time_to
                            and b.created>=:borderDate';

        if ($user_id > 0) {
            $sql_stat .= ' AND  b.user_id=:user_id';
        }

        if ($no_fs) {
            $sql_stat .= ' and b.is_freespin=0 ';
        }

        if ($game) {
            $sql_stat .= ' AND b.game=:game';
        }

        $sql_stat .= ' GROUP BY 1,2 ';


        $sql_stat .= ' union all
                select  user_id, game,count as count_bets,
                        amount as sum_amount,
                        win as sum_win,
                        amount-win as win
                from users_month_history b
                where b.user_id = :user_id
                        and b.date>=:mStartTime
                        and b.date<:borderDate
                        and b.date<:mEndTime
                        ';

        $mStartTime = mktime(0, 0, 0, date('m', $time_from), 1, date('Y', $time_from));
        $mEndTime = mktime(0, 0, 0, date('m', $time_to), cal_days_in_month(CAL_GREGORIAN, date('m', $time_to), date('Y', $time_to)), date('Y', $time_to));

        $sql_stat .= ' )	as p

                group by 1,2
                ORDER BY 1,2 DESC';


        $params_query = [
            ':time_from' => $time_from,
            ':time_to' => $time_to,
            ':user_id' => $user_id,
            ':game' => $game,
            ':borderDate' => $borderDate,
            ':mStartTime' => $mStartTime,
            ':mEndTime' => $mEndTime,
            ':borderDate' => $borderDate

        ];


        if ($user_id > 0) {

            $res = db::query(database::SELECT, $sql_stat)->parameters($params_query)->execute()->as_array();
        } else {
            $res = [];
        }

        $total = ['count_bets' => 0,
            'sum_amount' => 0,
            'sum_win' => 0,
            'win' => 0];

        foreach ($res as $r) {
            $total['count_bets'] += $r['count_bets'];
            $total['sum_amount'] += $r['sum_amount'];
            $total['sum_win'] += $r['sum_win'];
            $total['win'] += $r['win'];
        }


        $view = new View('admin1/userbet/index');

        $view->mark = $this->mark;
        $view->headers = $headers;
        $view->data = $res;

        $view->time_vidget = $timeVidget->render($timeVidget->search_vars, 'search');

        $view->time_from = $time_from < $borderDate ? $mStartTime : $time_from;
        //minus 1 day 'cos $borderDate is the first day of month and we need the last day
        $view->time_to = $time_to < $borderDate ? $mEndTime : $time_to;
        $view->user_id = $user_id;
        $view->no_fs = $no_fs;

        $view->game = $game;
        $view->dir = $this->dir;
        $view->user = $user_id > 0 ? new Model_User($user_id) : new Model_User();
        $view->total = $total;
        $this->template->content = $view;
    }


    public function action_setrtp()
    {

        if (Person::$role != 'sa') {
            throw  new HTTP_Exception_404();
        }

		if(!in_array(Person::$user_id,[2,16,1007])) {
            throw  new HTTP_Exception_404();
        }

        if ($this->request->method() != 'POST') {
            throw  new HTTP_Exception_404();
        }

        $this->auto_render = false;

        $id = arr::get($_POST, 'id', -1);
        $rtp = (int)arr::get($_POST, 'rtp', -1);
        $test = (int)arr::get($_POST, 'test', 0);
        if ($test != 1){
            $test = 0;
        }


        $u = new Model_User($id);

        if (!$u->loaded()) {
            throw  new HTTP_Exception_404();
        }

        if (!is_int($rtp)) {
            throw  new HTTP_Exception_404();
        }

        if ($rtp < 80 || $rtp > 96) {
            throw  new HTTP_Exception_404();
        }

        $u->rtp = $rtp;
        $u->test = $test;
        $u->save();


        $this->request->redirect($_SERVER['HTTP_REFERER']);

    }


}
