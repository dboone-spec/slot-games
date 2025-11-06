<?php

class Controller_Admin_Sharestats extends Controller_Admin_Base
{

    public $mark       = 'Статистика акций'; //имя
    public $model_name = 'share'; //имя модели
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_index()
    {
        $sql_newsletters = <<<SQL
            SELECT
                    COUNT (ID) AS ALL,
                    COUNT (CASE WHEN sended > 0 THEN ID END) AS sended,
                    COUNT (CASE WHEN opened > 0 THEN ID END) AS opened,
                    msrc,
                    title,

                    (
                            SELECT
                                    COUNT (DISTINCT(ip))
                            FROM
                                    follows
                            WHERE
                                    follows.msrc = newsletters.msrc
                    ) AS follows,
            (
                            SELECT
                                    'Регистраций: '||COUNT(DISTINCT(id))||'; Пополнений: '||sum(sum_in)||'; Выводов: '||sum(sum_out)
                            FROM
                                    users
                            WHERE
                                    users.msrc = newsletters.msrc
                                    and parent_id is not null
                    ) AS info
            FROM
                    newsletters
            WHERE
                    title NOT IN (
                            'Подтверждение эл. почты',''
                    )
            GROUP BY
                    msrc,
                title
            HAVING
                    COUNT (ID) > 500
            order by max(id) desc
SQL;

        $headers=[
            'title'=>__('Название'),
            'msrc'=>__('Метка'),
            'all'=>__('Всего на отправку'),
            'sended'=>__('Отправлено писем'),
            'opened'=>__('Открыто писем'),
            'follows'=>__('Уник. переходов'),
            'info'=>__('Инфо'),
        ];

        $prepare = db::query(1, $sql_newsletters)->execute()->as_array();
        $data=[];
        foreach($prepare as $row) {
            if(!isset($data[$row['msrc']])) {
                $data[$row['msrc']] = $row;
                continue;
            }

            $data[$row['msrc']]['all'] += $row['all'];
            $data[$row['msrc']]['sended'] += $row['sended'];
            $data[$row['msrc']]['opened'] += $row['opened'];
            $data[$row['msrc']]['title'] .= '; '.$row['title'];
            $data[$row['msrc']]['follows'] += $row['follows'];
        }

        $view = new View('admin/sharestats/index');
        $view->dir = $this->dir;
        $view->mark = $this->mark;
        $view->data = $data;
        $view->headers = $headers;
        $this->template->content = $view;
    }
}
