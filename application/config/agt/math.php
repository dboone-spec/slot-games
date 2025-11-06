<?php

class Controller_Math extends Controller
{







    public function action_gameinfo()
    {

        $sql = "select name,visible_name,type
                from games
                where show=1
                and name not like '%1win%'
                order by name";

        $games = db::query(1, $sql)->execute()->as_array('name');


        $config = [];
        $clones = [];
        $checkSum = [];

        foreach ($games as $game) {

            $c = Kohana::$config->load('agt/' . $game['name']);
            $c = $c->as_array();
            $c['heigth'] = $c['heigth'] ?? 3;


            //vermeer hokusai
            if (isset($c['probability96'])) {
                $c['barsCount'] = $c['width'];
            } //others
            else {
                $c['bars'] = $c['bars'] ?? $c['bars96'] ?? [];
                if (is_array($c['bars'])) {
                    $c['barsCount'] = count($c['bars']);
                } else {
                    $c['barsCount'] = -1;
                }

            }


            if (isset($c['lines']) && is_array($c['lines'])) {
                $c['linesCount'] = count($c['lines']);
            } else {
                $c['linesCount'] = -1;
            }

            $c['wild_except'] = $c['wild_except'] ?? [];


            if (!isset ($c['pay'])) {
                continue;
            }
            if (!isset ($c['pay'][0])) {
                continue;
            }

            if (!isset ($c['wild'])) {
                $c['wild'] = [];
            }

            if (!isset ($c['anypay'])) {
                $c['anypay'] = [];
            }


            foreach ($c['pay'] as $num => $pay) {


                $mark = [];
                if (in_array($num, $c['wild'])) {
                    $mark[] = 'wild - substitutes for all symbols except scatter';
                }

                if (in_array($num, $c['anypay'])) {
                    $mark[] = 'scatter';
                }

                if (in_array($num, $c['wild_except']) && !in_array($num, $c['anypay'])) {
                    $mark[] = 'wild except';
                }
                $c['mark'][$num] = implode(', ', $mark);

                if (in_array($num, $c['replace_bar'] ?? [])) {
                    $mark[] = 'substitutes for all on the same reel';
                }
                $c['mark'][$num] = implode(', ', $mark);

            }




            $check = 0;
            foreach ($c['pay'] as $pay) {
                $check += array_sum($pay) / $c['heigth'];
            }


            $cl = array_keys($checkSum, $check);
            if (count($cl) > 0 && $game['name'] != 'vangogh') {
                $clones[$game['name']] = $c;
                $clones[$game['name']]['cloneOf'] = $cl[0];
            } else {
                $checkSum[$game['name']] = $check;
                $config[$game['name']] = $c;
            }


        }

        //full view
        //$view=new View('test/gameinfo');

        //short view
        //$view=new View('test/gameinfoshort');

        //с текстом из игры
        $view = new View('test/gameinfogametext');


        //другие игры
        $configOthers = [];
        $configOthers['poker']['acesandfaces'] =  Kohana::$config->load('agt/acesandfaces');
        $configOthers['poker']['jacksorbetter'] =  Kohana::$config->load('agt/jacksorbetter');
        $configOthers['poker']['tensorbetter'] =  Kohana::$config->load('agt/tensorbetter')->as_array();
        $configOthers['poker']['tensorbetter']['level']=[ 10=>'Royal Flush',
            9=>'Straight Flush',
            8=>'Four of a Kind',
            7=>'Full House',
            6=>'Flush',
            5=>'Straight',
            4=>'Three of a Kind',
            3=>'Two pairs',
            2=>'Tens or Better',
            1=>'Hi card',
            0=>'No',
        ];

        $configOthers['keno'] = Kohana::$config->load('keno/keno');


        $viewOthers = new View('test/others');
        $viewOthers->config = $configOthers;


        $view->config = $config;
        $view->games = $games;
        $view->clones = $clones;
        $view->allGames = array_merge($config,$clones);
        //$this->response->body($view);
        $this->response->body($view.$viewOthers);


    }

    public function action_listofgames()
    {

        $sql = 'select name,visible_name,rtp
                from games
                where show=1
                order by visible_name';

        $games = db::query(1, $sql)->execute()->as_array('name');

        $config = [];
        $clones = [];
        $checkSum = [];

        foreach ($games as $game) {
            $c = Kohana::$config->load('agt/' . $game['name']);
            $c = $c->as_array();

            $c['images'] = [];
            $dir = DOCROOT . '..' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'screen' . DIRECTORY_SEPARATOR . $game['name'];


            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {

                    if (in_array($file, ['.', '..'])) {
                        continue;
                    }

                    $c['images'][] = $file;

                }
            }


            if (in_array($game['name'], ['acesandfaces', 'jacksorbetter', 'tensorbetter', 'keno'])) {

                $c['rtp'] = '97.54% - 99.54%';

                $max = 0;
                foreach ($c['pay'] as $num => $pay) {
                    foreach ($pay as $pay1) {
                        if ($max < $pay1) {
                            $max = $pay1;
                        }
                    }
                }

                $c['maxWin'] = $max;
                if ($game['name'] == 'keno') {
                    $c['rtp'] = '95.55% - 98.00%';
                    $c['maxWin'] = 15000;
                }
                $c['FSback'] = 'No';
                $c['FG'] = 'No';
                unset($c['pay']);

                $config[$game['name']] = $c;
                continue;
            }


            $c['FSback'] = 'Yes';
            $c['heigth'] = $c['heigth'] ?? 3;

            $c['rtp'] = str_replace(',', '.', $game['rtp']);
            $c['rtp'] = round(((float)$c['rtp']) * 100, 2) . '%';


            if (isset($c['lines_choose'])) {
                $c['linesCh'] = $c['lines_choose'];
                sort($c['linesCh']);
                $c['linesCh'] = implode(', ', $c['linesCh']);
            } else {
                $c['linesCh'] = '';
            }


            if (isset($c['bars']) && is_array($c['bars'])) {
                $c['barsCount'] = count($c['bars']);
            } elseif (isset($c['bars96']) && is_array($c['bars96'])) {
                $c['barsCount'] = count($c['bars96']);
            } else {
                $c['barsCount'] = -1;
            }

            if (isset($c['lines']) && is_array($c['lines'])) {
                $c['linesCount'] = count($c['lines']);
            } else {
                $c['linesCount'] = -1;
            }

            $c['wild_except'] = $c['wild_except'] ?? [];
            $c['info'] = [];


            //set default values;
            $c['wild'] = $c['wild'] ?? [];
            $c['wild_multiplier'] = $c['wild_multiplier'] ?? 1;
            $c['anypay'] = $c['anypay'] ?? [];

            if (count($c['wild']) > 0) {
                $c['info'][] = 'Wild';
            }

            if (count($c['anypay']) > 0) {
                $c['info'][] = 'Scatter';
            }


            $max = 0;


            foreach ($c['pay'] as $num => $pay) {


                foreach ($pay as $pay1) {
                    if (!in_array($num, $c['wild'])) {
                        if (is_array($pay1)) {
                            $pay1 = $pay1;
                        } else {
                            $pay1 *= $c['wild_multiplier'];
                        }
                    }
                    if ($max < $pay1) {
                        $max = $pay1;
                    }
                }

                $mark = [];
                if (in_array($num, $c['wild'])) {
                    $mark[] = 'wild';
                }

                if (in_array($num, $c['anypay'])) {
                    $mark[] = 'scatter';
                }

                if (in_array($num, $c['wild_except'])) {
                    $mark[] = 'wild except';
                }
                $c['mark'][$num] = implode(', ', $mark);

                if (in_array($num, $c['replace_bar'] ?? [])) {
                    $mark[] = 'substitutes for all on the same reel';
                }
                $c['mark'][$num] = implode(', ', $mark);

            }


            $c['FG'] = isset($c['barFree']) ? 'Yes' : 'No';

            if ($c['FG'] == 'Yes') {
                $max *= $c['free_multiplier'] ?? 1;
            }

            $c['maxWin'] = $max;

            if (in_array($game['name'], ['wildwest', 'alladin'])) {
                unset($c['pay'][12]);
                unset($c['pay'][13]);
                unset($c['pay'][14]);

            }


            $config[$game['name']] = $c;

        }


        $view = new View('test/listofgames');
        $view->config = $config;
        $view->games = $games;
        $view->clones = [];
        $this->response->body($view);


    }


    public function action_csv()
    {

        $sql = 'select name,visible_name,rtp
                from games
                where show=1
                order by visible_name';

        $games = db::query(1, $sql)->execute()->as_array('name');

        $config = [];
        $clones = [];
        $checkSum = [];

        $csv = [];

        foreach ($games as $game) {

            $row = [];
            $c = Kohana::$config->load('agt/' . $game['name']);
            $c = $c->as_array();

            $row['name'] = $game['visible_name'];
            $row['provider'] = 'site-domain';
            $row['id'] = $game['name'];
            $row['rtp'] = str_replace(',', '.', $game['rtp']);
            $row['rtp'] = round(((float)$row['rtp']) * 100, 2) . '%';
            $row['type'] = 'slot';
            $row['jackpot'] = 'Yes';
            $row['lang'] = 'English, German, French,Turkish, Russian';

            //Reels	Lines	Freespins (Yes/No)	Jackpot (Yes/No)			Other features


            $c['images'] = [];
            $dir = DOCROOT . '..' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'screen' . DIRECTORY_SEPARATOR . $game['name'];


            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {

                    if (in_array($file, ['.', '..'])) {
                        continue;
                    }

                    $c['images'][] = $file;

                }
            }


            if (in_array($game['name'], ['acesandfaces', 'jacksorbetter', 'tensorbetter', 'keno'])) {

                $c['rtp'] = '97.54% - 99.54%';

                $max = 0;
                foreach ($c['pay'] as $num => $pay) {
                    foreach ($pay as $pay1) {
                        if ($max < $pay1) {
                            $max = $pay1;
                        }
                    }
                }

                $c['maxWin'] = $max;
                if ($game['name'] == 'keno') {
                    $c['rtp'] = '95.55% - 98.00%';
                    $c['maxWin'] = 15000;
                }
                $c['FSback'] = 'No';
                $c['FG'] = 'No';
                unset($c['pay']);

                $row['type'] = 'table';
                $row['rtp'] = $c['rtp'];
                $row['lines'] = '';
                $row['reels'] = '';
                $row['info'] = '';
                $row['FS'] = '';
                $csv[] = $row;
                continue;
            }


            $c['FSback'] = 'Yes';
            $c['heigth'] = $c['heigth'] ?? 3;

            $c['rtp'] = str_replace(',', '.', $game['rtp']);
            $c['rtp'] = round(((float)$c['rtp']) * 100, 2) . '%';


            if (isset($c['lines_choose'])) {
                $c['linesCh'] = $c['lines_choose'];
                sort($c['linesCh']);
                $c['linesCh'] = implode(', ', $c['linesCh']);
            } else {
                $c['linesCh'] = '';
            }

            $row['lines'] = $c['linesCh'];


            if (isset($c['bars']) && is_array($c['bars'])) {
                $c['barsCount'] = count($c['bars']);
            } elseif (isset($c['bars96']) && is_array($c['bars96'])) {
                $c['barsCount'] = count($c['bars96']);
            } else {
                $c['barsCount'] = -1;
            }

            $row['reels'] = $c['barsCount'];


            if (isset($c['lines']) && is_array($c['lines'])) {
                $c['linesCount'] = count($c['lines']);
            } else {
                $c['linesCount'] = -1;
            }

            $c['wild_except'] = $c['wild_except'] ?? [];
            $c['info'] = [];


            if (count($c['wild']) > 0) {
                $c['info'][] = 'Wild';
            }

            if (count($c['anypay']) > 0) {
                $c['info'][] = 'Scatter';
            }

            $row['info'] = $c['info'];


            $max = 0;
            foreach ($c['pay'] as $num => $pay) {


                foreach ($pay as $pay1) {
                    if (!in_array($num, $c['wild'])) {
                        $pay1 *= $c['wild_multiplier'];
                    }
                    if ($max < $pay1) {
                        $max = $pay1;
                    }
                }

                $mark = [];
                if (in_array($num, $c['wild'])) {
                    $mark[] = 'wild';
                }

                if (in_array($num, $c['anypay'])) {
                    $mark[] = 'scatter';
                }

                if (in_array($num, $c['wild_except'])) {
                    $mark[] = 'wild except';
                }
                $c['mark'][$num] = implode(', ', $mark);

                if (in_array($num, $c['replace_bar'] ?? [])) {
                    $mark[] = 'substitutes for all on the same reel';
                }
                $c['mark'][$num] = implode(', ', $mark);

            }


            $c['FG'] = isset($c['barFree']) ? 'Yes' : 'No';

            if ($c['FG'] == 'Yes') {
                $max *= $c['free_multiplier'] ?? 1;
            }

            $row['FS'] = $c['FG'];

            $c['maxWin'] = $max;

            if (in_array($game['name'], ['wildwest', 'alladin'])) {
                unset($c['pay'][12]);
                unset($c['pay'][13]);
                unset($c['pay'][14]);

            }


            $csv[] = $row;

        }

        $output = '';
        foreach ($csv as $r) {

            if (is_array($r['info'])) {
                $r['info'] = implode(', ', $r['info']);
            }
            $output .= "{$r['name']};{$r['provider']};{$r['id']};{$r['reels']};{$r['lines']};{$r['rtp']};{$r['FS']};{$r['jackpot']};;{$r['type']};{$r['info']};{$r['lang']}\r\n";
        }


        $this->response->body($output);


    }


    public function action_card()
    {

        for ($i = 1; $i <= 52; $i++) {
            echo "$i " . card::print_card($i) . "<br>";
        }
    }



}
