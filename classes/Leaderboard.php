<?php

require_once dirname(__FILE__) . '/../lib/mysql.php';

require_once dirname(__FILE__) . '/User.php';

/**
 * Description of Leaderboard
 * 
 * @author ortemij
 */
class Leaderboard {

    private $id;
    private $compId;
    private $finished;
    private $playerIdsHash = array();

    public function __construct($data) {
        if (is_scalar($data)) {
            $req = mysql_qw('SELECT * FROM `total_leaderboards` WHERE `id`=?', $data);
            if ($lb = mysql_fetch_assoc($req)) {
                $this->id = $lb['id'];
                $this->compId = $lb['comp_id'];
                $this->finished = $lb['finished'];
                $req = mysql_qw('SELECT `uid` FROM `total_leaderboard_players` WHERE `lb_id`=?', $this->id);
                while ($pl = mysql_fetch_assoc($req)) {
                    $this->playerIdsHash[$pl['uid']] = true;
                }
            } else {
                throw new Exception('No leaderboard with id=' . $data, 0);
            }
        } else if (is_array($data)) {
            $this->id = $data['id'];
            $this->compId = $data['comp_id'];
            $this->finished = $data['finished'];
            $req = mysql_qw('SELECT `uid` FROM `total_leaderboard_players` WHERE `lb_id`=?', $this->id);
            while ($pl = mysql_fetch_assoc($req)) {
                $this->playerIdsHash[$pl['uid']] = true;
            }
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getCompId() {
        return $this->compId;
    }

    public function isFinished() {
        return $this->finished;
    }

    public function hasPlayer($uid) {
        return isset($this->playerIdsHash[$uid]);
    }

    public static function getByCompId($compId) {
        $req = mysql_qw('SELECT * FROM `total_leaderboards` WHERE `comp_id`=?', $compId);
        $leaderboards = array();
        if ($lb = mysql_fetch_assoc($req)) {
            return new Leaderboard($lb);
        } else {
            return null;
        }
    }

    public static function getGloryHall() {
        $req = mysql_qw('SELECT uid, COUNT(*) as count FROM 
                            `total_leaderboards` l 
                            JOIN `total_leaderboard_player` p 
                            ON l.id = p.lb_id 
                         WHERE l.finished = 1 AND p.place = 1
                         GROUP BY uid
                         ORDER BY count DESC');
        $data = array();
        while ($row = mysql_fetch_assoc($req)) {
            $user = new User($row['uid']);
            $data[] = array(
                'user' => $user,
                'count' => $row['count']
            );
        }
        return $data;
    }
}
