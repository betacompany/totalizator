<?php

require_once dirname(__FILE__) . '/../classes/StakeCalculator.php';

class StakeCalculatorTest extends PHPUnit_Framework_TestCase {

    public function testResultCalculation() {
        $this->check(1, 0, 1, 0, 4);

        $this->check(2, 0, 1, 0, 1);
        $this->check(2, 0, 2, 0, 4);

        $this->check(2, 1, 1, 0, 2);
        $this->check(2, 1, 2, 0, 1);
        $this->check(2, 1, 2, 1, 4);

        $this->check(3, 0, 4, 0, 2);

        $this->check(4, 1, 1, 0, 1);
        $this->check(4, 1, 2, 0, 1);
        $this->check(4, 1, 2, 1, 1);
        $this->check(4, 1, 3, 0, 2);
        $this->check(4, 1, 3, 1, 2);
        $this->check(4, 1, 3, 2, 1);
        $this->check(4, 1, 4, 0, 3);
        $this->check(4, 1, 4, 1, 4);

        $this->check(5, 0, 4, 0, 3);

        $this->check(5, 1, 4, 0, 2);
        $this->check(5, 1, 4, 1, 3);
        $this->check(5, 1, 5, 0, 3);

        $this->check(5, 2, 4, 1, 2);
        $this->check(5, 2, 4, 2, 2);
        $this->check(5, 2, 5, 1, 3);

        $this->check(5, 3, 5, 2, 2);

        $this->check(6, 2, 5, 1, 2);
    }

    public function check($stake1, $stake2, $score1, $score2, $expected) {
        $actual = StakeCalculator::calculateScore($score1, $score2, $stake1, $stake2);
        $this->assertEquals($expected, $actual, "Difference: " . $stake1 . ":" . $stake2 . " -> " . $score1 . ":" . $score2);

        $actual = StakeCalculator::calculateScore($stake1, $stake2, $score1, $score2);
        $this->assertEquals($expected, $actual, "Difference: " . $score1 . ":" . $score2 . " -> " . $stake1 . ":" . $stake2);
    }
}