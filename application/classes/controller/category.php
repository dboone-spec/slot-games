<?php

    class Controller_Category extends Controller_Base
    {

        protected $games = null;
        protected $show_search = true;

        public function before()
        {
            parent::before();

            $this->brand = strtolower($this->request->action());

            $this->request->action('index');
        }

        public function action_index()
        {

            $view = new View('site/select/index');
            
            $all_games = Kohana::$config->load('games');

            $g = orm::factory('game')->where('show', '>', 0)->find_all();

            $cat = [];
            foreach ($g as $v)
            {
                if (!in_array($v->brand, $cat))
                {
                    $cat[] = $v->brand;
                }
            }

            $games = [];
            foreach ($all_games as $brand => $value)
            {
                if ($this->brand == $brand)
                {
                    $games[$brand] = $value;
                }
            }

            //признак включенного апи империум для игрока
            //было auth::$user_id AND auth::user()->api_imperium == 1
            if (true)
            {
                $sql_games = <<<SQL
                Select g.*
                From games g JOIN office_games og ON g.id = og.game_id
            Where office_id = :office_id
                AND g.show in :show
                AND og.enable = 1
                and g.show>0
SQL;

                $show = [1, 3];
                $tech_type = ['h', 'f', 'fh'];
                if (th::isMobile())
                {
                    $show = [1, 2];
                    if (OFFLINE)
                    {
                        $tech_type = ['h', 'fh'];
                        $sql_games .= ' and g.tech_type in :tech_type ';
                    }
                }
                
                $sql_games.=' and brand = :games_brand';

                $res_games = db::query(1, $sql_games)->parameters([
                            ':office_id' => OFFICE,
                            ':show' => $show,
                            ':tech_type' => $tech_type,
                            ':games_brand' => $this->brand,
                        ])->execute('games')->as_array();

                $static_domain = kohana::$config->load('static.static_domain');
                $imperium_games = [];
                foreach ($res_games as $g)
                {

                    if ($static_domain)
                    {
                        $g['image'] = $static_domain . $g['image'];
                    }

                    if ($g['provider'] == 'our')
                    {
                        $games[$g['brand']][$g['name']] = [
                            'image' => $g['image'],
                            'visible_name' => $g['visible_name'],
                        ];
                    }
                    else
                    {
                        $imperium_games[UTF8::strtolower($g['brand'])][] = [
                            'game_id' => $g['external_id'],
                            'name' => $g['visible_name'],
                            'image' => $g['image'],
                            'type_system' => UTF8::strtolower($g['brand'])
                        ];
                    }
                }
                $view->imperium_games = $imperium_games;
            }

            //Процент возврата для сортировки
            $retpay = th::retpay();
            $maxp = th::maxpayin();
            $view->retpay = $retpay;
            $view->mp = $maxp;

            //Избранное для сортировки
            $fg = th::favegame();
            if (!empty($fg))
            {
                $decfg = json_decode($fg[0]['games'], true);
                $view->fgames = $decfg;
            }
            //
            $a = new Model_Userfavourite();
            $a->emptygamestmp();
            ////

            $new_games = Kohana::$config->load('newgames')->as_array();
            $view->new_games = $new_games;
            $percent_return = Kohana::$config->load('percentreturn')->as_array();
            $view->percent_return = $percent_return;
            $view->games_search_pop = $games;
            
            $view->brand = $this->brand;
            $view->cats = $cat;
            $view->games = $games;
            $this->template->cats = $cat;
            $this->template->content = $view;
        }

    }
    