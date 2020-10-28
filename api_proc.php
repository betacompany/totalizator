<?php

require_once dirname(__FILE__) . "/lib/local-properties.php";

if ($_REQUEST['access_key'] != ACCESS_KEY) {
    echo('Access denied');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/Match.php';
require_once dirname(__FILE__) . '/classes/Stake.php';

switch ($_REQUEST['action']) {
    case 'import_ext_match':
        $data = json_decode(file_get_contents("php://input"));
        echo $data['ext_id'];
}
