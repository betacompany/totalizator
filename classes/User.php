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
class User
{

    private $id;
    private $name;
    private $surname;

    public function __construct($id)
    {
        $user = CommonAuth::getData($id);
        if ($user) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->surname = $user['surname'];
        } else {
            throw new Exception('There is no user with id=' . $id, 0);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getSN()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getSNnbsp()
    {
        return $this->name . '&nbsp;' . $this->surname;
    }

    public function getScores($comp_id = 0)
    {
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

    public function getGuessedOutcomes($comp_id = 0)
    {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT COUNT(*) AS `scores` FROM `total_stakes` INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id` WHERE `uid`=? AND `comp_id`=? AND `total_stakes`.`score`>0', $this->getId(), $comp_id);
            return mysql_result($req, 0, 'scores');
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval($this->getGuessedOutcomes($competition->getId()));
        }

        return $sum;
    }

    public static function getGuessesOrdered($comp_id = 0)
    {
        if ($comp_id != 0) {
            $req = mysql_qw('SELECT `total_stakes`.`uid` AS uid,
              SUM(`total_stakes`.`score`) AS points,
              SUM(IF(`total_stakes`.`score` = 4, 1, 0)) AS count4,
              SUM(IF(`total_stakes`.`score` = 3, 1, 0)) AS count3,
              SUM(IF(`total_stakes`.`score` = 2, 1, 0)) AS count2,
              SUM(IF(`total_stakes`.`score` = 1, 1, 0)) AS count1
              FROM `total_stakes`
              INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`
              WHERE `comp_id`=?
              GROUP BY uid
              ORDER BY points DESC, count4 DESC, count3 DESC, count2 DESC, count1 DESC', $comp_id);
            return mysql_result($req, 0);
        }

        $sum = 0;
        foreach (Competition::getAll() as $competition) {
            $sum += intval(User::getGuessesOrdered($competition->getId()));
        }

        return $sum;
    }

    public function getPlayedStakes($compId = 0)
    {
        return Stake::getPlayedFor($this->getId(), $compId);
    }

    public static function getAllByRating($comp_id = 0)
    {
        $data = array();
        $i = 0;
        // get all users that have stakes
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

                $data[$i]['scores'] = $scores;
                $data[$i]['outcomes'] = $user->getGuessedOutcomes($comp_id);
                $data[$i]['user'] = $user;
                $i++;
            } catch (Exception $e) {

            }
        }
        $guesses_sorted = User::getGuessesOrdered($comp_id);
//        $data[$i]['point_stats'] = $guesses_sorted;

        $q = mysql_query('SELECT `total_stakes`.`uid` AS uid,
          SUM(`total_stakes`.`score`) AS points,
          SUM(IF(`total_stakes`.`score` = 4, 1, 0)) AS count4,
          SUM(IF(`total_stakes`.`score` = 3, 1, 0)) AS count3,
          SUM(IF(`total_stakes`.`score` = 2, 1, 0)) AS count2,
          SUM(IF(`total_stakes`.`score` = 1, 1, 0)) AS count1
          FROM `total_stakes`
          INNER JOIN `total_matches` ON `total_stakes`.`match_id`=`total_matches`.`id`
          WHERE `comp_id`=?
          GROUP BY uid
          ORDER BY points DESC, count4 DESC, count3 DESC, count2 DESC, count1 DESC', $comp_id);
        while ($row = mysql_fetch_assoc($q)) {
//            $user = new User($row['uid']);
//            print "$user->name $user->surname\t";
            foreach ($row as $name => $value) {
                print "$name: $value\t";
            }
            print "\r\n";
        }

        return $data;
    }

    function compare_by_score_then_by_valued_guesses($a, $b)
    {
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
