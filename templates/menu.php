<?php

$menu[0]['url'] = '/main.php';
$menu[0]['title'] = 'Список матчей';

$menu[1]['url'] = '/archive.php';
$menu[1]['title'] = 'Архив ставок';

$menu[2]['url'] = '/rating.php';
$menu[2]['title'] = 'Рейтинг участников';

$menu[3]['url'] = '/rules.php';
$menu[3]['title'] = 'Правила';
?>

<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav span3">
                <li>
                    <a href="/" class="brand">Тотализатор пайпосайта <sup>&beta;</sup></a>
                </li>
            </ul>
            <ul class="nav">
                <?
                if ($_SERVER['SCRIPT_NAME'] != '/index.php') {
                    foreach ($menu as $item) {
                        ?>
                        <li<? if ($item['url'] == $_SERVER['SCRIPT_NAME']) { ?> class="active"<? } ?>>
                            <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                        </li>
                    <?
                    }
                }
                ?>
            </ul>
            <ul class="nav pull-right">
                <li class="pull-right">
                    <a title="<?= username(userid()) ?>"
                       href="<?= MAIN_SITE_URL ?>/authorize.php?method=sign_out">Выйти</a>
                </li>
                <li class="divider-vertical"></li>
                <li>
                    <a href="http://pipeinpipe.info" target="_blank">Pipeinpipe.info</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Внимание!</strong>
    Мы опять коварно изменили правила: теперь всегда ставим на 90 минут,
    даже в случае дополнительного времени!
</div>
