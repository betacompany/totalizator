<?

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/User.php';
require_once dirname(__FILE__) . '/classes/Leaderboard.php';

$data = Leaderboard::getGloryHall();

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include dirname(__FILE__) . '/templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="row">
        <div class="span8 offset2">
            <? 
            foreach ($data as $item) {
                echo $item['user']->getSNnbsp();
            } 
            ?>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . '/templates/bottom.php'; ?>

</body>
</html>
