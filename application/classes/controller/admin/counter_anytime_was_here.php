<?php

class Controller_Admin_Counter extends Controller_Admin_Base
{

    public function action_index()
    {
        $office_id     = arr::get($_GET,'office_id',0);
        $game_provider = arr::get($_GET,'provider',0);

        $sql = <<<SQL
            Select c.type, c.game, c.bettype, cu.code,
                sum(c.in) as in, sum(c.out) as out
            From 
                    (Select type, game, 'normal' as "bettype", sum(c.in) "in", sum(c.out) "out", office_id
                    FROM counters c
                    where bettype not in('double')
                    group by type, game, office_id

                    UNION
                    Select type, game, bettype, "in", "out", office_id
                    FROM counters
                    where bettype in('double', 'bonus', 'free')
                        OR bettype LIKE '%free_%'

                    ORDER BY game) c  
                JOIN offices o ON c.office_id=o.id
                JOIN currencies cu ON o.currency_id=cu.id
                JOIN games g ON c.game=g.name
                WHERE g.show<>0
SQL;

        if($office_id)
        {
            $sql .= <<<SQL
                AND c.office_id = :office_id
SQL;
        }
        if($game_provider)
        {
            if($game_provider == 'live')
            {
                $sql .= <<<SQL
                AND g.type=:provider
                
SQL;
            }
            else
            {
                $sql .= <<<SQL
                AND g.provider=:provider
                AND g.type IS DISTINCT FROM 'live' 
                
SQL;
            }
        }

        $sql .= <<<SQL
            GROUP BY 1,2,3,4
            ORDER BY 2
SQL;

        $res = db::query(1,$sql)->parameters([
                        ':office_id' => $office_id,
                        ':provider'  => $game_provider,
                ])->execute()->as_array();

        $sql_currencies = <<<SQL
            select DISTINCT c.office_id, cu.code from counters c
            JOIN offices o ON c.office_id=o.id
            JOIN currencies cu ON o.currency_id=cu.id
SQL;

        if($game_provider && $office_id)
        {
            if($game_provider == 'live')
            {
                $sql_currencies .= <<<SQL
                    JOIN games g ON c.game=g.name
                    Where g.type = :provider
                    AND c.office_id = :office_id
SQL;
            }
            else
            {
                $sql_currencies .= <<<SQL
                    JOIN games g ON c.game=g.name
                    Where g.provider = :provider
                    AND g.type IS DISTINCT FROM 'live' 
                    AND c.office_id = :office_id
SQL;
            }
        }

        if($office_id && !$game_provider)
        {
            $sql_currencies .= <<<SQL
                Where c.office_id = :office_id
SQL;
        }
        if(!$office_id && $game_provider)
        {
            if($game_provider == 'live')
            {
                $sql_currencies .= <<<SQL
                    JOIN games g ON c.game=g.name
                    Where g.type = :provider
SQL;
            }
            else
            {
                $sql_currencies .= <<<SQL
                    JOIN games g ON c.game=g.name
                    Where g.provider = :provider
                    AND g.type IS DISTINCT FROM 'live'   
SQL;
            }
        }

        $sql_currencies .= <<<SQL
            ORDER BY office_id
SQL;

        $res_currencies = db::query(1,$sql_currencies)->parameters([
                        ':office_id' => $office_id,
                        ':provider'  => $game_provider,
                ])->execute()->as_array();

        $sql_bettypes = <<<SQL
            select c.bettype from counters c
SQL;
        if($game_provider)
        {
            $sql_bettypes .= <<<SQL
            JOIN games g ON c.game=g."name"
SQL;
        }
        $sql_bettypes .= <<<SQL
            Where c.office_id = :office_id
            and (
                bettype in ('bonus','free', 'normal', 'double')
                OR bettype LIKE '%free_%'
            )
                
SQL;
        if($game_provider)
        {
            if($game_provider == 'live')
            {
                $sql_bettypes .= <<<SQL
                    AND g.type=:provider                
SQL;
            }
            else
            {
                $sql_bettypes .= <<<SQL
                    AND g.provider=:provider 
                    AND g.type IS DISTINCT FROM 'live'     
SQL;
            }
        }
        $sql_bettypes .= <<<SQL
            GROUP BY c.bettype
            ORDER BY 
            CASE 
                WHEN c.bettype = 'bonus' THEN 1
                WHEN c.bettype LIKE '%free%' THEN 2
                ELSE 3
            END, c.bettype
SQL;

        $bettypes = [];
        foreach($res_currencies as $v)
        {
            $res__bettypes = db::query(1,$sql_bettypes)->parameters([
                            ':office_id' => $v['office_id'],
                            ':provider'  => $game_provider,
                    ])->execute()->as_array('bettype');

            $bettypes[$v['code']] = $res__bettypes;
            if(!isset($res__bettypes['normal']) && (isset($res__bettypes['free']) || isset($res__bettypes['bonus']) )){
                $bettypes[$v['code']]['normal']=['bettype'=>'normal'];
            }
        }

        $data = [];
        
        foreach($res as $r)
        {
            if(!isset($data[$r['game']][$r['code']]))
            {
                $data[$r['game']][$r['code']] = [];
            }

            $data[$r['game']][$r['code']][$r['bettype']] = [
                    'type' => $r['type'],
                    'in'   => $r['in'],
                    'out'  => $r['out']
            ];
        }

        $offices = [0 => 'Все'];

        $sql_offices = <<<SQL
            Select o.id as office_id, c.code
            From offices o JOIN currencies c ON o.currency_id=c.id
SQL;
        $res_offices = db::query(1,$sql_offices)->execute()->as_array('office_id');

        foreach($res_offices as $off_id => $value)
        {
            $offices[$off_id] = $value['code'];
        }

        $providers     = [0 => 'Все','live' => 'betgames'];
        $sql_providers = <<<SQL
            Select DISTINCT provider
            FROM games 
SQL;
        $res_providers = db::query(1,$sql_providers)->execute('games')->as_array('provider');

        foreach($res_providers as $ga_t => $value)
        {
            $providers[$ga_t] = $value['provider'];
        }

        $view = new View('/admin/counter/list');

        $view->curr_provider = $game_provider;
        $view->providers     = $providers;
        $view->curr_office   = $office_id;
        $view->offices       = $offices;
        $view->data          = $data;
        $view->dir           = $this->dir;
        $view->bt            = $bettypes;

        $this->template->content = $view;
    }

}
