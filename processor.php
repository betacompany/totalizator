<?php

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest()) {
    echo '<error>access violation</error>';
    exit(0);
}

require_once dirname(__FILE__) . '/classes/Match.php';
require_once dirname(__FILE__) . '/classes/Stake.php';
require_once dirname(__FILE__) . '/classes/User.php';
require_once dirname(__FILE__) . '/templates/templates.php';

switch ($_REQUEST['action']) {
    case 'make_stake':
        try {
            $match = new Match($_REQUEST['match_id']);
            $match->makeStake(userid(), $_REQUEST['score1'], $_REQUEST['score2']);
            inner_menu($match, userid());
            exit(0);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        break;

    case 'get_stakes':
        function out($title, $stakes)
        {
            echo '<div class="span4">';
            $count = count($stakes);
            echo "<h6>$title ($count)</h6>";
            echo '<ul class="unstyled">';
            foreach ($stakes as $stake) {
                $mine = $stake->getUID() == userid();
                echo "<li class=\"{$stake->getType()}\">" . ($mine ? "<b>" : "") . username($stake->getUID()) . ': ' . $stake->getStakeScore() . (($stake->isPlayed()) ? ' (' . $stake->getScore() . ')' : '') . ($mine ? "</b>" : "") . '</li>';
            }
            echo '</ul></div>';
        }

        function filter($all, $type)
        {
            $result = array();
            foreach ($all as $item) {
                if ($item->getType() == $type) {
                    $result[] = $item;
                }
            }
            return $result;
        }

        try {
            $match = new Match($_REQUEST['match_id']);
            if (!$match->isAvailableFor(userid())) {
                $stakes = Stake::getByMatchId($_REQUEST['match_id']);
                echo '<div class="row-fluid">';
                out("{$match->getCompetitor1()->getName()} победит", filter($stakes, Stake::WIN1));
                out("ничья", filter($stakes, Stake::DRAW));
                out("{$match->getCompetitor2()->getName()} победит", filter($stakes, Stake::WIN2));
                echo '</div>';
            } else {
                echo 'Атата!';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        break;

    case 'get_matches':
        $matches = array();

        switch ($_REQUEST['type']) {
            case 'available':
                $matches = Match::getAvailable();
                break;
            case 'active':
                $matches = Match::getActive();
                break;
            case 'played':
                $matches = Match::getPlayed(20);
                break;
        }

        if (!empty ($matches)) {
            ?>
            <!--<u onClick="javascript: openAllMatches(this)">Развернуть все</u>-->
            <ul>
                <?
                foreach ($matches as $match) {
                    show_match($match, userid());
                }
                ?>
            </ul>
            <? if ($_REQUEST['type'] == 'played'): ?>
                <div class="alert alert-info">И ещё много-много матчей...</div>
            <? endif; ?>
            <?
        } else {
            echo '<div class="alert alert-info">Матчей нет</div>';
        }
        break;

    case 'load_stakes_for_uid':
        $user = new User($_REQUEST['quid']);
        $stakes = $user->getPlayedStakes($_REQUEST['comp_id']);
        foreach ($stakes as $stake) {
            $match = new Match($stake->getMatchId());
            if (!isset($_REQUEST['full'])) {
                echo '<li>' . $match->getName() . ':<br/><small>Счёт: ' . $match->getScore() . '; Ставка: ' . $stake->getStakeScore() . ' ' . (($stake->getScore() > 0) ? '<b>' : '') . '(' . $stake->getScore() . ')' . (($stake->getScore() > 0) ? '</b>' : '') . '</small></li>';
            } else {
                show_match($match, $user->getId());
            }
        }
        break;

    case 'load_rating':
        $users = User::getAllByRating($_REQUEST['comp_id']);
        foreach ($users as $user) {
            $cur_uid = $user['user']->getId();
            ?>
            <li>
                <? if ($cur_uid == userid()) { ?><strong><? } ?>
                    <a href="#"
                       onClick="userClick(this, <?= $cur_uid ?>, <?= $_REQUEST['comp_id'] ?>);"><?= $user['user']->getSNnbsp() ?></a>
                    <? if ($cur_uid == userid()) { ?></strong><? } ?>
                (<?= $user['scores'] ?>)
                <? if ($user['sort_info'] != "") { ?> (<?= $user['sort_info'] ?>) <? } ?>
                <ul style="display: none;" id="stakes_user_<?= $cur_uid ?>"></ul>
            </li>
            <?
        }
        break;
}

?>
