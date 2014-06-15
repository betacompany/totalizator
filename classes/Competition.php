<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Match.php';

require_once './lib/mysql.php';

/**
 * Description of Competition
 *
 * @author ortemij
 */
class Competition {
    private $id;
    private $name;

    public function  __construct($id) {
        $req = mysql_qw('SELECT * FROM `total_competitions` WHERE `id`=?', $id);
        if ($comp = mysql_fetch_assoc($req)) {
            $this->id = $comp['id'];
            $this->name = $comp['name'];
        } else {
            throw new Exception('No competition with id=' . $id, 0);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getMatches() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_matches` WHERE `comp_id`=? ORDER BY `id`', $this->getId());
        while ($match = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Match($match['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

    public static function getAll() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_competitions` WHERE 1=1 ORDER BY `id` DESC');
        while ($comp = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Competition($comp['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

}

?>
