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
            <ul class="thumbnails">
            <? foreach ($data as $item) { ?>
                
                <li class="span3">
                    <img src="https://placehold.it/260x180" alt="">
                    <h3><?= $item['user']->getSNnbsp() ?></h3>
                </li>

            <? } ?>                
            </ul>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . '/templates/bottom.php'; ?>

</body>
</html>
