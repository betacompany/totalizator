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

    public function __construct($id) {
        $competitor = Competitor::getById($id);
        if ($competitor != null) {
            return $competitor;
        } else {
            throw new Exception('No competitor with id=' . $id, 0);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getExtId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public static function getAll() {
        $data = array();
        $req = mysql_qw('SELECT `id` FROM `total_competitors` WHERE 1=1 ORDER BY `name`');
        while ($c = mysql_fetch_assoc($req)) {
            try {
                $data[] = new Competitor($c['id']);
            } catch (Exception $e) {
            }
        }

        return $data;
    }

    public static function getById($id) {
        $req = mysql_qw('SELECT * FROM `total_competitors` WHERE `id`=?', $id);
        if ($comp = mysql_fetch_assoc($req)) {
            return Competitor::fill($comp);
        } else {
            return null;
        }
    }

    public static function getOrCreateByExtId($ext_id, $name) {
        $req = mysql_qw('SELECT * FROM `total_competitors` WHERE `ext_id`=?', $ext_id);
        if ($comp = mysql_fetch_assoc($req)) {
            return Competitor::fill($comp);
        } else {
            mysql_qw(
                'INSERT INTO `total_competitors` SET `ext_id`=?, `name`=? ON DUPLICATE KEY UPDATE `name`=?',
                $ext_id, $name, $name
            );

            $req = mysql_qw('SELECT * FROM `total_competitors` WHERE `ext_id`=?', $ext_id);
            if ($comp = mysql_fetch_assoc($req)) {
                return Competitor::fill($comp);
            } else {
                throw new Exception('Unable to create non-existing competitor for ext_id=' . $ext_id);
            }
        }
    }

    private static function fill($comp) {
        $compObj = new Competitor();
        $compObj->id = $comp['id'];
        $compObj->ext_id = $comp['ext_id'];
        $compObj->name = $comp['name'];
        return $compObj;
    }
}

?>
