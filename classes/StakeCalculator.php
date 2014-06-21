<?php

class StakeCalculator {

    /**
     * @param $real_score1
     * @param $want_score1
     * @param $real_score2
     * @param $want_score2
     * @return int
     */
    public static function calculateScore($real_score1, $real_score2, $want_score1, $want_score2) {
        if ($real_score1 == $want_score1 && $real_score2 == $want_score2) {
            // score guessed
            $score = 4;
            return $score;
        } else if ($real_score1 - $real_score2 == $want_score1 - $want_score2) {
            // difference guessed
            if (abs($real_score1 - $want_score1) <= 1) {
                // near
                $score = 2;
                return $score;
            } else {
                // far
                $score = 1;
                return $score;
            }
        } else if ($real_score1 - $real_score2 >= 2 && $want_score1 - $want_score2 >= 2 && ($real_score1 == $want_score1 && abs($real_score2 - $want_score2) <= 1 || $real_score2 == $want_score2 && abs($real_score1 - $want_score1) <= 1)
        ) {
            if ($real_score1 - $real_score2 >= 3 && $want_score1 - $want_score2 >= 3 && max($real_score1, $real_score2) >= 4 && max($want_score1, $want_score2) >= 4
            ) {
                // almost guessed completely crushing victory
                $score = 3;
                return $score;
            } else {
                // almost guessed convincing/confident (?) victory
                $score = 2;
                return $score;
            }
        } else if (($real_score1 > $real_score2 && $want_score1 > $want_score2) || ($real_score1 < $real_score2 && $want_score1 < $want_score2)) {
            //winner guessed
            $score = 1;
            return $score;
        } else {
            // nothing guessed
            $score = 0;
            return $score;
        }
    }
}