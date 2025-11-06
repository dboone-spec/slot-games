<?php

/**
 * Description of BlackjackRatioCalculatorTest
 *
 * @author comp3
 */

require_once '../classes/rate.php';
require_once '../classes/deckmanager.php';
require_once '../classes/blackjack/rules.php';
require_once '../classes/blackjack/game/logic.php';
require_once '../classes/blackjack/results/calculator.php';

require_once '../classes/blackjack/ratio/calculator.php';


class test_calculator_ratio extends blackjack_ratio_calculator {
	
	public function calculateChances() {
		return parent::calculateChances();
	}
	
}


class BlackjackRatioCalculatorTest extends PHPUnit_Framework_TestCase {
	

    /**
     * @group exclude
     * @dataProvider provider
     */
	public function testCalculateChancesInGamerBlackjackCase($gamersHands, $dealerHands, $chancesExpected) {
		
		$calculator  = new test_calculator_ratio($gamersHands, $dealerHands);
		$calculator->setOutcomesNumber(100);		
		
		// chances
		$chancesCalculated = $calculator->calculateChances();

		$this->assertEquals($chancesCalculated, $chancesExpected);
		
	}


	public function provider() {
		
		$data = array(
			// 1 - dealer blackjack (standoff)
			array(
				array('1' => array(array('number' => 'A'), array('number' => 'J')) ), 
				array(array('number' => '10'), array('number' => 'A')), 
				array('1' => 0)
			)
		);
		
		return $data;
	}

	
	
}