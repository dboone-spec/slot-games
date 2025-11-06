<?php

class Model_Freespin extends ORM {

    protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_serialize_columns = array('gameids');
    protected $_table_columns = array(
            'id' =>
            array(
                    'type' => 'int',
                    'min' => '-9223372036854775808',
                    'max' => '9223372036854775807',
                    'column_name' => 'id',
                    'column_default' => 'nextval(\'freespins_id_seq\'::regclass)',
                    'is_nullable' => false,
                    'data_type' => 'bigint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '64',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'user_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'user_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'game_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'game_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'lines' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'lines',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'amount' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'amount',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '12',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
            'fs_count' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_count',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'fs_played' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_played',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'src' =>
            array(
                    'type' => 'string',
                    'column_name' => 'src',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '8',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'active' =>
            array(
                    'type' => 'int',
                    'min' => '-32768',
                    'max' => '32767',
                    'column_name' => 'active',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'smallint',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '16',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'created' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'created',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'event_end_time' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'event_end_time',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'office_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'office_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'updated' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'updated',
                    'column_default' => '0',
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'starttime' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'starttime',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'bettime' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'bettime',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'expirtime' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'expirtime',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'uuid' =>
            array(
                    'type' => 'string',
                    'column_name' => 'uuid',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '46',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'fs_offer_type' =>
            array(
                    'type' => 'string',
                    'column_name' => 'fs_offer_type',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '46',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'gameids' =>
            array(
                    'type' => 'string',
                    'column_name' => 'gameids',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'character varying',
                    'character_maximum_length' => '0',
                    'numeric_precision' => NULL,
                    'numeric_scale' => NULL,
                    'datetime_precision' => NULL,
            ),
            'fs_offer_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'fs_offer_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'event_id' =>
            array(
                    'type' => 'int',
                    'min' => '-2147483648',
                    'max' => '2147483647',
                    'column_name' => 'event_id',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'integer',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '32',
                    'numeric_scale' => '0',
                    'datetime_precision' => NULL,
            ),
            'sum_win' =>
            array(
                    'type' => 'float',
                    'exact' => true,
                    'column_name' => 'sum_win',
                    'column_default' => NULL,
                    'is_nullable' => true,
                    'data_type' => 'numeric',
                    'character_maximum_length' => NULL,
                    'numeric_precision' => '12',
                    'numeric_scale' => '2',
                    'datetime_precision' => NULL,
            ),
    );
    protected $_belongs_to = [
		'user' => [
			'model'		 => 'user',
			'foreign_key'	 => 'user_id',
		],
		'game' => [
			'model'		 => 'game',
			'foreign_key'	 => 'game_id',
		],
	];

    public function labels()
    {
        return [
                'user_id'=>'User Id',
                'fs_count'=>'FS count',
                'fs_played'=>'FS played',
                'game_id'=>'Game',
                'external_id'=>'Partner User Id'
        ];
    }

    public function giveFreespins($user_id,$office_id,$game_id,int $count,float $amount,int $lines,int $dentab_index=-1, $src = 'cashback', $force_replace=true, $log=[],$started=false, $src_type=null, $expirtime=null,$fs_uuid=null,$start_time=null) {

        if(empty($expirtime)) {
            $expirtime=time()+60*60*24*30;
        }

        //раньше все перетиралось. сейчас update не выполняется вообще
        $override=false;

        if((int) $count<=0) {
            return false;
        }

        if((int) $count>=300) {
            return false;
        }

        $o = new Model_Office($office_id);

        $c = $o->get_k_list();

        if($dentab_index>=0 && !isset($c[$dentab_index])) {
            return false;
        }

        if(!is_array($game_id)) {
            $game_id=[$game_id];
        }

        $games=db::query(1,'select id,name,type from games where id in :g_ids')
            ->param(':g_ids',$game_id)
            ->execute()
            ->as_array('id');

        foreach($game_id as $gid) {
            if(!isset($games[$gid])) {
                return false;
            }

            if($src=='api' && $games[$gid]['type']=='moon') {
                return false;
            }

            if (!in_array($games[$gid]['type'], ['slot', 'moon', 'shuffle'])) {
                return false;
            }

            $gc = Kohana::$config->load('agt/' . $games[$gid]['name']);

            if($lines==0) {
                $lines=$gc['lines_choose'][0];
            }

            if (!in_array($lines, $gc['lines_choose'])) {
                $lines=$gc['lines_choose'][0];
//                return false;
            }
        }

        $sql = "fs_count=$count,fs_played=0";
        if(!$force_replace) {
            $sql = "fs_count=fs_count+$count";
        }

        $time=time();
        $started_time='null';
        if($started) {
            $started_time=$time;
        }

        if(!empty($start_time)) {
            $started_time=$start_time;
        }

        $gameids=json_encode($game_id);
        //очень грубо. обнуляем если есть текущие фриспины
        $update = "update freespins set $sql,game_id=$game_id[0],amount=$amount,lines=$lines,src='$src',
                   active=0,created=$time,starttime=$started_time,gameids=$gameids 
                    where user_id=$user_id"
                . " returning id";

        $fs_offer_id=$this->fs_offer_id ?? 'null';

        $fs_offer_type=$this->fs_offer_type;

        $insert = "insert into freespins(user_id,office_id,game_id,fs_count,lines,amount,src,created,starttime,expirtime,uuid,fs_offer_id,fs_offer_type,gameids) 
                values($user_id,$office_id,$game_id[0],$count,$lines,$amount,'$src',$time,$started_time,$expirtime,'$fs_uuid',$fs_offer_id,'$fs_offer_type','$gameids') returning id";

        $log = json_encode($log);

        $r=[];
        if($override) {
            $r = Database::instance()->direct_query($update);

            $id = arr::get(current($r),'id',0);

        }
        if(empty($r)) {
            $i = Database::instance()->direct_query($insert);
            $id = arr::get(current($i),'id',0);
        }

        $event_id=$this->event_id ?? 'null';
        $event_end_time=$this->event_end_time ?? 'null';

        $insert_history = "insert into freespins_history(user_id,office_id,game_id,fs_count,lines,amount,src,created,log,freespin_id,type,expirtime,fs_offer_id,fs_offer_type,event_id,event_end_time,gameids) "
                . "values($user_id,$office_id,$game_id[0],$count,$lines,$amount,'$src',$time,'$log',$id,'$src_type',$expirtime,$fs_offer_id,'$fs_offer_type',$event_id,$event_end_time,'$gameids')";
        Database::instance()->direct_query($insert_history);

        return $id;
    }

    public function declineFreespins($fs_id,$auto=false) {
        $delete = "delete from freespins where id=$fs_id RETURNING id";
        $r = Database::instance()->direct_query($delete);
        $status=$auto?'-2':'-1';
        if(count($r)) {
            $id = $r[0]['id'];
            $expirtime=time();
            Database::instance()->direct_query("update freespins_history set active=$status, expirtime=$expirtime where freespin_id=$id");
        }
    }

    public function activateFreespins($fs_id) {
        $update = "update freespins set active=1 where id=$fs_id returning id";
        $r = Database::instance()->direct_query($update);
        if(count($r)) {
            $id = $r[0]['id'];
            $expirtime=time()+20*60;
            Database::instance()->direct_query("update freespins_history set active=1, expirtime=$expirtime where freespin_id=$id");
        }
    }

    public $_fs_played=0;

    public function spinOneFreespin($fs_id,$win) {

        $time=time();


        $update = "update freespins set fs_played=fs_played+1,sum_win=sum_win+$win,bettime=$time where id=$fs_id returning fs_count-fs_played as fs_count,fs_played, id";
        $delete = "delete from freespins where id=$fs_id";

        $r = Database::instance()->direct_query($update);

        if(!empty($r)) {
            $id = $r[0]['id'];
            $this->_fs_played = $r[0]['fs_played'];
            Database::instance()->direct_query("update freespins_history set fs_played=fs_played+1,sum_win=sum_win+$win where freespin_id=$id");
        }

        if(!empty($r) && (int) $r[0]['fs_count']==0) {
            Database::instance()->direct_query($delete);
            return false;
        }
        return true;
    }

    public function updateSum($fs_id,$win) {
        Database::instance()->direct_query("update freespins set sum_win=sum_win+$win where id=$fs_id");
        Database::instance()->direct_query("update freespins_history set sum_win=sum_win+$win where freespin_id=$fs_id");
    }

    public function updateAnonym($o_id) {
        $fs_id=$this->id;
        Database::instance()->direct_query("update freespins set office_id=$o_id where id=$fs_id");
        Database::instance()->direct_query("update freespins_history set office_id=$o_id where freespin_id=$fs_id");
    }

    public function getTypeName() {
        if($this->src=='cashback') {
            return 'DS';
        }

        if($this->src=='lucky') {
            return 'LS';
        }

        if($this->src=='moon') {
            return '+1BET';
        }

        return 'FSAPI';
    }

    public function games() {
        if(empty($this->gameids)) {
            return;
        }
        $sqlgames=db::query(1,'select name,visible_name from games where id in :ids')
            ->param(':ids',$this->gameids)
            ->execute()->as_array();
        return $sqlgames;
    }
}

