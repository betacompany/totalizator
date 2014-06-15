<?php

require_once dirname(__FILE__) . "/local-properties.php";

require_once dirname(__FILE__) . "/mysql.php";

require_once PATH_TO_COMMON_AUTH;

function userid() {
    global $_COMMON_USER_ID;
    return $_COMMON_USER_ID;
}

function username($uid) {
    $user = CommonAuth::getData($uid);
    if ($user) {
        return htmlspecialchars("{$user['name']} {$user['surname']}");
    }

    return "Некто";
}

function accessTest() {
    return userid() > 0;
}

?>
