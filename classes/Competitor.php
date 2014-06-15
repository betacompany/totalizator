<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './lib/mysql.php';

/**
 * Description of Competitor
 *
 * @author ortemij
 */
class Competitor {
    private $id;
    private $name;

    public function __construct($id) {
	$req = mysql_qw('SELECT * FROM `total_competitors` WHERE `id`=?', $id);
	if ($comp = mysql_fetch_assoc($req)) {
	    $this->id =	    $comp['id'];
	    $this->name =   $comp['name'];
	} else {
	    throw new Exception('No competitior with id='.$id, 0);
	}
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }

    public static function getAll() {
	$data = array();
	$req = mysql_qw('SELECT `id` FROM `total_competitors` WHERE 1=1 ORDER BY `name`');
	while ($c = mysql_fetch_assoc($req)) {
	    try {
		$data[] = new Competitor($c['id']);
	    } catch (Exception $e) {}
	}

	return $data;
    }
}
?>
