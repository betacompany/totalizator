<?

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/User.php';

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include dirname(__FILE__) . '/templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="row">

        <div class="span2 offset1">
            <ul class="nav nav-pills nav-stacked">
                <?
                $competitions = Competition::getAll();
                foreach ($competitions as $competition) {
                    ?>
                    <li id="menu_<?= $competition->getId() ?>">
                        <a href="#"
                           onclick="loadRating(<?= $competition->getId() ?>);"><?= $competition->getName() ?></a>
                    </li>
                <?
                }
                ?>
                <li id="menu_0" class="active">
                    <a href="#" onclick="loadRating(0);">Общий</a>
                </li>
            </ul>

            <script type="text/javascript">loadRating(<?=$competitions[0]->getId()?>);</script>
        </div>

        <div class="span8">
            <ol id="people"></ol>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . '/templates/bottom.php'; ?>

</body>
</html>
