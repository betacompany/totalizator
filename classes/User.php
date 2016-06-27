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

    public function getGuessed($comp_id = 0) {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT COUNT(*) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? and `comp_id`=? and `total_stakes`.`score`>0', $this->getId(), $comp_id);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getGuessed($competition->getId()));
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
                $guessed = $user->getGuessed($comp_id);

                $data[$i]['scores'] = $scores;
                $data[$i]['guessed'] = $guessed;
                $data[$i]['user'] = $user;
                $i++;
            } catch (Exception $e) {

            }
        }

        print_r($data);
        rsort($data);
        print_r($data);

        return $data;
    }

}

?>
