<?php

require_once dirname(__FILE__) . "/lib/local-properties.php";

if ($_REQUEST['access_key'] != ACCESS_KEY) {
    echo('Access denied');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/Match.php';
require_once dirname(__FILE__) . '/classes/Competitor.php';

switch ($_REQUEST['action']) {
    case 'import_ext_match':
        $data = json_decode(file_get_contents("php://input"), true);
        
        $comp_id = $data['comp_id'];
        $ext_id = $data['ext_id'];
        $datetime = $data['datetime'];
        
        $comp1 = Competitor::getOrCreateByExtId($data['comp1']['ext_id'], $data['comp1']['name']);
        $comp2 = Competitor::getOrCreateByExtId($data['comp2']['ext_id'], $data['comp2']['name']);
        
        Match::getOrCreateByExtId($comp_id, $comp1->getId(), $comp2->getId(), $ext_id, $datetime);
        
        echo('OK ' . $comp1->getId() . ' vs ' . $comp2->getId());
        
    break;
}
