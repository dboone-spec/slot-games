<?php

//логика игры

class Game_Slot_Fsgames
{

    protected $_name = 'fromdb';
    protected $_def_ans = [];
    protected $_converted_ans = [];

    public function converted_ans()
    {
        return $this->_converted_ans;
    }

    public function __construct($game,$ans)
    {
        $this->_def_ans = $ans;
        $this->_game = $game;
        $gm = new Model_Game(['name'=>$game]);
        $this->_name = $gm->brand;
        $this->_config_defaults = Kohana::$config->load($this->_name);
        $this->_config          = Kohana::$config->load($this->_name.'/' . $game);

        if(!empty(auth::$user_id) && $bets_arr = auth::user()->bets_arr) {
            $bets_arr = explode(',',auth::user()->bets_arr);

            if(!empty($bets_arr)) {
                foreach($bets_arr as $k=>$v) {
                    $bets_arr[$k]= floatval($v);
                }
                $this->_config['bets']=$bets_arr;
            }
        }
    }

    public function double() {

        switch($this->_def_ans['suite']) {
            case 0:
                $c='D';
                break;
            case 1:
                $c='H';
                break;
            case 2:
                $c='S';
                break;
            case 3:
                $c='C';
                break;
        }

        $newans = [];

        $newans['Balance']="".(($this->_def_ans['balance'])/100);
        $newans['afterBalance']="".(($this->_def_ans['balance']+$this->_def_ans['win'])/100);

        $newans['gambleState']=$this->_def_ans['win']>0?'win':'lose';
        $newans['totalWin']=$this->_def_ans['win']/100;
        $newans['dealerCard']=$c;


        $this->_converted_ans = [
                'responseEvent' => 'gambleResult',
                'serverResponse' => $newans
        ];
        return $this;
    }

    public function start()
    {
        $c = $this->_config;

        $common = $c['common'];

        $common['Balance']="".(($this->_def_ans['balance'])/100);

        $this->_converted_ans = [
                'responseEvent' => 'getSettings',
                'slotLanguage' => $c['common_lang'],
                'serverResponse' => $common
        ];
        return $this;
    }

    public function freespin()
    {

        $newans = [];

        $newans['Balance']="".(($this->_def_ans['balance'])/100);
        $newans['Jackpots']=[
            'jack1' => '1.11',
            'jack2' => '2.22',
            'jack3' => '3.33',
        ];
        $newans['afterBalance']="".(($this->_def_ans['balance']+$this->_def_ans['win'])/100);

        $newans['bonusInfo']=[
            'scattersWin'=>"".($this->_def_ans['linesValue'][0]/100),
        ];

        $newans['reelsSymbols']=[];

        $count = count($this->_config['bars']);
        foreach($this->_def_ans['comb'] as $i=>$n) {
            $bar = ($i%$count)+1;

            if(!isset($newans['reelsSymbols']['reel'.$bar])) {
                $newans['reelsSymbols']['reel'.$bar]=[];
            }

            $newans['reelsSymbols']['reel'.$bar][]=$this->_config['compare'][$n];

            if(in_array($n,$this->_config['scatter'])) {

                $y = floor($i / $count);
                $newans['bonusInfo']['winReel'.$bar] = [
                        $y, 'SCAT' //todo check all games
                ];
            }
        }
        $newans['reelsSymbols']['rp']=th::mixedRange(array_keys($this->_config['pay']),count($this->_config['bars'])); //??????

        $newans['bonusWin']="".($this->_def_ans['session_total_win_free']??0); //это session_total_winfree вместе с первым выигрышем
        $newans['expPay']=0;
        $newans['expReels']=array_fill(0,count($this->_config['bars'])+1,false);
        $newans['expLines']=[];
        $newans['currentFreeGames']=$this->_def_ans['bonus_all']-$this->_def_ans['bonus'];


        if($this->_def_ans['bonus_win']>0) {
            $newans['bonusInfo']['scattersType']='bonus';
            $newans['bonusWin']="".($this->_def_ans['linesValue'][0]/100); //?? проверить
        }

        $stepWin=0;

        if(isset($this->_def_ans['bonus_super_symbol_win']) && $this->_def_ans['bonus_super_symbol_win']['win']>0) {

            $bonusmask = str_pad(decbin($this->_def_ans['bonus_super_symbol_win']['linesMask'][0]),count($this->_config['bars']),'0',STR_PAD_LEFT);

            for($i=1;$i<=count($this->_config['bars']);$i++) {
                $newans['expReels'][$i] = (bool) ($bonusmask[$i-1]==1);
            }

            $newans['expPay']="".($this->_def_ans['bonus_super_symbol_win']['win']/100);

            foreach($this->_def_ans['bonus_super_symbol_win']['linesValue'] as $bnum => $bval) {
                if($bval<=0) {
                    continue;
                }
                $bval = $bval;

                $stepWin+=$bval;

                $mask = str_pad(decbin($this->_def_ans['bonus_super_symbol_win']['linesMask'][$bnum]),count($this->_config['bars']),'0',STR_PAD_LEFT);
                $countSyms = substr_count($mask,1);

                $line = $this->_config['lines'][$bnum+1];

                $wR=[];

                $combIndex = -1;

                foreach($line as $row_index=>$row) {
                    foreach($row as $bar=>$flag) {
                        $combIndex++;
                        if($flag==0) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            'none','none'
                        ];

                        if(!$newans['expReels'][$bar+1]) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            $row_index, $this->_config['compare'][game::data('extra_param')]
                        ];
                    }
                }

                $wL = [
                        'Count'=> substr_count($mask,1),
                        'Line'=>$bnum,
                        'lineWin'=>"".($bval/100),
                        'Win'=>"".($stepWin/100),
                        'stepWin'=>"".($stepWin/100),
                ];

                foreach($wR as $i=>$k) {
                    $wL['winReel'.$i]=$k;
                }

                $newans['expLines'][]=$wL;
            }
        }

        if($this->_config['common']['slotBonusType']=='1') {
            $extra_param = game::data('extra_param')??false;
            if($extra_param!==false) {
                $newans['expSymbol']=$this->_config['compare'][$extra_param];
            }
        }


        $newans['totalFreeGames']= $this->_def_ans['bonus_all'];
        $newans['totalWin']= "".($this->_def_ans['win']/100);

        $newans['winLines']= [];
        if($this->_def_ans['win']-$stepWin>0) {
            $stepWin=0;
            foreach($this->_def_ans['linesValue'] as $num=>$val) {

                if($num==0) {
                    //scatter
                    continue;
                }

                if($val<=0) {
                    continue;
                }

                $val = $val;

                $stepWin+=$val;

                $mask = decbin($this->_def_ans['linesMask'][$num]);
                $countSyms = substr_count($mask,1);

                $line = $this->_config['lines'][$num];

                $wR=[];

                $combIndex = -1;

                foreach($line as $row_index=>$row) {
                    foreach($row as $bar=>$flag) {
                        $combIndex++;
                        if($flag==0) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            'none','none'
                        ];

                        if($countSyms<$bar+1) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            $row_index, $this->_config['compare'][$this->_def_ans['comb'][$combIndex]]
                        ];
                    }
                }

                $wL = [
                        'Count'=> substr_count($mask,1),
                        'Line'=>$num-1,
                        'Win'=>"".($val/100),
                        'stepWin'=>"".($stepWin/100),
                ];

                foreach($wR as $i=>$k) {
                    $wL['winReel'.$i]=$k;
                }

                $newans['winLines'][]=$wL;
            }
        }

        //bonus win first!!!!
        /*{"responseEvent":"spin","responseType":"bet","serverResponse":{"bonusWin":"50","expLines":[],"expPay":0,"expReels":[false,false,false,false,false,false],"expSymbol":"P_2","totalFreeGames":15,"currentFreeGames":0,"Balance":4826,"afterBalance":4876,"totalWin":50,"winLines":[],"bonusInfo":{"winReel2":[2,"SCAT"],"winReel4":[2,"SCAT"],"winReel5":[0,"SCAT"],"scattersType":"bonus","scattersWin":50},"Jackpots":{"jack1":"5.24","jack2":"3305.68","jack3":"3436.95"},"reelsSymbols":{"reel1":["J","10","P_4"],"reel2":["J","P_4","SCAT"],"reel3":["K","P_1","P_2"],"reel4":["P_3","P_4","SCAT"],"reel5":["SCAT","Q","P_2"],"rp":[5,7,7,3,1]}}}*/

        //expand in freegames!!!!
        /*{"responseEvent":"spin","responseType":"freespin","serverResponse":{"bonusWin":"70","expLines":[{"lineWin":2,"Count":2,"Line":0,"Win":2,"stepWin":2,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":1,"Win":4,"stepWin":4,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":2,"Win":6,"stepWin":6,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":3,"Win":8,"stepWin":8,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":4,"Win":10,"stepWin":10,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":5,"Win":12,"stepWin":12,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":6,"Win":14,"stepWin":14,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":7,"Win":16,"stepWin":16,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":8,"Win":18,"stepWin":18,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":9,"Win":20,"stepWin":20,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[0,"P_2"]}],"expPay":20,"expReels":[false,false,false,false,true,true],"expSymbol":"P_2","totalFreeGames":15,"currentFreeGames":3,"Balance":4826,"afterBalance":4846,"totalWin":20,"winLines":[],"bonusInfo":{"scattersWin":0},"Jackpots":{"jack1":"401.44","jack2":"3701.86","jack3":"3776.58"},"reelsSymbols":{"reel1":["P_3","J","K"],"reel2":["P_1","Q","10"],"reel3":["P_3","A","10"],"reel4":["P_1","P_4","P_2"],"reel5":["P_1","K","P_2"],"rp":[3,3,5,0,5]}}}*/



        $newans['oldans']=$this->_def_ans;

        $newans['slotJackpot'] = 5631;

        $this->_converted_ans = [
            'responseEvent' => 'spin',
            'responseType' => 'freespin',
            'serverResponse' =>$newans
        ];

        return $this;
    }

    public function spin()
    {
        $newans = [];

        $newans['Balance']="".(($this->_def_ans['balance'])/100);
        $newans['Jackpots']=[
            'jack1' => '1.11',
            'jack2' => '2.22',
            'jack3' => '3.33',
            //test jsckpot
//            'jackPay' => true
        ];

        $newans['slotJackpot'] = 5631;


        $newans['afterBalance']="".(($this->_def_ans['balance']+$this->_def_ans['win'])/100);

        $newans['bonusInfo']=[
            'scattersWin'=>"".($this->_def_ans['linesValue'][0]/100),
        ];

        $newans['reelsSymbols']=[];
        $newans['restoreFG']=$this->_def_ans['restoreFG'];

        $count = count($this->_config['bars']);
        $count_reel = count($this->_config['lines'][1]);
        foreach($this->_def_ans['comb'] as $i=>$n) {
            $bar = ($i%$count)+1;

            if(!isset($newans['reelsSymbols']['reel'.$bar])) {
                $newans['reelsSymbols']['reel'.$bar]=[];
            }

            $newans['reelsSymbols']['reel'.$bar][]=$this->_config['compare'][$n];

            if(in_array($n,$this->_config['scatter'])) {

                $y = floor($i / $count);
                $newans['bonusInfo']['winReel'.$bar] = [
                        $y, 'SCAT' //todo check all games
                ];
            }
        }
        $newans['reelsSymbols']['rp']=th::mixedRange(array_keys($this->_config['pay']),count($this->_config['bars'])); //??????

        $newans['bonusWin']="0"; //это session_total_winfree вместе с первым выигрышем

        if($this->_def_ans['bonus_win']>0) {
            $newans['bonusInfo']['scattersType']='bonus';
            $newans['bonusWin']="".($this->_def_ans['linesValue'][0]/100); //?? проверить
        }

        $newans['currentFreeGames']=$this->_def_ans['bonus_all']-$this->_def_ans['bonus'];
        $newans['expLines']=[];

        if($this->_config['common']['slotBonusType']=='1') {
            $extra_param = $this->_def_ans['extra_param']??false;
            if($extra_param!==false) {
                $newans['expSymbol']=$this->_config['compare'][$this->_def_ans['extra_param']];
            }
        }

        $newans['expPay']=0;
        $newans['expReels']=array_fill(0,count($this->_config['bars'])+1,false);
        $newans['totalFreeGames']= $this->_def_ans['bonus_all'];
        $newans['totalWin']= "".($this->_def_ans['win']/100);

        $newans['winLines']= [];
        if($this->_def_ans['win']>0) {
            $stepWin=0;
            foreach($this->_def_ans['linesValue'] as $num=>$val) {

                if($num==0) {
                    //scatter
                    continue;
                }

                if($val<=0) {
                    continue;
                }

                $val = $val;

                $stepWin+=$val;

                $mask = decbin($this->_def_ans['linesMask'][$num]);
                $countSyms = substr_count($mask,1);

                $line = $this->_config['lines'][$num];

                $wR=[];

                $combIndex = -1;

                foreach($line as $row_index=>$row) {
                    foreach($row as $bar=>$flag) {
                        $combIndex++;
                        if($flag==0) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            'none','none'
                        ];

                        if($countSyms<$bar+1) {
                            continue;
                        }

                        $wR[$bar+1]=[
                            $row_index, $this->_config['compare'][$this->_def_ans['comb'][$combIndex]]
                        ];
                    }
                }

                $wL = [
                        'Count'=> substr_count($mask,1),
                        'Line'=>$num-1,
                        'Win'=>"".($val/100),
                        'stepWin'=>"".($stepWin/100),
                ];

                foreach($wR as $i=>$k) {
                    $wL['winReel'.$i]=$k;
                }

                $newans['winLines'][]=$wL;
            }
        }

        //bonus win first!!!!
        /*{"responseEvent":"spin","responseType":"bet","serverResponse":{"bonusWin":"50","expLines":[],"expPay":0,"expReels":[false,false,false,false,false,false],"expSymbol":"P_2","totalFreeGames":15,"currentFreeGames":0,"Balance":4826,"afterBalance":4876,"totalWin":50,"winLines":[],"bonusInfo":{"winReel2":[2,"SCAT"],"winReel4":[2,"SCAT"],"winReel5":[0,"SCAT"],"scattersType":"bonus","scattersWin":50},"Jackpots":{"jack1":"5.24","jack2":"3305.68","jack3":"3436.95"},"reelsSymbols":{"reel1":["J","10","P_4"],"reel2":["J","P_4","SCAT"],"reel3":["K","P_1","P_2"],"reel4":["P_3","P_4","SCAT"],"reel5":["SCAT","Q","P_2"],"rp":[5,7,7,3,1]}}}*/

        //expand in freegames!!!!
        /*{"responseEvent":"spin","responseType":"freespin","serverResponse":{"bonusWin":"70","expLines":[{"lineWin":2,"Count":2,"Line":0,"Win":2,"stepWin":2,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":1,"Win":4,"stepWin":4,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":2,"Win":6,"stepWin":6,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":3,"Win":8,"stepWin":8,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":4,"Win":10,"stepWin":10,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":5,"Win":12,"stepWin":12,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":6,"Win":14,"stepWin":14,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[1,"P_2"]},{"lineWin":2,"Count":2,"Line":7,"Win":16,"stepWin":16,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[0,"P_2"],"winReel5":[0,"P_2"]},{"lineWin":2,"Count":2,"Line":8,"Win":18,"stepWin":18,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[2,"P_2"],"winReel5":[2,"P_2"]},{"lineWin":2,"Count":2,"Line":9,"Win":20,"stepWin":20,"winReel1":["none","none"],"winReel2":["none","none"],"winReel3":["none","none"],"winReel4":[1,"P_2"],"winReel5":[0,"P_2"]}],"expPay":20,"expReels":[false,false,false,false,true,true],"expSymbol":"P_2","totalFreeGames":15,"currentFreeGames":3,"Balance":4826,"afterBalance":4846,"totalWin":20,"winLines":[],"bonusInfo":{"scattersWin":0},"Jackpots":{"jack1":"401.44","jack2":"3701.86","jack3":"3776.58"},"reelsSymbols":{"reel1":["P_3","J","K"],"reel2":["P_1","Q","10"],"reel3":["P_3","A","10"],"reel4":["P_1","P_4","P_2"],"reel5":["P_1","K","P_2"],"rp":[3,3,5,0,5]}}}*/



        $newans['oldans']=$this->_def_ans;


        $this->_converted_ans = [
            'responseEvent' => 'spin',
            'responseType' => 'bet',
            'serverResponse' =>$newans
        ];

        return $this;
    }

}
