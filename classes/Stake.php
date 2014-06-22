<?php

require_once dirname(__FILE__) . '/Match.php';
require_once dirname(__FILE__) . '/StakeCalculator.php';

require_once dirname(__FILE__) . '/../lib/mysql.php';

/**
 * Description of Stake
 *
 * @author ortemij
 */
class Stake {

    const DRAW = "draw";
    const WIN1 = "win1";
    const WIN2 = "win2";

    private $id;
    private $uid;
    private $match_id;
    private $stake_score1;
    private $stake_score2;
    private $played;
    private $score;

    public function __construct($id) {
        $req = mysql_qw('SELECT * FROM `total_stakes` WHERE `id`=?', $id);
        if ($stake = mysql_fetch_assoc($req)) {
            $this->id = $stake['id'];
            $this->uid = $stake['uid'];
            $this->match_id = $stake['match_id'];
            $this->stake_score1 = $stake['stake_score1'];
            $this->stake_score2 = $stake['stake_score2'];
            $this->played = $stake['played'];
            $this->score = $stake['score'];
        } else {
            throw new Exception('No stake with id=' . $id, 0);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getUID() {
        return $this->uid;
    }

    public function getMatchId() {
        return $this->match_id;
    }

    public function getStakeScore() {
        return $this->stake_score1 . ":" . $this->stake_score2;
    }

    public function getScore1() {
        return $this->stake_score1;
    }

    public function getScore2() {
        return $this->stake_score2;
    }

    public function isPlayed() {
        return $this->played;
    }

    public function getScore() {
        return $this->score;
    }

    public function getType() {
        if ($this->getScore1() == $this->getScore2()) {
            return self::DRAW;
        }
        if ($this->getScore1() < $this->getScore2()) {
            return self::WIN2;
        }
        return self::WIN1;
    }

    public function edit($uid, $score1, $score2) {
        if ($uid != $this->getUID()) {
            throw new Exception('Попытка изменить не свою ставку!', 0);
        }

        mysql_qw('UPDATE `total_stakes` SET `stake_score1`=?, `stake_score2`=? WHERE `id`=?', $score1, $score2, getId());
    }

    public function remove($uid) {
        if ($uid != $this->getUID()) {
            throw new Exception('Попытка удалить не свою ставку!', 0);
        }

        mysql_qw('DELETE FROM `total_stakes` WHERE `id`=?', $this->getId());
    }

    private function setScore($value) {
        mysql_qw('UPDATE `total_stakes` SET `score`=?, `played`=1 WHERE `id`=?', $value, $this->getId());
    }

    public function finish(Match $match) {
        $this->setScore(StakeCalculator::calculateScore(
            $match->getScore1(),
            $match->getScore2(),
            $this->getScore1(),
            $this->getScore2()
        ));
    }

    public static function make($uid, $match_id, $score1, $score2, $force = false) {
        try {
            $match = new Match($match_id);
            if (!$match->isAvailableFor($uid) && !$force) {
                throw new Exception('Такая ставка уже была сделана: match_id=' . $match_id, 0);
            }
        } catch (Exception $e) {
            throw $e;
        }

        mysql_qw('INSERT INTO `total_stakes` SET
		    `uid`=?,
		    `match_id`=?,
		    `stake_score1`=?,
		    `stake_score2`=?,
		    `played`=0,
		    `score`=0
		 ', $uid, $match_id, $score1, $score2);

        return mysql_insert_id();
    }

    public static function getByMatchId($match_id) {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `match_id`=?', $match_id);
        while ($stake = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Stake($stake['id']);
            } catch (Exception $e) {

            }
        }

        return $data;
    }

    public static function getByMatchIdAndUID($match_id, $uid) {
        $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `match_id`=? and `uid`=?', $match_id, $uid);
        if ($stake = mysql_fetch_assoc($req)) {
            return new Stake($stake['id']);
        } else {
            return null;
        }
    }

    public static function getActiveFor($uid) {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `played`=0 and `uid`=?', $uid);
        while ($stake = mysql_fetch_assoc($req)) {
            try {
                $stake_o = new Stake($stake['id']);
                $match = new Match($stake_o->getMatchId());
                if ($match->isAvailable()) {
                    $data[] = $stake_o;
                }
            } catch (Exception $e) {

            }
        }

        return $data;
    }

    public static function getWaitingFor($uid) {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `played`=0 and `uid`=?', $uid);
        while ($stake = mysql_fetch_assoc($req)) {
            try {
                $stake_o = new Stake($stake['id']);
                $match = new Match($stake_o->getMatchId());
                if (!$match->isAvailable()) {
                    $data[] = $stake_o;
                }
            } catch (Exception $e) {

            }
        }

        return $data;
    }

    public static function getPlayedFor($uid, $compId = 0) {
        $data = array();
        if ($compId == 0) {
            $req = mysql_qw('SELECT `id` FROM `total_stakes` WHERE `played`=1 and `uid`=? ORDER BY `match_id` DESC', $uid);
        } else {
            $req = mysql_qw('
            SELECT `total_stakes`.`id` FROM
            (`total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`)
            WHERE `total_stakes`.`played`=1 and `total_stakes`.`uid`=? and `total_matches`.`comp_id`=? ORDER BY `total_stakes`.`match_id` DESC', $uid, $compId);
        }
        while ($stake = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Stake($stake['id']);
            } catch (Exception $e) {

            }
        }

        return $data;
    }

}

?>
