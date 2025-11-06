<?php

class Slot_Amarok extends Slot_Calc{
	
	public $mud=0;		
	
	
	public function __construct( $name) {
		parent::__construct('amarok', $name);
	}
	
	
	
	public function ans_init(){
		
		$rules_url="/ru/terms";    		// ссылка где написанны правила игры
		$help_url="/ru/messages";      		// ссылка где написанна справка по казино
		$bank_url="/ru/in";       		// ссылка на аккаунт пользователя
		$gamelist_url="/select";  	// ссылка на список игр
		
		
		
		$ans["currency"]="Ru";
		$ans["lng"]="EN";
		$ans["cas_log"]="YC";
		$ans["pop_use"]="0";
		$ans["pop_1disp"]="30";
		$ans["pop_nextdisp"]="60";
		$ans["pop_url"]="";
		$ans["pop_var1"]="Do you want to play in real mode ?";
		$ans["pop_var2"]="Yes";
		$ans["pop_var3"]="No";
		$ans["pop_var4"]="";
		$ans["pop_var"]="";
		$ans["rules"]=$rules_url;
		$ans["help"]=$help_url;
		$ans["bank"]=$bank_url;
		$ans["casino"]=$gamelist_url;
		$ans["t_payouts"]="PAYOUTS";
		$ans["t_select"]="SELECT LINES";
		$ans["t_betlines"]="BET PER LINE";
		$ans["t_spin"]="SPIN ";
		$ans["t_betmax"]="BET MAX.";
		$ans["t_autoplay"]="AUTO PLAY";
		$ans["t_stop"]="STOP";
		$ans["t_double"]="DOUBLE";
		$ans["t_collect"]="COLLECT";
		$ans["t_play"]="PLAY";
		$ans["t_cancel"]="CANCEL";
		$ans["t_back"]="BACK TO GAME";
		$ans["t_help"]="HELP";
		$ans["t_exit"]="EXIT";
		$ans["t_rules"]="RULES";
		$ans["t_info"]="INFO";
		$ans["t_bank"]="BANK";
		$ans["t_numlines"]="NUMBER OF LINES";
		$ans["t_coinslines"]="COINS PER LINE";
		$ans["t_totalbet"]="TOTAL BET";
		$ans["t_credit"]="CREDIT";
		$ans["t_winpaid"]="TOTAL WIN";
		$ans["t_bonuswin"]="BONUS WIN";
		$ans["t_gameover1"]="GAME";
		$ans["t_gameover2"]="OVER";
		$ans["t_coinvalue"]="COIN VALUE";
		$ans["t_bet"]="BET";
		$ans["t_scatterspay"]="SCATTERS PAY";
		$ans["t_unfinished"]="A FREE MODE WAS NOT COMPLETED";
		$ans["t_payoutsincoins"]="PAYOUTS IN COINS";
		$ans["t_pays2x"]="PAYS 2x";
		$ans["t_pays"]="PAYS";
		$ans["t_choosecolor"]="Select a color";
		$ans["t_choosesymbol"]="Select a symbol";
		$ans["t_paylines"]="PAYLINES";
		$ans["t_characters"]="CHARACTERS";
		$ans["t_items"]="OBJETS";
		$ans["t_symbols1"]="SYMBOLS 1";
		$ans["t_symbols2"]="SYMBOLS 2";
		$ans["t_specials"]="SPECIALS";
		$ans["t_bonusgame"]="BONUS GAME";
		$ans["t_bonuspays"]="Bonus pays : number of lines x number of coins x station's payout";
		$ans["t_doubleup"]="DOUBLE UP";
		$ans["t_previous"]="PREVIOUS";
		$ans["t_next"]="NEXT";
		$ans["t_3dices"]=" >3 DICES";
		$ans["t_4dices"]=" >4 DICES";
		$ans["t_5dices"]=" >5 DICES";
		$ans["t_any3"]="anywhere on the screen gives 3 dices throws";
		$ans["t_any4"]="anywhere on the screen gives 4 dices throws";
		$ans["t_any5"]="anywhere on the screen gives 5 dices throws";
		$ans["t_rulesdouble"]="Any winnings (Except bonus winnings) with a value of less than three times the wagers can be doubled or quadrupled.";
		$ans["t_rulesbonus"]="After each dice throw, your pawn moves to the station corresponding to the result of the dice throw. You win the the number of coins given for this station, multiplied by the number of lines played and multiplied by the number of coins.";
		$ans["t_click"]="Click here to go back to the game";
		$ans["t_eachline"]="On each line]=you can bet from 1 to 10 coins";
		$ans["t_replace_pint"]="Substitutes other symbols except Scatters. Every payout where it substitutes a symbol is doubled";
		$ans["t_close"]="CLOSE";
		$ans["t_alert1"]="Not enough credits, please check your account";
		$ans["t_alert2"]="Your session has expired, please log-in";
		$ans["t_alert3"]="Please only play in one window";



		$ans["t_coinslines"]="COINS PER LINE";
		$ans["t_totalbet"]="TOTAL BET";
		$ans["t_credit"]="CREDIT";
		$ans["t_winpaid"]="WINPAID";
		$ans["t_gameover1"]="GAME";
		$ans["t_gameover2"]="OVER";
		$ans["t_coinvalue"]="COIN VALUE";
		$ans["t_bet"]="BET";
		$ans["t_scatterspay"]="SCATTERS PAY";
		$ans["t_unfinished"]="A FREE MODE WAS NOT COMPLETED";
		$ans["t_freeplayed"]="FREE GAMES PLAYED";
		$ans["t_freetoplay"]="FREE GAMES LEFT";
		$ans["t_payoutsincoins"]="PAYOUTS IN COINS";
		$ans["t_15freegames"]="15 FREE GAMES";
		$ans["t_20freegames"]="20 FREE GAMES";
		$ans["t_25freegames"]="25 FREE GAMES";
		$ans["t_scatterpays"]="Scatter pays anywhere on the screen";

		$ans["t_replace"]="Mona Lisa substitute other symbols except Scatters";
		$ans["t_replace_james"]="The symbol James substitutes other symbols except Scatters";
		$ans["t_replace_jv"]="The symbol Jules Verne substitutes other symbols except Scatters";
		$ans["t_replace_heroe"]="The symbol SuperHeroe substitute other symbols except Scatters";
		$ans["t_replace_cat"]="The symbol Cat substitute other symbols except Scatters";
		$ans["t_replace_referee"]="The symbol Referee substitute other symbols except Scatters";
		$ans["t_replace_guns"]="The symbol Lasers substitute other symbols except Scatters";
		$ans["t_replace_invaders"]="The symbol Invaders substitute other symbols except Scatters";
		$ans["t_replace_peacock"]="The symbol peacock substitute other symbols except Scatters";
		$ans["t_replace_king"]="The symbol King substitute other symbols except Scatters";
		$ans["t_replace_snow"]="The symbol SnowFlake substitute other symbols except Scatters";

		$ans["t_pays2x"]="PAYS 2x";
		$ans["t_doubled"]="Every payout where Mona Lisa substitute a symbol is doubled";
		$ans["t_doubled_james"]="Every payout where the symbol James substitutes a symbol is doubled";
		$ans["t_doubled_jv"]="Every payout where the symbol Jules Verne substitutes a symbol is doubled";
		$ans["t_doubled_heroe"]="Every payout where the symbol SuperHeroe substitute a symbol is doubled";
		$ans["t_doubled_cat"]="Every payout where the symbol Cat ABCDEF substitute a symbol is doubled";
		$ans["t_doubled_referee"]="Every payout where the symbol Referee substitute a symbol is doubled";
		$ans["t_doubled_guns"]="Every payout where the symbol Lasers substitute a symbol is doubled";
		$ans["t_doubled_guns"]="Every payout where the symbol Invaders substitute a symbol is doubled";
		$ans["t_doubled_peacock"]="Every payout where the symbol peacock substitute a symbol is doubled";
		$ans["t_doubled_king"]="Every payout where the symbol King substitute a symbol is doubled";
		$ans["t_doubled_snow"]="Every payout where the symbol SnowFlake substitutes a symbol is doubled";
		$ans["t_payoutsx3"]="";
		$ans["t_alert1"]="Crйdit insuffisant]=vйrifiez votre compte";
		$ans["t_alert2"]="Votre session est terminйe]=veuillez vous reloguer";
		$ans["t_alert3"]="Ne jouez que dans une fenкtre";
		$ans["t_jackpotwon"]="VOUS GAGNEZ LE JACKPOT !";
		$ans["t_close"]="FERMER";
		$ans["error"]="0";
		$ans["mode_free"]="0";
		$ans["nb_gratuit_to_play"]="";
		$ans["nb_gratuit_played"]="";
		$ans["bonus"]="0";
		$ans["rebuild"]="0";
		
		return $ans;
		
	}
	
	
	public $double_mode;
	
	public function double(){
		
		if ($this->double_mode=='color'){
			$this->doubleclass=new Double_Color();
		}
		else {
			$this->doubleclass=new Double_Suit();
		}

		
		parent::double();
		
		
		
	}
	
	
	public function ColorSlotToCard($color){
		if($color==1){
			return 2;
		}
		if($color==2){
			return 1;
		}
		throw new Exception;
		
	}
	
	public function SuitSlotToCard($suit){
		if($suit==1){
			return 2;
		}
		if($suit==2){
			return 3;
		}
		if($suit==3){
			return 4;
		}
		if($suit==4){
			return 1;
		}
		throw new Exception;
		
	}
	
	
	public function CardSlotFromCard($card){
		
		$num=card::num($card);
		$num;
		$suit=card::suit($card);
		$suit--;
		$suit= $suit==0 ? 4 : $suit;
		
		return ($suit-1)*13+$num;
	}
	
	
	protected $bonus_multiplier=[250,100,10,25,5,50,  20,100,35,75,150,0, 50,25,5,5,10,5, 75,250,50,20,100,75, 200,125,250,75,500,2500 ];
	
	public function calcbonus() {
		
		
		$this->bonus_win=0;
		$this->bonusdata=[];
		$pos=0;
		for($i=1;$i<=$this->bonusrun;$i++){
			$h=math::random_int(1,6);
			$pos+=$h;
			$this->bonusdata[$i]=$h;
			$this->bonus_win+=$this->amount*$this->bonus_multiplier[$pos-1];
		}
		
	}
	
	
	public $bonusnum;
	public $digit;
	public $pos;
	public $win_pos;
	
	public function bonusgame(){
		
		if (game::data('can_bonus')!=1){
			throw new HTTP_Exception_404;
		}
		$this->bonusrun=game::data('bonusrun',0);
		if ($this->bonusrun==0){
			throw new HTTP_Exception_404;
		}
		
		$this->amount=game::data('bet');
		$this->cline=game::data('lines');
		$this->amount_line=(int) $this->amount/$this->cline;
		//текущая итерация
		$this->bonusnum=game::data('bonusnum',0);
		//данные бонус игры
		$p=game::data('bonusdata');
		
		
		//следующий ход
		$this->bonusnum++;
		$this->digit=$p[$this->bonusnum];
		$this->pos=0;
		
		$this->win_all=0;
		//собираем данные
		$result='';
		for($i=1;$i<=$this->bonusnum;$i++){
			$this->pos+=$p[$i];
			$poswin=$this->bonus_multiplier[$this->pos-1]*$this->amount;
			$this->win_all+=$poswin;
			$result.="$i) step:{$p[$i]} pos:$this->pos poswin:$poswin win_all:{$this->win_all}\r\n";
		}
		$this->win_pos=$this->bonus_multiplier[$this->pos-1]*$this->amount;
		
		$data=[];
		$data['bonusnum']=$this->bonusnum;
		
		if ($this->bonusnum==$this->bonusrun){
			$data=null;
		}
		
		
		
		$bet['amount']=0;
		$bet['come']=$this->pos-1;
		$bet['result']=$result;
		$bet['win']=$this->win_pos;
		$bet['method']='bonus';
		
		//TODO ставка
		bet::make($bet,$this->win_pos,$data);
		
	}
	
	
	
	public function bonuscalcmath(){
		
		$win=[0,0,0,0,0,0];
		
		if ($this->bonus_param[3]>0){
			$win[3]=37845/(6*6*6);
    		/*
			for($i1=1;$i1<=6;$i1++){
				for($i2=1;$i2<=6;$i2++){
					for($i3=1;$i3<=6;$i3++){
						
						$win[3]+=$this->bonus_multiplier[$i1-1];
						$win[3]+=$this->bonus_multiplier[$i1+$i2-1];
						$win[3]+=$this->bonus_multiplier[$i1+$i2+$i3-1];
					}
				}
			}
			 */
		}
		
		
		if ($this->bonus_param[4]>0){
			$win[4]=283115/(6*6*6*6);;
			/*
			for($i1=1;$i1<=6;$i1++){
				for($i2=1;$i2<=6;$i2++){
					for($i3=1;$i3<=6;$i3++){
						for($i4=1;$i4<=6;$i4++){
								
								$win[4]+=$this->bonus_multiplier[$i1-1];
								$win[4]+=$this->bonus_multiplier[$i1+$i2-1];
								$win[4]+=$this->bonus_multiplier[$i1+$i2+$i3-1];
								$win[4]+=$this->bonus_multiplier[$i1+$i2+$i3+$i4-1];
								
						}
					}
				}
			}
			 */
		}

		
		if ($this->bonus_param[5]>0){
			$win[5]=2146500/(6*6*6*6*6);;
			/*
			for($i1=1;$i1<=6;$i1++){
				for($i2=1;$i2<=6;$i2++){
					for($i3=1;$i3<=6;$i3++){
						for($i4=1;$i4<=6;$i4++){
							for($i5=1;$i5<=6;$i5++){
				
								$win[5]+=$this->bonus_multiplier[$i1-1];
								$win[5]+=$this->bonus_multiplier[$i1+$i2-1];
								$win[5]+=$this->bonus_multiplier[$i1+$i2+$i3-1];
								$win[5]+=$this->bonus_multiplier[$i1+$i2+$i3+$i4-1];
								$win[5]+=$this->bonus_multiplier[$i1+$i2+$i3+$i4+$i5-1];
							}
						}
					}
				}
			}*/
		}
		
		return $win;
		
	}
	
}

