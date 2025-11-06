<?php

class Slot_Novomatic_Pharaohsgold2d extends Slot_Novomatic
{

    public function __construct ()
    {
        parent::__construct ('pharaohsgold2d');
    }

    public function lightingLine ($num = null)
    {
        $a = parent::lightingLine ($num);
        if (is_null ($num))
        {
            array_shift ($a);
        }
        return $a;
    }

}
