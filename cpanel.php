<?php

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest() || !(userid() == 1 || userid() == 10 || userid() == 9 || userid() == 60)) {
    echo 'Access denied';
    exit(0);
}

require_once dirname(__FILE__) . '/classes/Competition.php';
require_once dirname(__FILE__) . '/classes/Competitor.php';


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <style type="text/css">
        #end_match input[type=text] {
            width: 20px;
        }
    </style>
</head>
<body>
<h1>Добавить матч</h1>

<form action="/cpanel_proc.php" method="post">
    <input type="hidden" name="action" value="add_match"/>

    <select name="comp_id">
        <?
        $comps = Competition::getAll();
        foreach ($comps as $comp) {
            ?>
            <option value="<?= $comp->getId() ?>"><?= $comp->getName() ?></option>
        <?
        }
        ?>
    </select> <br/>

    <select name="comp1_id">
        <?
        $comps = Competitor::getAll();
        foreach ($comps as $comp) {
            ?>
            <option value="<?= $comp->getId() ?>"><?= $comp->getName() ?></option>
        <?
        }
        ?>
    </select>

    vs

    <select name="comp2_id">
        <?
        $comps = Competitor::getAll();
        foreach ($comps as $comp) {
            ?>
            <option value="<?= $comp->getId() ?>"><?= $comp->getName() ?></option>
        <?
        }
        ?>
    </select> <br/>

    Дата (ГГГГ-ММ-ДД): <input type="text" name="date"/>
    Время (ЧЧ:ММ): <input type="text" name="time"/> <br/>

    <input type="submit" value="Добавить"/>
</form>

<h1>Завершить матч</h1>

<form id="end_match" action="/cpanel_proc.php">
    <input type="hidden" name="action" value="end_match"/>

    <select name="match_id">
        <?
        $matches = Match::getActive();
        foreach ($matches as $match) {
            ?>
            <option value="<?= $match->getId() ?>"><?= $match->getName() ?></option>
        <?
        }
        ?>
    </select> <br/>

    Счёт: <input type="text" name="score1"/>:<input type="text" name="score2"/>
    <br/>

    <input type="submit" value="Завершить">
</form>

<h1>Импортировать ставки</h1>

<h3>Формат файла:</h3>
	<pre>
match_id \t uid=stake \t uid=stake ...
...
	</pre>
<h3>Пример файла:</h3>
	<pre>
69	1=1:0	2=3:3	4=5:5
71	2=2:2
29	1=1:1	4=5:3
	</pre>
<form action="/cpanel_proc.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="import_stakes"/>
    <input type="file" name="stakes_file"/>
    <input type="submit" value="Импортировать"/>
</form>

</body>
</html>
