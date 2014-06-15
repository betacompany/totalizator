<?

require_once 'lib/access.php';

if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once 'classes/Match.php';
require_once 'classes/Stake.php';

?><!DOCTYPE html>
<html lang="ru">

<? include 'templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="row">
        <div class="span8 offset3">
            <div class="top_m clearfix">
                <div class="btn-group pull-left">
                    <div class="btn active" onclick="filter(this, '#matches > ul li')">Все матчи</div>
                    <div class="btn" onclick="filter(this, '#matches > ul > li', 'data-without-my')">Без моих ставок
                    </div>
                    <div class="btn" onclick="filter(this, '#matches > ul > li', 'data-my')">С моими ставками</div>
                </div>
                <div class="btn pull-left" style="margin-left: 10px;" onclick="refreshMatches()">
                    <i class="icon-refresh"></i>
                </div>
                <div id="show_all" class="btn pull-right dropdown-toggle" onclick="openAllMatches(this)">
                    Развернуть все
                    <span class="caret"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="span2 offset1">
            <ul class="nav nav-pills nav-stacked">
                <li id="menu_available" class="active">
                    <a href="#" onClick="loadMatches('available');">
                        Доступные
                        <!--						<span class="badge badge-inverse pull-right"></span>-->
                    </a>
                </li>
                <li id="menu_active">
                    <a href="#" onClick="loadMatches('active');">Активные</a>
                </li>
                <li id="menu_played">
                    <a href="#" onClick="loadMatches('played');">Сыгранные</a>
                </li>
            </ul>
        </div>
        <div class="span8">
            <div id="matches"></div>
        </div>
    </div>

    <script type="text/javascript">loadMatches('available');</script>
</div>

<? require_once "templates/bottom.php"; ?>

</body>
</html>
