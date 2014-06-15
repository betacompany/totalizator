<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Competition.php';
require_once 'Competitor.php';
require_once 'Stake.php';

require_once './lib/mysql.php';

define('AVAILABLE_TIME', 120);

/**
 * Description of Match
 *
 * @author ortemij
 */
class Match {
    private $id;

    private $comp_id;
    private $comp1_id;
    private $competitor1 = false;
    private $comp2_id;
    private $competitor2 = false;
    private $score1;
    private $score2;
    private $played;
    private $timestamp;
    private $comment;

    private $name = "undefined";
    private $comp_name = "undefined";

    public function __construct($id) {
        $req = mysql_qw('SELECT * FROM `total_matches` WHERE `id`=?', $id);
        if ($match = mysql_fetch_assoc($req)) {
            $this->id = $match['id'];
            $this->comp_id = $match['comp_id'];
            $this->comp1_id = $match['comp1_id'];
            $this->comp2_id = $match['comp2_id'];
            $this->score1 = $match['score1'];
            $this->score2 = $match['score2'];
            $this->played = $match['played'];
            $this->timestamp = $match['timestamp'];
            $this->comment = $match['short_comment'];
        } else {
            throw new Exception('No math with id=' . $id, 0);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getScore1() {
        return $this->score1;
    }

    public function getScore2() {
        return $this->score2;
    }

    public function getScore() {
        return $this->score1 . ':' . $this->score2;
    }

    public function isPlayed() {
        return $this->played;
    }

    public function isAvailable() {
        return !($this->played) && ((strtotime($this->timestamp) - time()) >= AVAILABLE_TIME);
    }

    public function isAvailableFor($uid) {
        if (!$this->isAvailable())
            return false;

        $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `uid`=? and `match_id`=?', $uid, $this->getId());
        if ($match = mysql_fetch_assoc($req)) {
            return false;
        }

        return true;
    }

    public function getName() {
        if ($this->name != "undefined")
            return $this->name;

        try {
            $competitor1 = $this->getCompetitor1();
            $competitor2 = $this->getCompetitor2();
            $this->name = $competitor1->getName() . " vs " . $competitor2->getName();
            return $this->name;
        } catch (Exception $e) {
            $this->name = "Неизвестный матч";
            return $this->name;
        }
    }

    public function getCompetitor1() {
        if ($this->competitor1)
            return $this->competitor1;
        return $this->competitor1 = new Competitor($this->comp1_id);
    }

    public function getCompetitor2() {
        if ($this->competitor2)
            return $this->competitor2;
        return $this->competitor2 = new Competitor($this->comp2_id);
    }

    public function getCompName() {
        if ($this->comp_name != "undefined")
            return $this->comp_name;

        try {
            $competition = new Competition($this->comp_id);
            $this->comp_name = $competition->getName();
            return $this->comp_name;
        } catch (Exception $e) {
            $this->comp_name = "Неизвестное соревнование";
            return $this->comp_name;
        }
    }

    public function getTime() {
        return date('d/m/Y в H:i', strtotime($this->timestamp));
    }

    public function makeStake($uid, $score1, $score2) {
        return Stake::make($uid, $this->getId(), $score1, $score2);
    }

    public function removeStake($uid) {
        $stake = Stake::getByMatchIdAndUID($this->getId(), $uid);
        if ($stake != null) {
            $stake->remove($uid);
        } else {
            throw new Exception('Нет такой ставки', 0);
        }
    }

    public function getStakes() {
        return Stake::getByMatchId($this->getId());
    }

    public function getScoreForTotal() {
        return 4;
    }

    public function getScoreForDiff() {
        return 2;
    }

    public function getScoreForDrawNear() {
        return 2;
    }

    public function getScoreForDrawFar() {
        return 1;
    }

    public function getScoreForResult() {
        return 1;
    }

    public function getScoreForInvalid() {
        return 0;
    }

    public function finish($score1, $score2) {
        mysql_qw('UPDATE `total_matches` SET `score1`=?, `score2`=?, `played`=1 WHERE `id`=?', $score1, $score2, $this->getId());
        $this->score1 = $score1;
        $this->score2 = $score2;
    }

    public function getForecast() {
        $stakes = Stake::getByMatchId($this->getId());

        $count = 1;
        $count_w1 = 1;
        $count_d = 1;
        $count_w2 = 1;

        foreach ($stakes as $stake) {
            $count++;
            if ($stake->getScore1() > $stake->getScore2()) {
                $count_w1++;
            } elseif ($stake->getScore1() == $stake->getScore2()) {
                $count_d++;
            } else {
                $count_w2++;
            }
        }

        if ($count > 0) {
            $coef_w1 = round(100 * $count / $count_w1) / 100;
            $coef_d = round(100 * $count / $count_d) / 100;
            $coef_w2 = round(100 * $count / $count_w2) / 100;
            return "$coef_w1 : $coef_d : $coef_w2";
        }

        return 'Ставок нет';
    }

    public static function getActive() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_matches` WHERE `played`=0 ORDER BY `timestamp` ASC, `id` ASC');
        while ($match = mysql_fetch_assoc($req)) {
            try {
                $m = new Match($match['id']);
                if (!$m->isAvailable()) {
                    $data[] = $m;
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    public static function getAvailable() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_matches` WHERE `played`=0 ORDER BY `timestamp` ASC, `id` ASC');
        while ($match = mysql_fetch_assoc($req)) {
            try {
                $m = new Match($match['id']);
                if ($m->isAvailable())
                    $data[] = $m;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    public static function getPlayed() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_matches` WHERE `played`=1 ORDER BY `timestamp` DESC, `id` ASC LIMIT 20');
        while ($match = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Match($match['id']);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    public static function getAvailableFor($uid) {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_matches` WHERE `played`=1 ORDER BY `id` ASC');
        while ($match = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Match($match['id']);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    public static function create($comp_id, $comp1_id, $comp2_id, $datetime) {
        mysql_qw('INSERT INTO `total_matches` SET
			`comp_id`=?,
			`comp1_id`=?,
			`comp2_id`=?,
			`timestamp`=?', $comp_id, $comp1_id, $comp2_id, $datetime);
    }
}

?>
