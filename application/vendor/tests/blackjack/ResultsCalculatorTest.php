<?php

/**
 * Description of BlackjackResultsCalculatorTest
 *
 * @author comp3
 */

require_once '../classes/blackjack/results/calculator.php';


class BlackjackResultsCalculatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getAll
     */
	public function testCalculate($gamersHands, $dealerHands, $results) {
		
		$calculator = new blackjack_results_calculator();

		$calculator->setGamersHands($gamersHands);
		$calculator->setDealerHands($dealerHands);
		
		$this->assertEquals($calculator->calculate(), $results);
	}
	
	
	public function getAll() {
		
		$data = array(
			// set #0
			array(
				array('1' => array(array('number' => '2'), array('number' => 'A')) ), 
				array(array('number' => '10'), array('number' => '6')), 
				array('1' => 'loser')
			),
			// set #1
			array(
				array('1' => array(array('number' => 'K'), array('number' => 'K')) ), 
				array(array('number' => '10'), array('number' => 'A')), 
				array('1' => 'loser')
			),
			// set #2
			array(
				array('1' => array(array('number' => '7'), array('number' => '6'), array('number' => 'A')) ), 
				array(array('number' => '10'), array('number' => 'A')), 
				array('1' => 'loser')
			),
			// set #3
			array(
				array('1' => array(array('number' => 'A'), array('number' => '10')) ), 
				array(array('number' => 'K'), array('number' => '10')), 
				array('1' => 'winner')
			),
			// set #4
			array(
				array('1' => array(array('number' => 'A'), array('number' => '10')) ), 
				array(array('number' => 'K'), array('number' => 'A')), 
				array('1' => 'standoff')
			),
			// set #5
			array(
				array('1' => array(array('number' => '9'), array('number' => '5')) ), 
				array(array('number' => '5'), array('number' => 'A'), array('number' => 'A')), 
				array('1' => 'loser')
			),
			// set #6
			array(
				array('1' => array(array('number' => 'A'), array('number' => '10')) ), 
				array(array('number' => '10'), array('number' => '7'), array('number' => '3')), 
				array('1' => 'winner')
			),
			// set #7
			array(
				array('1' => array(array('number' => 'A'), array('number' => '10')) ), 
				array(array('number' => 'A'), array('number' => 'K')), 
				array('1' => 'standoff')
			),
			// set #8
			array(
				array('1' => array(array('number' => '10'), array('number' => '7'), array('number' => '3')) ), 
				array(array('number' => 'A'), array('number' => 'K')), 
				array('1' => 'loser')
			)
			
		);
		
		return $data;
	}
			
	
}

?>
