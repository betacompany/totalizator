<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../lib/mysql.php';
require_once dirname(__FILE__) . '/../lib/access.php';

require_once dirname(__FILE__) . '/Competition.php';

/**
 * Description of User
 *
 * @author ortemij
 */
class User {

    private $id;
    private $name;
    private $surname;

    public function __construct($id) {
        $user = CommonAuth::getData($id);
        if ($user) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->surname = $user['surname'];
        } else {
            throw new Exception('There is no user with id=' . $id, 0);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getSN() {
        return $this->name . ' ' . $this->surname;
    }

    public function getSNnbsp() {
        return $this->name . '&nbsp;' . $this->surname;
    }

    public function getScores($comp_id = 0) {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT SUM(`score`) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? and `comp_id`=?', $this->getId(), $comp_id);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getScores($competition->getId()));
        }

        return $sum;
    }

    public function getGuessedOutcomes($comp_id = 0) {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT COUNT(*) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? and `comp_id`=? and `total_stakes`.`score`>0', $this->getId(), $comp_id);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getGuessedOutcomes($competition->getId()));
        }

        return $sum;
    }

    public function getGuessesByPoints($comp_id = 0, $points) {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT COUNT(*) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? and `comp_id`=? and `total_stakes`.`score`=?', $this->getId(), $comp_id, $points);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getGuessesByPoints($competition->getId()));
        }

        return $sum;
    }

    public function getPlayedStakes($compId = 0) {
        return Stake::getPlayedFor($this->getId(), $compId);
    }

    public static function getAllByRating($comp_id = 0) {
        $data = array();
        $i = 0;
        if ($comp_id == 0) {
            $req = mysql_qw('SELECT DISTINCT(`total_stakes`.`uid`) FROM
                (`total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`)
                WHERE 1=1');
        } else {
            $req = mysql_qw('SELECT DISTINCT(`total_stakes`.`uid`) FROM
                (`total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`)
                WHERE `total_matches`.`comp_id`=?', $comp_id);
        }
        while ($u = mysql_fetch_assoc($req)) {
            try {
                $user = new User($u['uid']);
                $scores = $user->getScores($comp_id);
                $guesses_by_points = array();
                for ($j = 1; $j <= 4; $j++) {
                    $guesses_by_points[$j] = $user->getGuessesByPoints($comp_id, $j);
                }

                $data[$i]['scores'] = $scores;
                $data[$i]['point_stats'] = $guesses_by_points;
                $data[$i]['outcomes'] = $user->getGuessedOutcomes($comp_id);
                $data[$i]['user'] = $user;
                $i++;
            } catch (Exception $e) {

            }
        }

        uasort($data, 'compare_by_score_then_by_valued_guesses');
        print_r("\n after: ");
        print_r($data);

        return $data;
    }

    function compare_by_score_then_by_valued_guesses($a, $b) {
        if ($a['scores'] > $b['scores']) return 1;
        elseif ($a['scores'] < $b['scores']) return -1;
        for ($i = 4; $i >= 1; $i--) {
            if ($a['point_stats'][$i] > $b['point_stats'][$i]) return 1;
            elseif ($a['point_stats'][$i] < $b['point_stats'][$i]) return -1;
        }
        return 0;
    }

}

?>
