<?php

require_once 'lib/access.php';
if (!accessTest() || !(userid() == 1 || userid() == 10 || userid() == 9 || userid() == 60)) {
    echo 'Access denied';
    exit(0);
}

require_once 'classes/Match.php';
require_once 'classes/Stake.php';

switch ($_REQUEST['action']) {
    case 'add_match':
        $datetime = $_REQUEST['date'] . ' ' . $_REQUEST['time'] . ':00';
        Match::create($_REQUEST['comp_id'], $_REQUEST['comp1_id'], $_REQUEST['comp2_id'], $datetime);
        break;

    case 'end_match':
        try {
            $match = new Match($_REQUEST['match_id']);
            $match->finish($_REQUEST['score1'], $_REQUEST['score2']);
            $stakes = $match->getStakes();
            foreach ($stakes as $stake) {
                $stake->finish($match);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        break;
    case 'import_stakes':
        try {
            $file = $_FILES['stakes_file'];
            $tmp = $file['tmp_name'];
            //		echo $tmp, "<br/>";
            if (@is_uploaded_file($tmp)) {
                $fh = fopen($tmp, 'r');
                while ($data = fgets($fh)) {
                    //				echo $data, "<br/>";
                    $stems = explode("\t", $data);
                    $count = count($stems);
                    if ($count <= 1) {
                        continue;
                    }

                    $match_id = intval($stems[0]);
                    //				echo $match_id, "<br/>";

                    for ($i = 1; $i < $count; $i++) {
                        $stake_stems = explode("=", $stems[$i], 2);
                        $user_id = intval($stake_stems[0]);
                        $stake_data = $stake_stems[1];
                        $scores = explode(":", $stake_data, 2);
                        $score1 = intval($scores[0]);
                        $score2 = intval($scores[1]);

                        // Force make stake
                        Stake::make($user_id, $match_id, $score1, $score2, true);
                        //					echo "$user_id = $score1 : $score2 <br/>";
                    }
                }
                fclose($fh);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        break;
}

Header('Location: /cpanel.php#ok' . time());
exit(0);

?>
