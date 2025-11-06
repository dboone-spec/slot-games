<?php

class Controller_Promos extends Controller_Base{

	public function action_givevips2asdadsd() {

exit;

        $listpath = '/home/site/vips.csv';

        echo '<pre>';

        $parsed_data=[];

        foreach(file($listpath) as $row) {
            list($user_external,$sum)=explode(';',$row);

            $sum = preg_replace( '/\W+/', '', $sum);
            $sum = $sum/100;

            $sum = 0.1*$sum; //10%

            $user_external = '3000113:'.$user_external;

            $parsed_data[$user_external]=$sum;
        }

        $users = db::query(1,'select id,external_id from users where external_id in :ids')
                ->param(':ids',array_keys($parsed_data))
                ->execute()
                ->as_array('external_id');

        foreach($parsed_data as $external_id=>$sum) {

            if(!isset($users[$external_id])) {
                file_put_contents('errorvips','not found '.$external_id.PHP_EOL,FILE_APPEND);
                continue;
            }

            $u = new Model_User($users[$external_id]['id']);

            if(!$u->loaded()) {
                file_put_contents('errorvips','not found2 '.$external_id.PHP_EOL,FILE_APPEND);
                continue;
            }


            $res = $u->calc_fsback($sum,'hotpepper100',811);


            if($res) {

                list($lines,$dentab_index,$bet_index)=explode('-',$res['near']);

                echo 'USER: '.$external_id.'; lines: '.$lines.'; dentab_index: '.$dentab_index.'; bet_index: '.$bet_index.'   ';
                echo 'FS COUNT: '.floor($res['win']/$res['zzz']).' WITH BET AMOUNT: '.$res['zzz'];

                echo '<br>';

                $dentab = $dentab_index;

                $z = floor($res['win']/$res['zzz']);

                $expire=time()+30*24*60*60;

                $f = new Model_Freespin();
                $f->giveFreespins($u->id,$u->office_id,$res['game_id'],$z,$res['zzz'],$lines,$dentab,'api',true,['vip'],false,null,$expire);
            }
            else {
                file_put_contents('errorvips','trouble with '.$external_id.PHP_EOL,FILE_APPEND);
            }
        }

        echo 'ok';
        exit;
    }   
}

