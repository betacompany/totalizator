<?

require_once 'lib/access.php';
if (!accessTest()) {
	Header('Location: /index.php?code=69');
	exit(0);
}

require_once 'classes/Match.php';
require_once 'classes/Stake.php';
require_once 'templates/templates.php';

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include 'templates/head.php'; ?>

<body>

<div class="container">
	<? include 'templates/menu.php'; ?>

	<div class="row">
		<div class="span8 offset3">
			<div class="top_m clearfix">
				<div class="btn-group pull-left">
					<div class="btn active" onclick="filter(this, '#matches > ul li')">Все матчи</div>
					<div class="btn" onclick="filter(this, '#matches > ul > li', 'data-four')">4 очка</div>
					<div class="btn" onclick="filter(this, '#matches > ul > li', 'data-two')">2 очка</div>
					<div class="btn" onclick="filter(this, '#matches > ul > li', 'data-one')">1 очко</div>
					<div class="btn" onclick="filter(this, '#matches > ul > li', 'data-zero')">0 очков</div>
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
				<li id="menu_0" class="active">
					<a href="#" onclick="loadStakesForUserFull(<?=userid()?>, 0);">Общий</a>
				</li>
				<?
				$competitions = Competition::getAll();
				foreach ($competitions as $competition) {
					?>
					<li id="menu_<?=$competition->getId()?>">
						<a href="#"
						   onclick="loadStakesForUserFull(<?=userid()?>, <?=$competition->getId()?>);"><?=$competition->getName()?></a>
					</li>
					<?
				}
				?>

			</ul>

			<script type="text/javascript">loadStakesForUserFull(<?=userid()?>, 0);</script>
		</div>

		<div id="matches" class="span8"><ul></ul></div>
	</div>
</div>

<? require_once "templates/bottom.php"; ?>

</body>
</html>
