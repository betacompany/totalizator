<?php

function show_match(Match $match, $uid) {
	$stake = Stake::getByMatchIdAndUID($match->getId(), $uid);
	$withMy = $stake != null;
	$withoutMy = !$withMy;
	$scoresAttr = "data-zero=\"true\"";
	if ($stake) {
		switch ($stake->getScore()) {
			case 4: $scoresAttr = "data-four=\"true\""; break;
			case 2: $scoresAttr = "data-two=\"true\""; break;
			case 1: $scoresAttr = "data-one=\"true\""; break;
		}
	}
?>
<li class="well" <?if ($withMy) {?>data-my="true"<?}?> <?if ($withoutMy) {?>data-without-my="true"<?}?> <?=$scoresAttr?>>
	<h2>
		<?=$match->getName()?>
		<small class="pull-right">
			<?=$match->getCompName()?>,
			<?=$match->getTime()?>
			(<?=$match->getId()?>)
		</small>
	</h2>

	<div class="menu">
		<? inner_menu($match, $uid); ?>

	</div>

	<div class="response"></div>
</li>
<?
}

function inner_menu(Match $match, $uid) {
	$stake = Stake::getByMatchIdAndUID($match->getId(), $uid);
?>
	<? if ($match->isAvailableFor($uid)): ?>
	<form action="processor.php" class="form-inline">
		<div class="controls">
			<input type="hidden" name="action" value="make_stake"/>
			<input type="hidden" name="match_id" value="<?=$match->getId()?>"/>
			<label class="control-label">Ваша ставка:</label>
			<input name="score1" type="text" class="input-mini" maxlength="2" size="2" placeholder="<?=$match->getCompetitor1()->getName()?>"/>
			<span>:</span>
			<input name="score2" type="text" class="input-mini" maxlength="2" size="2" placeholder="<?=$match->getCompetitor2()->getName()?>"/>
			<button type="button" class="btn" onclick="make(this);">Сделать ставку!</button>
		</div>
	</form>
	<? else: ?>
	<div class="row-fluid">
		<div class="span4">
			<ul class="unstyled">
				<? if ($match->isPlayed()): ?>
				<li>Исход: <strong><?=$match->getScore()?></strong></li>
				<? endif; ?>
				<li>Коэффициенты: <?=$match->getForecast()?></li>
				<? if ($stake): ?>
				<li>Ваша ставка: <strong><?=$stake->getStakeScore()?></strong>
					<? if ($stake->isPlayed()): ?>
						(<strong><?=$stake->getScore()?></strong>)
						<? endif; ?></li>
				<? endif; ?>
			</ul>
		</div>
		<div class="span8">
			<div onclick="showStakes(<?=$match->getId()?>, this)" class="pull-right btn dropdown-toggle stakes-btn">
				Все ставки
				<span class="caret"></span>
			</div>
		</div>
	</div>
	<? endif; ?>
<?
}