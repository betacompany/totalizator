<?php

function sign($n) {
    return $n > 0 ? 1 : ($n < 0 ? -1 : 0);
}

class StakeCalculator {

    /**
     * @param $real_score1
     * @param $want_score1
     * @param $real_score2
     * @param $want_score2
     * @return int
     */
    public static function calculateScore($real_score1, $real_score2, $want_score1, $want_score2) {

        $real_diff = $real_score1 - $real_score2;
        $want_diff = $want_score1 - $want_score2;

        $real_sign = sign($real_diff);
        $want_sign = sign($want_diff);

        if ($real_sign != $want_sign) {
            // nothing guessed
            return 0;
        }

        if ($real_score1 == $want_score1 && $real_score2 == $want_score2) {
            // score guessed
            return 4;
        }

        $real_winner_score = max($real_score1, $real_score2);
        $real_loser_score = min($real_score1, $real_score2);

        $want_winner_score = max($want_score1, $want_score2);
        $want_loser_score = min($want_score1, $want_score2);

        $one_score_guessed = $real_winner_score == $want_winner_score || $real_loser_score == $want_loser_score;

        if (abs($want_diff - $real_diff) <= 1 && $one_score_guessed) {
            if (abs($real_diff) >= 3 && abs($want_diff) >= 3) {
                if ($real_winner_score >= 4 && $want_winner_score >= 4) {
                    // almost guessed completely crushing victory
                    return 3;
                }
            }

            if (abs($real_diff) >= 2 && abs($want_diff) >= 2) {
                // almost guessed convincing/confident (?) victory
                return 2;
            }
        }

        if ($real_diff == $want_diff && abs($real_score1 - $want_score1) <= 1) {
            // near difference guessed
            return 2;
        }

        return 1;
    }
}