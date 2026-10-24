<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../lib/mysql.php';

/**
 * Description of Competitor
 *
 * @author ortemij
 */
class Competitor {
    private $id;
    private $ext_id;
    private $name;

    public function __construct($data) {
        if (is_scalar($data)) {
            $req = db_qw('SELECT * FROM `total_competitors` WHERE `id`=?', $data);
            if ($comp = db_fetch_assoc($req)) {
                $this->id = $comp['id'];
                $this->ext_id = $comp['ext_id'];
                $this->name = $comp['name'];
            } else {
                throw new Exception('No competitor with id=' . $data, 0);
            }
        } else if (is_array($data)) {
            $this->id = $data['id'];
            $this->ext_id = $data['ext_id'];
            $this->name = $data['name'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getExtId() {
        return $this->ext_id;
    }

    public function getName() {
        return $this->name;
    }

    public static function getAll() {
        $data = array();
        $req = db_qw('SELECT `id` FROM `total_competitors` WHERE 1=1 ORDER BY `name`');
        while ($c = db_fetch_assoc($req)) {
            try {
                $data[] = new Competitor($c['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

    public static function getOrCreateByExtId($ext_id, $name) {
        $req = db_qw('SELECT * FROM `total_competitors` WHERE `ext_id`=?', $ext_id);
        if ($comp = db_fetch_assoc($req)) {
            return new Competitor($comp);
        } else {
            db_qw(
                'INSERT INTO `total_competitors` SET `ext_id`=?, `name`=? ON DUPLICATE KEY UPDATE `name`=?',
                $ext_id, $name, $name
            );

            $req = db_qw('SELECT * FROM `total_competitors` WHERE `ext_id`=?', $ext_id);
            if ($comp = db_fetch_assoc($req)) {
                return new Competitor($comp);
            } else {
                throw new Exception('Unable to create non-existing competitor for ext_id=' . $ext_id);
            }
        }
    }
}

?>
