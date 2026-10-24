<?php

require_once dirname(__FILE__) . '/TotalMatch.php';

require_once dirname(__FILE__) . '/../lib/mysql.php';

/**
 * Description of Competition
 *
 * @author ortemij
 */
class Competition {
    private $id;
    private $name;

    public function  __construct($id) {
        $req = db_qw('SELECT * FROM `total_competitions` WHERE `id`=?', $id);
        if ($comp = db_fetch_assoc($req)) {
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
        $req = db_qw('SELECT `id` FROM `total_matches` WHERE `comp_id`=? ORDER BY `id`', $this->getId());
        while ($match = db_fetch_assoc($req)) {
            try {
                $data[] = new TotalMatch($match['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

    public static function getAll() {
        $data = array();
        $req = db_qw('SELECT `id` FROM `total_competitions` WHERE 1=1 ORDER BY `id` DESC');
        while ($comp = db_fetch_assoc($req)) {
            try {
                $data[] = new Competition($comp['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

}

?>
