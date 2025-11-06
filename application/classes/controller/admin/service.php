<?php

class Controller_Admin_Service extends Controller_Admin_Base
{

    public function action_index()
    {

    }

    public function action_interkassapayments()
    {
        $i = new interkassa();

        foreach($i->getInvoiceList([]) as $pi)
        {
            if($pi['paywayId'] == '50d9ebfd8f2a2dd45d000015')
            {
                continue;
            }
            
            if($pi['stateName'] != 'success')
            {
                continue;
            }

            $p = new Model_Payment($pi['paymentNo']);
            if(!$p->loaded())
            {
                continue;
            }

            if($p->status != 30)
            {
                print_r($p->as_array());
            }
        }
    }

}
