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
            $req = mysql_qw('SELECT SUM(`score`) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? AND `comp_id`=?', $this->getId(), $comp_id);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getScores($competition->getId()));
        }

        return $sum;
    }

    public function getPlayedStakes($compId = 0) {
        return Stake::getPlayedFor($this->getId(), $compId);
    }

    public static function getAllByRating($comp_id = 0) {
        if ($comp_id == 0) {
            $qwery = mysql_qw('SELECT `total_stakes`.`uid`,
              SUM(`total_stakes`.`score`) AS scores,
              SUM(IF(`total_stakes`.`score` = 4, 1, 0)) AS count4,
              SUM(IF(`total_stakes`.`score` = 3, 1, 0)) AS count3,
              SUM(IF(`total_stakes`.`score` = 2, 1, 0)) AS count2,
              SUM(IF(`total_stakes`.`score` = 1, 1, 0)) AS count1
              FROM (`total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`)
              GROUP BY uid
              ORDER BY scores DESC, count4 DESC, count3 DESC, count2 DESC, count1 DESC');
        } else {
            $qwery = mysql_qw('SELECT `total_stakes`.`uid`,
              SUM(`total_stakes`.`score`) AS scores,
              SUM(IF(`total_stakes`.`score` = 4, 1, 0)) AS count4,
              SUM(IF(`total_stakes`.`score` = 3, 1, 0)) AS count3,
              SUM(IF(`total_stakes`.`score` = 2, 1, 0)) AS count2,
              SUM(IF(`total_stakes`.`score` = 1, 1, 0)) AS count1
              FROM (`total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`)
              WHERE `total_matches`.`comp_id`=?
              GROUP BY uid
              ORDER BY scores DESC, count4 DESC, count3 DESC, count2 DESC, count1 DESC', $comp_id);
        }

        $data = array();
        $i = 0;
        $previous_user_score = array(
            "score" => 0,
            "count4" => 0,
            "count3" => 0,
            "count2" => 0,
            "count1" => 0
        );
        while ($row = mysql_fetch_assoc($qwery)) {
            try {
                $user = new User($row['uid']);
                $data[$i]['user'] = $user;
                $data[$i]['scores'] = $row['scores'];
                $data[$i]['sort_info'] = "";
                $this_scores = array(
                    "score" => $row['scores'],
                    "4" => $row['count4'],
                    "3" => $row['count3'],
                    "2" => $row['count2'],
                    "1" => $row['count1']
                );
                if ($previous_user_score["score"] == $row['scores']) {
                    foreach ($this_scores as $category => $value) {
                        $prev_value = $previous_user_score[$category];
                        if ($value < $prev_value) {
                            $pts = $category == 1 ? "очку" : "очка";
                            $data[$i - 1]['sort_info'] = "больше угаданных ставок по $category $pts: $prev_value против $value";
                            break;
                        } else {
                            $data[$i - 1]['sort_info'] = "EQUAL";
                        }
                    }
                }
                $previous_user_score = $this_scores;
                $i++;
            } catch (Exception $e) {
            }
        }
        return $data;
    }

}

?>
