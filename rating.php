<?

require_once 'lib/access.php';
if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once 'classes/User.php';

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include 'templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="row">

        <div class="span2 offset1">
            <ul class="nav nav-pills nav-stacked">
                <li id="menu_0" class="active">
                    <a href="#" onclick="loadRating(0);">Общий</a>
                </li>
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

            </ul>

            <script type="text/javascript">loadRating(0);</script>
        </div>

        <div class="span8">
            <ol id="people"></ol>
        </div>
    </div>
</div>

<? require_once "templates/bottom.php"; ?>

</body>
</html>
