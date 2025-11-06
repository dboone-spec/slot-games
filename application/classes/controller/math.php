<?php

class Controller_Math extends Controller {
	
	public function before(){
		
		
		exit;
	}

	public function action_math1(){
		ob_end_flush();
		
		$games=Kohana::$config->load('games.igrosoft');
		$games=['crazymonkey'=>1, 'fairyland'=>1, 'island'=>1];
		
		$ii=0;
		$all_g=count($games);
		foreach($games as $game=>$data){
			$ii++;
			$calc=new Slot_Igrosoft($game);
			$all=10000;
			for($i=1;$i<$all;$i++){
				$calc->calcmath1();
				if ($i % 100==0){
					echo "\r".round($i/$all*100).'%';
				}
			}

			echo "\rended $game $ii/$all_g\r\n";
		}
		
	}
	
	
	public function action_math0(){
		//1263
		$calc=new Slot_Novomatic('columbus');
		$calc->calcmath();

	}
	
	public function action_math(){
		//1263
		$calc=new Slot_Igrosoft('test');
		$calc->gen();

	}
	
	public function action_matha(){
		
		$calc=new Slot_Novomatic('columbus2');
		$calc->calcmath1(true);

	}
        
        public function action_math2(){
		
		$calc=new Slot_Novomatic('dolphin1');
		$calc->calcmath2();

	}
        
        
        public function action_mathbook(){
		
		$calc=new Slot_Novomatic_Bookofra();
        $calc->modeFreeRun=true;
		$calc->calcmath();

	}


	protected $info =[
        'bluestar100'=>[100,0.01,500,'3,5','Yes','Wild, Scatter'],
        'crystalskull100'=>[100,0.01,500,'5','Yes','Wild'],
        'dreamcatcher100'=>[100,0.01,500,'3,5','Yes','Wild, Scatter'],
        '6dreamcatcher100'=>[100,0.01,500,'4','Yes','Wild, Scatter'],
        'hotpepper100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'icecream100'=>[100,0.01,500,'3,5','Yes','Wild, Scatter'],
        '6icecream100'=>[40,0.01,200,'4','Yes','Wild, Scatter'],
        'megaice100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'icepepper100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'jokers100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'besthottest100'=>[5,0.05,100,'3,5','Yes','Wild, Scatter'],
        'megahot100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'megashine100'=>[100,0.01,500,'2,5','Yes','Wild, Scatter'],
        'shiningstars100'=>[100,0.01,500,'3,5','Yes','Wild, Scatter'],
        'besthottest20'=>[5,0.05,100,'2,5','Yes','Wild, Scatter'],
        '6luckyclover20'=>[5,0.05,100,'3','Yes','Wild, Scatter'],
        '6bluestar40'=>[40,0.01,200,'4','Yes','Wild, Scatter'],
        '6hotpepper40'=>[40,0.01,200,'2','Yes','Wild, Scatter'],
        '6megaice40'=>[40,0.01,200,'2','Yes','Wild, Scatter'],
        '6icepepper40'=>[40,0.01,200,'2','Yes','Wild, Scatter'],
        'besthottest40'=>[5,0.05,100,'3,5','Yes','Wild, Scatter'],
        '6luckyclover40'=>[5,0.05,100,'4','Yes','Wild, Scatter'],
        '6superhot40'=>[40,0.01,200,'2','Yes','Wild, Scatter'],
        'applesshine50'=>[50,0.1,250,'4','Yes','Wild, Scatter'],
        'gems50'=>[50,0.1,250,'1,5','Yes','Wild, Scatter'],
        'besthottest5'=>[5,0.05,100,'3','Yes','Wild, Scatter'],
        'acesandfaces'=>[10,0.1,24,'depends on the user\'s choice','Yes',''],
'aislot'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'aladdin'=>[100,0.1,500,'2,5','Yes','Wild, Scatter'],
'anonymous'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'applesshine'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'arabiannights'=>[10,0.01,50,'5','Yes','Wild, Scatter'],
'arabiannights2'=>[100,0.1,500,'2,5','Yes','Wild, Scatter'],
'bigfive'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'bitcoin'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'coolblizzard'=>[20,0.01,100,'2,5','Yes','Wild, Scatter'],
'6bluestar'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'bookofset'=>[30,0.01,150,'5','Yes','Wild, Scatter'],
'casino'=>[100,0.1,500,'2,5','Yes','Wild, Scatter'],
'cherryhot'=>[5,0.01,25,'4','Yes','Scatter'],
'crown'=>[100,0.1,500,'3','Yes','Wild, Scatter'],
'crystalskull'=>[20,0.01,100,'5','Yes','Wild, Scatter'],
'double'=>[5,0.05,100,'2','Yes',''],
'dreamcatcher'=>[20,0.01,100,'2,5','Yes','Wild, Scatter'],
'6dreamcatcher'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'extraspin'=>[10,0.01,50,'1,5','Yes','Wild, Scatter'],
'extraspin3'=>[10,0.01,50,'1,5','Yes','Wild, Scatter'],
'firefighters'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'gems20'=>[20,0.01,100,'2','Yes','Wild, Scatter'],
'bankofny'=>[20,0.01,100,'5','Yes','Wild, Scatter'],
'greenhot'=>[5,0.05,100,'3,5','Yes','Wild, Scatter'],
'hotpepper'=>[20,0.01,100,'2','Yes','Wild, Scatter'],
'6hotpepper'=>[20,0.01,100,'1,5','Yes','Wild, Scatter'],
'icecream'=>[20,0.01,100,'2,5','Yes','Wild, Scatter'],
'6icecream'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'megaice'=>[20,0.01,100,'2','Yes','Wild, Scatter'],
'6megaice'=>[20,0.01,100,'1,5','Yes','Wild, Scatter'],
'icepepper'=>[20,0.01,100,'2','Yes','Wild, Scatter'],
'6icepepper'=>[20,0.01,100,'1,5','Yes','Wild, Scatter'],
'iceqween'=>[10,0.01,50,'4,5','Yes','Wild, Scatter'],
'infinitygems'=>[20,0.01,100,'5','Yes','Wild, Scatter'],
'jacksorbetter'=>[10,0.1,100,'depends on the user\'s choice','Yes',''],
'jokers20'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'keno'=>[5,0.05,100,'depends on the user\'s choice','Yes',''],
'luckyslot'=>[10,0.01,50,'3','Yes','Wild, Scatter'],
'megahot20'=>[20,0.01,100,'2','Yes','Wild, Scatter'],
'megashine'=>[30,0.01,150,'2','Yes','Wild, Scatter'],
'pharaoh2'=>[30,0.01,150,'5','Yes','Wild, Scatter'],
'piratesgold'=>[20,0.01,100,'5','Yes','Wild, Scatter'],
'sevenhot20'=>[20,0.01,100,'2','Yes','Scatter'],
'shiningstars'=>[5,0.05,100,'3','Yes','Wild, Scatter'],
'stalker'=>[10,0.01,50,'5','Yes','Wild, Scatter'],
'6superhot5'=>[20,0.01,100,'1,5','Yes','Wild, Scatter'],
'tensorbetter'=>[10,0.1,100,'depends on the user\'s choice','Yes',''],
'tesla'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'leprechaun'=>[10,0.01,50,'5','Yes','Wild, Scatter'],
'timemachine2'=>[15,0.01,75,'5','Yes','Wild, Scatter'],
'tropichot'=>[5,0.05,100,'2','Yes',''],
'wildwest'=>[100,0.1,500,'2,5','Yes','Wild, Scatter'],
'bigfoot40'=>[40,0.01,200,'3','Yes','Wild, Scatter'],
'hothothot5'=>[5,0.05,100,'4,5','Yes','Wild, Scatter'],
'happysanta50'=>[50,0.1,250,'3','Yes','Wild, Scatter'],
'aroundtheworld'=>[40,0.01,200,'1,5','Yes','Wild, Scatter'],
'bigfoot'=>[20,0.01,100,'3,5','Yes','Wild, Scatter'],
'doubleice'=>[5,0.05,200,'2,5','Yes',''],
'happysanta'=>[20,0.01,100,'3','Yes','Wild, Scatter'],
'iceiceice'=>[5,0.05,200,'4','Yes','Wild, Scatter'],
'wizard'=>[50,0.1,250,'2','Yes','Wild, Scatter'],
'spinners'=>[1,25,500,'depends on the user\'s choice','Yes',''],
'roshambo'=>[1,25,500,'depends on the user\'s choice','Yes',''],
'tothemoon'=>[5,0.1,100,'depends on the user\'s choice','Yes',''],
'vangogh'=>[5,0.05,100,'1,5','Yes',''],
'hokusai'=>[5,0.05,100,'2','Yes',''],
'extraspin2'=>[40,0.01,200,'2,5','Yes','Wild, Scatter'],
'klimt'=>[5,0.05,100,'3,5','Yes',''],
'munch'=>[5,0.05,100,'5','Yes',''],
'vermeer'=>[5,0.05,100,'1,5','Yes',''],
'rubens'=>[5,0.05,100,'1,5','Yes',''],
'monet'=>[5,0.05,100,'2','Yes',''],
'renoir'=>[5,0.05,100,'1','Yes',''],
'jewels'=>[28,0.01,140,'2,5','Yes',''],
'rembrandt'=>[5,0.05,100,'1','Yes',''],
'aliens'=>[5,0.05,100,'2','Yes',''],
'fruitqueen'=>[18,0.01,90,'2,5','Yes','Wild'],
'gauguin'=>[5,0.05,100,'1,5','Yes',''],
'michelangelo'=>[5,0.05,100,'1','Yes',''],
'halloween'=>[5,0.05,100,'2','Yes',''],
'cezanne'=>[5,0.05,100,'1,5','Yes',''],
'manet'=>[5,0.05,100,'1,5','Yes',''],
'bosch'=>[5,0.05,100,'2','Yes',''],
'pixelkingdom'=>[100,0.1,500,'3','Yes','Wild, Scatter'],
'santa'=>[10,0.01,50,'1,5','Yes','Wild, Scatter'],
'valentine'=>[5,0.01,25,'2,5','Yes','Scatter'],
'suncity'=>[30,0.01,150,'3,5','Yes','Wild, Scatter, Bonus buy'],
'foolsday'=>[5,0.05,100,'2,5','Yes','Wild, Scatter'],
'pedrope'=>[5,0.05,100,'1,5','Yes','Scatter'],
'panda'=>[5,0.05,100,'5','Yes','Wild, Scatter'],
'valkyrie'=>[30,0.01,150,'1','Yes','Wild, Scatter, Bonus buy'],
'hotclover100'=>[5,0.05,100,'3,5','Yes','Wild, Scatter'],
'egypt'=>[15,0.01,75,'2','Yes','Wild, Scatter'],
'christmas'=>[25,0.01,500,'5','Yes','Wild, Scatter, Bonus buy'],
'redcrown'=>[100,0.01,500,'3,5','Yes','Wild, Scatter'],


    ];

        
        public function action_gameinfo(){

            $useClones = false;

            $sql='select name,visible_name,rtp
                from games
                where show=1
                and branded=0 
                order by name';
            
            $games=db::query(1, $sql)->execute()->as_array('name');
            
            $config=[];
            $clones=[];
            $checkSum=[];
            
            foreach ($games as $game){
                $c= Kohana::$config->load('agt/'.$game['name']);
                $c=$c->as_array();
                $c['heigth']=$c['heigth']??3;


                //vermeer hokusai
                if (isset($c['probability96']) ){
                    $c['barsCount']=$c['width'];
                }
                //others
                else{
                    $c['bars']=$c['bars'] ?? $c['bars96'] ?? [];
                    if ( is_array($c['bars']) ){
                        $c['barsCount']=count($c['bars']);
                    }
                    else {
                        $c['barsCount']=-1;
                    }

                }



                if( isset($c['lines']) && is_array($c['lines']) ){
                    $c['linesCount']=count($c['lines']);
                }
                else {
                    $c['linesCount']=-1;
                }
                
                $c['wild_except']=$c['wild_except']?? [];
                
                
                if (!isset ($c['pay']) ){
                    continue;
                }
                if (!isset ($c['pay'][0]) ){
                    continue;
                }
                
                if (!isset ($c['wild']) ){
                    $c['wild']=[];
                }
                
                if (!isset ($c['anypay']) ){
                    $c['anypay']=[];
                }
                

                $c['maxMult'] = 0;
                foreach ($c['pay'] as $num=>$pay){

                    $c['maxMult'] = max($c['maxMult'],max($pay));

                    $mark=[];
                    if(in_array($num,$c['wild'])){
                        $mark[]='wild';
                    }

                    if(in_array($num,$c['anypay'])){
                        $mark[]='scatter';
                    } 

                    if(in_array($num,$c['wild_except'])){
                        $mark[]='wild except';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                    if(in_array($num,$c['replace_bar']??[])){
                        $mark[]='substitutes for all on the same reel';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                }
          
                $c['FG']=isset($c['barFree']) ? 'Yes' : 'No';
                
                
                $check=0;
                foreach ($c['pay'] as $pay){
                    $check+= array_sum($pay)/$c['heigth'];
                }
                
                
                
                $cl=array_keys($checkSum,$check);
                if (count($cl)>0 && $game['name']!='vangogh' && $useClones ){
                    $clones[$game['name']]=$c;
                    $clones[$game['name']]['cloneOf']=$cl[0];
                }
                else{
                    $checkSum[$game['name']]=$check;
                    $config[$game['name']]=$c;
                }
                
                        
                        
                        
                
                
            }
            
            //full view
            //$view=new View('test/gameinfo');

            //short view
            //$view=new View('test/gameinfoshort');

            $view=new View('test/csv');

            $view->config=$config;
            $view->games=$games;
            $view->clones=$clones;
            $view->info=$this->info;

            $this->response->body($view);
            
            
        }
        
           public function action_listofgames(){
            
            $sql='select name,visible_name,rtp
                from games
                where show=1
                order by visible_name';
            
            $games=db::query(1, $sql)->execute()->as_array('name');
            
            $config=[];
            $clones=[];
            $checkSum=[];
            
            foreach ($games as $game){
                $c= Kohana::$config->load('agt/'.$game['name']);
                $c=$c->as_array();
                
                $c['images']=[];
                $dir=DOCROOT.'..'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'screen'.DIRECTORY_SEPARATOR.$game['name'];
                
                
                if (is_dir($dir)){
                    $files= scandir($dir);
                    foreach ($files as $file){
                        
                        if (in_array($file,['.', '..'])){
                            continue;
                        }
                        
                        $c['images'][]=$file;
                        
                    }
                }
                
                
                if ( in_array($game['name'],['acesandfaces','jacksorbetter','tensorbetter','keno']) ){
                    
                    $c['rtp']='97.54% - 99.54%';
                   
                    $max=0;
                    foreach ($c['pay'] as $num=>$pay){
                        foreach ($pay as $pay1){
                            if ($max<$pay1){
                                $max=$pay1;
                            }
                        }
                    }

                    $c['maxWin']=$max;
                    if ( $game['name']=='keno' ){
                        $c['rtp']='95.55% - 98.00%';
                        $c['maxWin']=15000;
                    }
                    $c['FSback']='No';
                    $c['FG']='No';
                    unset($c['pay']);
                   
                   $config[$game['name']]=$c;
                   continue;
                }
                
                
                $c['FSback']='Yes';
                $c['heigth']=$c['heigth']??3;
                
                $c['rtp']= str_replace(',','.', $game['rtp']);
                $c['rtp']= round(  ((float) $c['rtp'])*100,2).'%';
                
                
                
                if (isset($c['lines_choose'])){
                    $c['linesCh']=$c['lines_choose'];
                    sort($c['linesCh']);
                    $c['linesCh']=implode(', ',$c['linesCh']);
                }
                else{
                    $c['linesCh']='';
                }
                
                

       
                
                if ( isset($c['bars']) && is_array($c['bars']) ){
                    $c['barsCount']=count($c['bars']);
                }
                elseif ( isset($c['bars96']) && is_array($c['bars96']) ){
                    $c['barsCount']=count($c['bars96']);
                }
                else {
                    $c['barsCount']=-1;
                }

                if( isset($c['lines']) && is_array($c['lines']) ){
                    $c['linesCount']=count($c['lines']);
                }
                else {
                    $c['linesCount']=-1;
                }
                
                $c['wild_except']=$c['wild_except']?? [];
                $c['info']=[];


                //set default values;
                $c['wild']=$c['wild'] ?? [];
                $c['wild_multiplier']=$c['wild_multiplier'] ?? 1;
                $c['anypay']=$c['anypay'] ?? [];

                if(count($c['wild'])>0 ){
                   $c['info'][]='Wild';
                }

                if( count($c['anypay'])>0 ){
                   $c['info'][]='Scatter';
                }
               
               
                
                $max=0;


                foreach ($c['pay'] as $num=>$pay){


                    foreach ($pay as $pay1){
                        if (!in_array($num,$c['wild'])){
                            if (is_array($pay1)){
                                $pay1=$pay1;
                            }
                            else{
                                $pay1*=$c['wild_multiplier'];
                            }
                        }
                        if ($max<$pay1){
                           $max=$pay1;
                        }
                    }
                    
                    $mark=[];
                    if(in_array($num,$c['wild'])){
                        $mark[]='wild';
                    }

                    if(in_array($num,$c['anypay'])){
                        $mark[]='scatter';
                    } 

                    if(in_array($num,$c['wild_except'])){
                        $mark[]='wild except';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                    if(in_array($num,$c['replace_bar']??[])){
                        $mark[]='substitutes for all on the same reel';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                }
          
                
                
                $c['FG']=isset($c['barFree']) ? 'Yes' : 'No';
                
                if ($c['FG']=='Yes'){
                    $max*=$c['free_multiplier']??1;
                }
                
                $c['maxWin']=$max;
                
                if ( in_array($game['name'],['wildwest','alladin']) ){
                    unset($c['pay'][12]);
                    unset($c['pay'][13]);
                    unset($c['pay'][14]);
                     
                }
                               
                

                $config[$game['name']]=$c;
                
            }
            
            
            $view=new View('test/listofgames');
            $view->config=$config;
            $view->games=$games;
            $view->clones=[];
            $this->response->body($view);
            
            
        }
        

           public function action_csv(){
            
            $sql='select name,visible_name,rtp
                from games
                where show=1
                order by visible_name';
            
            $games=db::query(1, $sql)->execute()->as_array('name');
            
            $config=[];
            $clones=[];
            $checkSum=[];
            
            $csv=[];
            
            foreach ($games as $game){
                
                $row=[];
                $c= Kohana::$config->load('agt/'.$game['name']);
                $c=$c->as_array();
                
                $row['name']=$game['visible_name'];
                $row['provider']='site-domain';
                $row['id']=$game['name'];
                $row['rtp']= str_replace(',','.', $game['rtp']);
                $row['rtp']= round(  ((float) $row['rtp'])*100,2).'%';
                $row['type']='slot';
                $row['jackpot']='Yes';
                $row['lang']='English, German, French,Turkish, Russian';
                
                //Reels	Lines	Freespins (Yes/No)	Jackpot (Yes/No)			Other features

                
                
                $c['images']=[];
                $dir=DOCROOT.'..'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'screen'.DIRECTORY_SEPARATOR.$game['name'];
                
                
                if (is_dir($dir)){
                    $files= scandir($dir);
                    foreach ($files as $file){
                        
                        if (in_array($file,['.', '..'])){
                            continue;
                        }
                        
                        $c['images'][]=$file;
                        
                    }
                }
                
                
                if ( in_array($game['name'],['acesandfaces','jacksorbetter','tensorbetter','keno']) ){
                    
                    $c['rtp']='97.54% - 99.54%';
                   
                    $max=0;
                    foreach ($c['pay'] as $num=>$pay){
                        foreach ($pay as $pay1){
                            if ($max<$pay1){
                                $max=$pay1;
                            }
                        }
                    }

                    $c['maxWin']=$max;
                    if ( $game['name']=='keno' ){
                        $c['rtp']='95.55% - 98.00%';
                        $c['maxWin']=15000;
                    }
                    $c['FSback']='No';
                    $c['FG']='No';
                    unset($c['pay']);
                   
                    $row['type']='table';
                    $row['rtp']=$c['rtp'];
                    $row['lines']='';
                    $row['reels']='';
                    $row['info']='';
                    $row['FS']='';
                    $csv[]=$row;
                   continue;
                }
                
                                
                $c['FSback']='Yes';
                $c['heigth']=$c['heigth']??3;
                
                $c['rtp']= str_replace(',','.', $game['rtp']);
                $c['rtp']= round(  ((float) $c['rtp'])*100,2).'%';
                
                
                
                if (isset($c['lines_choose'])){
                    $c['linesCh']=$c['lines_choose'];
                    sort($c['linesCh']);
                    $c['linesCh']=implode(', ',$c['linesCh']);
                }
                else{
                    $c['linesCh']='';
                }
                
                $row['lines']=$c['linesCh'];

       
                
                if ( isset($c['bars']) && is_array($c['bars']) ){
                    $c['barsCount']=count($c['bars']);
                }
                elseif ( isset($c['bars96']) && is_array($c['bars96']) ){
                    $c['barsCount']=count($c['bars96']);
                }
                else {
                    $c['barsCount']=-1;
                }

                $row['reels']=$c['barsCount'];
                
                
                if( isset($c['lines']) && is_array($c['lines']) ){
                    $c['linesCount']=count($c['lines']);
                }
                else {
                    $c['linesCount']=-1;
                }
                
                $c['wild_except']=$c['wild_except']?? [];
                $c['info']=[];
                
               
                if( count($c['wild'])>0 ){
                   $c['info'][]='Wild';
                }

                if( count($c['anypay'])>0 ){
                   $c['info'][]='Scatter';
                }
                
                $row['info']=$c['info'];
               
               
                
                $max=0;
                foreach ($c['pay'] as $num=>$pay){
                    
                    
                    foreach ($pay as $pay1){
                        if (!in_array($num,$c['wild'])){
                            $pay1*=$c['wild_multiplier'];
                        }
                        if ($max<$pay1){
                           $max=$pay1;
                        }
                    }
                    
                    $mark=[];
                    if(in_array($num,$c['wild'])){
                        $mark[]='wild';
                    }

                    if(in_array($num,$c['anypay'])){
                        $mark[]='scatter';
                    } 

                    if(in_array($num,$c['wild_except'])){
                        $mark[]='wild except';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                    if(in_array($num,$c['replace_bar']??[])){
                        $mark[]='substitutes for all on the same reel';
                    }
                    $c['mark'][$num]=implode(', ',$mark);
                    
                }
          
                
                
                $c['FG']=isset($c['barFree']) ? 'Yes' : 'No';
                
                if ($c['FG']=='Yes'){
                    $max*=$c['free_multiplier']??1;
                }
                
                $row['FS']=$c['FG'];
                
                $c['maxWin']=$max;
                
                if ( in_array($game['name'],['wildwest','alladin']) ){
                    unset($c['pay'][12]);
                    unset($c['pay'][13]);
                    unset($c['pay'][14]);
                     
                }
                               
                
                $csv[]=$row;
                
            }
            
            $output='';
            foreach($csv as $r){
               
                if (is_array($r['info'])){
                    $r['info']=implode(', ',$r['info']);
                }
                $output.="{$r['name']};{$r['provider']};{$r['id']};{$r['reels']};{$r['lines']};{$r['rtp']};{$r['FS']};{$r['jackpot']};;{$r['type']};{$r['info']};{$r['lang']}\r\n";
            }
            
            
            $this->response->body($output);
            
            
        }


        
        public function action_card(){
            
            for($i=1;$i<=52;$i++){
                echo "$i ".card::print_card($i)."<br>";
            }
        }
        
        public function action_poker(){
            ob_end_clean();

            
            auth::$user_id=15;
            
            $calc=new Videopoker_Game_Tensorbetter();
            $calc->betcoin=5;
            $calc->amount=1;
            game::session('videopoker','tensorbetter');
            
            $in=0;
            $out=0;
            
            $level= array_fill(0,13,0);
            for($i=0;$i<=100000000;$i++){
            //for($i=0;$i<=10;$i++){
                $deal=$calc->deal();
                $hold=$calc->hold;
  //              echo card::print_card($calc->cardon);

                $deal=$calc->draw($hold);
                $in+=$calc->amount;
                $out+=$calc->win;
                
                $level[$calc->level]++;
                
/*
                echo "\r\n";
                echo card::print_card($calc->cardon);
                echo "\r\n";

                echo "win: {$calc->win} winComb:{$calc->wincomb}\r\n";
                echo json_encode($calc->wincard);
                echo "\r\n---------------------------\r\n";
 
     */           
                if ($i % 10000==0){
                    $z=round($out/$in,6);
                    echo "count:$i in:$in out:$out RTP:$z\r\n";
                    echo json_encode($level)."\r\n";
                }
            }
            $z=round($out/$in,6);
            echo "count:$i in:$in out:$out RTP:$z\r\n";
            echo json_encode($level)."\r\n";

        }
	
}
