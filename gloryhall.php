<?

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/User.php';
require_once dirname(__FILE__) . '/classes/Leaderboard.php';

$data = Leaderboard::getGloryHall();

$upper_bracket = array();
$middle_bracket = array();
$lower_bracket = array();

$maxCount = max(array_column($data, 'count'));
$minCount = min(array_column($data, 'count'));

foreach ($data as $item) {
    if ($item['count'] == $maxCount) {
        $upper_bracket[] = $item;
    } elseif ($item['count'] == $minCount) {
        $lower_bracket[] = $item;
    } else {
        $middle_bracket[] = $item;
    }
}

function compile_row($item) {
    return $item['user']->getSNnbsp() . " " . str_repeat("&#127942;", $item['count']);
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include dirname(__FILE__) . '/templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="row" style="margin-top: 100px; margin-bottom: 100px">
        <div class="span4 offset4">
            <? foreach ($upper_bracket as $item) { ?>
            <h1><?= compile_row($item) ?></h1>
            <? } ?>
            <? foreach ($middle_bracket as $item) { ?>
            <h2><?= compile_row($item) ?></h2>
            <? } ?>
            <? foreach ($lower_bracket as $item) { ?>
            <h3><?= compile_row($item) ?></h3>
            <? } ?>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . '/templates/bottom.php'; ?>

</body>
</html>
