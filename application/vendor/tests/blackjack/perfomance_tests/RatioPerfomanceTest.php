<?php

/**
 * Description of BlackjackRatioPerfomanceTest
 *
 * @author comp3
 */

require_once 'blackjack/RatioCalculatorTest.php';
require_once '../classes/blackjack/ratio/calculator.php';


class BlackjackRatioPerfomanceTest extends PHPUnit_Framework_TestCase {
	

	/**
	 * @group perfomance
	 */
	function testTimeCalculating()
    {
		
		$handsBefore = $this->generateGamerAndDealerHands();

		$calculator  = new test_calculator_ratio($handsBefore['gamers'], $handsBefore['dealer']);
		$calculator->setOutcomesNumber(1000);		
		
		// chances
		$chances = $calculator->calculateChances();
		
		var_dump($chances);
		
		
		$this->assertEquals(1, 1);
		
    }
		

	function generateGamerAndDealerHands() {
		$resultArr = array();
		
		$deckManager = new DeckManager();
		
		$deckManager->createDecks(6);
		$deckManager->shuffleCards();

		$gamersHandNameList = array('hand1', 'hand2', 'hand3', 'hand4', 'hand5');
		
		foreach($gamersHandNameList as $gamerHandName) {
			$resultArr['gamers'][$gamerHandName] = $deckManager->takeCardsOpened(2);
		}
		
		$resultArr['dealer'] = $deckManager->takeCardsClosed(1);
		
		return $resultArr;
	}
	
	
	
}