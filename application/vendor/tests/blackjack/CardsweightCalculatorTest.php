<?php

/**
 * Description of BlackjackResultsCalculatorTest
 *
 * @author comp3
 */

require_once '../classes/deckmanager.php';
require_once '../classes/blackjack/cardsweight/calculator.php';


class BlackjackCardsweightCalculatorTest extends PHPUnit_Framework_TestCase {
	
    /**
     * @dataProvider provider
     */
	public function testCalculate($cards, $results) {
		
		$deckManager = new DeckManager();
		$calculator = new blackjack_cardsweight_calculator($deckManager);
				
		$this->assertEquals($calculator->calculateCardsWeight($cards), $results);
	}
	
	
	public function provider() {
		
		$data = array(
			// 1
			array(
				array(array('number' => '2'), array('number' => 'A')),
				array('all' => 13, 'without_ace' => 2)
			),
			// 2
			array(
				array(array('number' => '2'), array('number' => 'A'), array('number' => 'A')),
				array('all' => 14, 'without_ace' => 3)
			),
			// 3
			array(
				array(array('number' => 'A'), array('number' => 'A')),
				array('all' => 12, 'without_ace' => 1)
			),
			// 4
			array(
				array(array('number' => '10'), array('number' => '6')),
				array('all' => 16, 'without_ace' => 16)
			)
			
		);
		
		return $data;
	}
	
}

?>
