<?php

require_once dirname(__FILE__) . "/lib/local-properties.php";

if ($_REQUEST['access_key'] != ACCESS_KEY) {
    die('Access denied');
}

require_once dirname(__FILE__) . '/classes/Match.php';
require_once dirname(__FILE__) . '/classes/Stake.php';

switch ($_REQUEST['action']) {
    case 'import_ext_match':
        $data = json_decode(http_get_request_body());
        echo $data['ext_id'];
}
