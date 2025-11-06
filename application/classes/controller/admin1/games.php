<?php

class Controller_Admin1_Games extends Controller_Admin1_Base
{

    public function action_table()
    {

        if (person::$role !== 'sa') {
            throw new HTTP_Exception_403();
        }

        $owners_colors=[
            1023=>'red',
            1042=>'green',
        ];

        $sql='select  g.id, g.visible_name, g.brand
        from games g
        where
            g.show=1 and g.brand=\'agt\' ';

        $games=db::query(1,$sql)
            ->execute()
            ->as_array('id');

        $sqlo='select og.game_id,o.owner,o.id,o.visible_name,og.enable from office_games og join offices o on o.id=og.office_id order by 2,3';

        $offices=db::query(1,$sqlo)->execute()->as_array('id');

        $officegames=[];
        foreach($offices as $row) {
            if(!isset($officegames[$row['id']])) {
                $officegames[$row['id']]=[$row['game_id']];
            }
        }

        $view=new View('admin1/games/table');
        $view->officegames=$officegames;
        $view->officelist=array_unique(array_column($offices, 'visible_name','id'));

        $view->games=$games;
        $view->colors=$owners_colors;

        $this->template->content=$view;

    }


    public function action_create()
    {

        if(person::$role!=='sa') {
            throw new HTTP_Exception_403();
        }

        $fields=[
            'name',
            'visible_name',
            'provider',
            'type',
            'brand',
            'image',
            'show',
            'tech_type',
            'sort',
            'category',
            'demo',
            'mobile',
            'text',
            'infin_show',
            'evenbet_show',
            'softg_show',
            'showpromo',
        ];

        $current_types=array_keys(db::query(1,'select distinct type from games where brand=\'agt\'')->execute()->as_array('type'));
        $current_cats=array_keys(db::query(1,'select distinct category from games where brand=\'agt\'')->execute()->as_array('category'));
        $min_sort=key(db::query(1,'select min(sort) from games where sort is not null and brand=\'agt\'')->execute()->as_array('min'));

        $defaults=[
            'brand'=>'agt',
            'tech_type'=>'h',
            'provider'=>'our',
            'show'=>1,
            'demo'=>0,
            'mobile'=>1,
            'infin_show'=>1,
            'evenbet_show'=>1,
            'softg_show'=>1,
            'category'=>implode(' ',$current_cats),
            'sort'=>$min_sort,
            'type'=>implode(' ',$current_types),
            'image'=>'/games/agt/thumb/{name}.png',
            'showpromo'=>1,
        ];

        $errors=[];

        $gamepath = '/var/www/agt/www/';
        $gamepath = DOCROOT;

        if($this->request->method()=='POST') {

            $newname=arr::get($_POST,'name');
            $new_visiblename=arr::get($_POST,'visible_name');

            $game=new Model_Game(['name'=>$newname]);

            if(!file_exists($gamepath . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . $newname . '.js')) {
                $errors[]='JS config not found';
            }

            if(!defined('TESTADMIN')) {

                if(!file_exists($gamepath . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'sqthumb' . DIRECTORY_SEPARATOR . $newname . '.png') ||
                    !file_exists($gamepath . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'sqthumb' . DIRECTORY_SEPARATOR . $newname . '.webp')) {
                    $errors[]='SQthumb not found';
                }

                if(!is_dir($gamepath . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . $newname)) {
                    $errors[]='audio not found';
                }

                if(!file_exists($gamepath . 'files' . DIRECTORY_SEPARATOR . 'promo' . DIRECTORY_SEPARATOR . $new_visiblename.'.zip')) {
                    $errors[]='promo not found';
                }
            }

            if(!is_dir($gamepath . 'games' . DIRECTORY_SEPARATOR . 'agt' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR. 'games' . DIRECTORY_SEPARATOR . $newname)) {
                $errors[]='images not found';
            }

            if($game->loaded()) {
                $errors[]='game name not unique';
            }

            if(!count($errors)) {

                foreach($fields as $f) {
                    $game->$f=arr::get($_POST,$f);
                }

                $game->save()->reload();

                $this->request->redirect('/enter/games?game_id='.$game->id);
            }

            $defaults=$_POST;
        }

        $view=new View('admin1/games/create');
        $view->fields=$fields;
        $view->defaults=$defaults;
        $view->errors=$errors;

        $this->template->content=$view;
    }


public function action_index()
{

    if(person::$role!=='sa') {
//        throw new HTTP_Exception_403();
    }

    $office_id = (int) arr::get($_GET,'office_id',-1);
    $game_id = (int) arr::get($_GET,'game_id',-1);

    if($game_id>0) {
        $office_id=-1;
    }

    if ($office_id!=-1 and !in_array($office_id, Person::user()->offices()) ){
        throw new HTTP_Exception_403();
    }

    if ($this->request->method()=='POST'){



        $games=arr::get($_POST,'g',[]);

        $values=[];

        $sql='update office_games
            set enable=0 ';

        $updated_type='bad';
        $updated_id=null;

        if($game_id>0) {
            $updated_type='game';
            $updated_id=$game_id;
            $sql.='where game_id=:game_id';
        }
        elseif($office_id>0) {
            $updated_type='office';
            $updated_id=$office_id;
            $sql.='where office_id=:oid';
        }
        else {
            exit;
        }

        db::query(Database::UPDATE,$sql)
                ->param(':oid',$office_id)
                ->param(':game_id',$game_id)
                ->execute();

        if(count($games)>0) {
            foreach($games as $g) {
                if($game_id>0){
                    $values[]="($game_id,$g,1)";
                }
                else {
                    $values[]="($g,$office_id,1)";
                }
            }

            $sql = 'insert into office_games(game_id,office_id,enable) values '.implode(',',$values).' on conflict(game_id,office_id) do update set enable=1';

            db::query(Database::INSERT,$sql)->execute();

            $this->query_log_changes(
                "office_games",
                [
                    'updated_type' => $updated_type,
                    'updated_id' => $updated_id,
                    'before' => '',
                    'after' => $games,
                    'created' => time(),
                ]
            );
        }

    }


    $sql='select  g.id, g.visible_name, g.brand
        from games g
        where
            g.show=1 ';


    $sql.=" and g.brand='agt' ";

    $allowed_games=[];

    $disabled_games=[];

    if(in_array(Person::$user_id,[1023])) {
        $sql.=" and g.branded=0 or (g.branded=1 and g.name in :allowed_games) ";
        $allowed_games=['betsafelkl','supabets','betfrednifty','betfredbonanza','betssonlkl'];

        //$sql.=" and (g.name not in :disabled_games) ";
        $disabled_games=[];
    }

    $brands=['agt'=>'AGT'];

    $sql.=' order by g.brand, g.visible_name    ' ;

    $allgames=db::query(1, $sql)
        ->param(':allowed_games',$allowed_games)
        ->param(':disabled_games',$disabled_games)
        ->execute()->as_array();

    $sql='select  o.game_id, o.enable, o.office_id
        from office_games o
        where o.office_id=:oid or o.game_id=:game_id';

    $games=db::query(1, $sql)->param(':oid',$office_id)->param(':game_id',$game_id)->execute()->as_array($office_id>0?'game_id':'office_id');


    if($office_id<=0 && $game_id<0) {
//        $allgames=[];
    }

    $owners_list=[];
    $owner_offices=[];
    $office_owners=[];

    if($game_id>0) {
        $owners_sql = db::query(1,'select p.id,p.comment from persons p where comment is not null and comment !=\'\'')->execute()->as_array('id');
        $owner_offices_sql = 'select id,owner from offices where owner is not null';

        $offices = person::user()->offices()+person::user()->offices(true);
        $owner_offices_sql.=' and id in :offices';

        $owner_offices_sql=db::query(1,$owner_offices_sql)->param(':offices',$offices)->execute()->as_array('id');

        foreach($owners_sql as $s) {
            $owners_list[$s['id']]=$s['comment'];
        }

        foreach($owner_offices_sql as $s) {
            $owner_offices[$s['id']]=$s['owner'];
            $office_owners[$s['owner']][]=$s['id'];
        }
    }

    $view=new View('admin1/games/index');
    $view->officesList= [-1=>'Select office']+Person::user()->officesName(null,true);
    $view->ownerList= [-1=>'Select owner']+$owners_list;
    $view->office_id=$office_id;
    $view->game_id=$game_id;
    $view->allgames=$allgames;
    $view->games=$games;
    $view->brands=$brands;
    $view->fullgameslist=array_combine(arr::pluck($allgames,'id'),arr::pluck($allgames,'visible_name'));

    $view->owners_list=$owners_list;
    $view->owner_offices=$owner_offices;

    $this->template->content=$view;

}



}
