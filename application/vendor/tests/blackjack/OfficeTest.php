<?php

/**
 * Description of BlackjackResultsCalculatorTest
 *
 * @author comp3
 */

require_once '../classes/rate.php';
require_once '../classes/office.php';


class OfficeTest extends PHPUnit_Framework_TestCase {
	
    /**
     * @dataProvider officesProvider
     */
	public function testSelect($office, $results) {
		
		$officeInstance = new Office();

		$this->assertEquals($officeInstance->select($office), $results);
	}
	
		
	public function officesProvider() {
		
		// TODO: fill test-data array
		
		$data = array();
		
		return $data;
	}
	
}

?>
