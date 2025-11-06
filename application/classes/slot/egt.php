<?php

class Slot_Egt extends Slot_Calc {

    public $mud = 0;
    protected $_countrolls=5; //количество реальных символов барабанов передаваемые на клиент

    public function __construct($name) {
        parent::__construct('egt', $name);

        $game = new Model_Game(['provider' => 'our', 'name' => $name]);
        $off_game = new Model_Office_Game(['game_id' => $game->id, 'office_id' => OFFICE]);
        //$bars = 'bars_' . $off_game->z * 100;
        $this->bars = $this->config['bars'];

        $this->_countrolls = $this->heigth+2;

        $this->barcount = count($this->bars);
        $this->barFree = arr::get($this->config, 'barFree', $this->bars);
    }

    public function double() {

        $this->doubleclass = new Double_Egt($this->game_id);

        parent::double();
    }

    public function extrasym() {

        $a=[];

        if(empty($this->pos)) {
            for($yy = 1; $yy <= $this->barcount; $yy++) {
                $this->pos[$yy]=0;
            }
        }

        for ($i = 1; $i <= $this->barcount; $i++) {
            $a[]= array_slice($this->bars[$i],$this->pos[$i]-1,$this->_countrolls);
        }

        foreach($a as $i => &$b) {
            if(count($b)<$this->_countrolls) {
                foreach(array_slice($this->bars[$i+1],0,$this->_countrolls-count($b)) as $k) {
                    $b[]=$k;
                }
            }
        }
        return $a;
    }

}
