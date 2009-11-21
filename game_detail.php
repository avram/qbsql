<?php
/* Show details for a particular round
    GET variables:
        game
*/
 require "init.php";			// set up (connect to DB, etc)
  $title="Game Detail";
  
 // First, see what we've got.
 if(isset($_GET["game"]) && is_numeric($_GET["game"])) {
    $id = $_GET["game"];
    $res = query("SELECT t1.short_name AS t1_short,
                        t1.full_name AS t1_full,
                        t1.id AS t1_id,
                        rounds.score1 AS t1_score,
                        t2.short_name AS t2_short,
                        t2.full_name AS t2_full,
                        t2.id AS t2_id,
                        rounds.score2 AS t2_score,
                        rounds.id as id,
                        rounds.forfeit as forfeit
                    FROM {$mysql_prefix}_rounds AS rounds,
                        {$mysql_prefix}_teams AS t1,
                        {$mysql_prefix}_teams AS t2
                    WHERE rounds.game_id = '$id'
                        AND t1.id = rounds.team1
                        AND t2.id = rounds.team2
                    LIMIT 1");
    if($round = fetch_row($res)) {
        list($t1, $f1, $id1, $sc1, $t2, $f2, $id2, $sc2, $rid, $forfeit) = $round;
        $title .= ": $t1 vs. $t2, Round $rid";
        // see who won
        if ($sc1 > $sc2 || $forfeit == $id2) {
            $winner = array($t1, $f1, $id1, $sc1);
            $loser = array($t2, $f2, $id2, $sc2);
        } elseif ($sc2 > $sc1 || $forfeit == $id1) {
            $loser = array($t1, $f1, $id1, $sc1);
            $winner = array($t2, $f2, $id2, $sc2);
        } else {
            $draw = true;
            $winner = array($t1, $f1, $id1, $sc1);
            $loser = array($t2, $f2, $id2, $sc2);
        }
    } else {
        $error = "No game found for given game number.";
    }
 } else { // don't have necessary input
    $error = "No game number provided.";
}
  
 require "head.php";
 if ($error)
    warning($error, "Go to \"Round Summaries\"", "stats_round.php");
 else { // continue

if($auth) {
    print "<p><a href='add_game.php?edit=$id&t=$mysql_prefix'>Edit Round</a></p>";
}

// set up styles for winner and loser
$winstyle = "winner";
$wintext = "W";
$loserstyle = "loser";
$losertext = (!$forfeit) ? "L" : "Forfeit";
if($draw) {
    $winstyle = "draw";
    $loserstyle = "draw";
    $wintext = "D";
    $losertext = "D";
}

// round data
if(!$forfeit){
$w_query = "SELECT SUM(rp.tossups),
                SUM(rp.negs),
                SUM(rp.powers),
                FORMAT(($winner[3]-SUM(10*rp.tossups+15*rp.powers-5*rp.negs))/SUM(rp.tossups+rp.powers),2),
                SUM(rp.tu_heard),
                SUM(10*rp.tossups+15*rp.powers-5*rp.negs),
                FORMAT(SUM(rp.powers)/SUM(rp.negs),2)
            FROM {$mysql_prefix}_rounds_players AS rp
            WHERE rp.game_id = '$id'
                AND rp.team_id = '$winner[2]' ";
$l_query = "SELECT SUM(rp.tossups),
                SUM(rp.negs),
                SUM(rp.powers),
                FORMAT(($loser[3]-SUM(10*rp.tossups+15*rp.powers-5*rp.negs))/SUM(rp.tossups+rp.powers),2),
                SUM(rp.tu_heard),
                SUM(10*rp.tossups+15*rp.powers-5*rp.negs),
                FORMAT(SUM(rp.powers)/SUM(rp.negs),2)
            FROM {$mysql_prefix}_rounds_players AS rp
            WHERE rp.game_id = '$id'
                AND rp.team_id = '$loser[2]' ";

        $w_res = query($w_query) or die(mysql_error());
        $l_res = query($l_query) or die(mysql_error());
        list($wtups, $wnegs, $wpows, $wbconv, $wtuh, $wtot, $wpn) = fetch_row($w_res);
        list($ltups, $lnegs, $lpows, $lbconv, $ltuh, $ltot, $lpn) = fetch_row($l_res);
        
        // fetch player data
        $w_query = "SELECT CONCAT(p.first_name, ' ', p.last_name),
                             p.id,
                             rp.tu_heard,
                             rp.powers,
                             rp.tossups,
                             rp.negs,
                             FORMAT(rp.powers/rp.negs,2),
                             rp.powers*15+rp.tossups*10-rp.negs*5
                        FROM {$mysql_prefix}_players AS p,
                            {$mysql_prefix}_rounds_players AS rp
                        WHERE p.id=rp.player_id AND rp.game_id='$id'
                            AND rp.team_id = '$winner[2]'
                            AND rp.tu_heard > 0
                        ORDER BY p.last_name, p.first_name";


        $w_res = query($w_query) or die(mysql_error());
        $l_query = "SELECT CONCAT(p.first_name, ' ', p.last_name),
                             p.id,
                             rp.tu_heard,
                             rp.powers,
                             rp.tossups,
                             rp.negs,
                             FORMAT(rp.powers/rp.negs,2),
                             rp.powers*15+rp.tossups*10-rp.negs*5
                        FROM {$mysql_prefix}_players AS p,
                            {$mysql_prefix}_rounds_players AS rp
                        WHERE p.id=rp.player_id AND rp.game_id='$id'
                            AND rp.team_id = '$loser[2]'
                            AND rp.tu_heard > 0
                        ORDER BY p.last_name, p.first_name";
        $l_res = query($l_query) or die(mysql_error());
}
?>
	 <table>
	  <thead>
	   <tr class="<?=$winstyle?>">
	    <th colspan="7" class="team"><?=link_team($winner[1], $winner[2])?> (<?=$wintext?>)</th>
	   </tr>
	   <tr>
	    <th>Name</th>
	    <th>TUH</th>
	    <th>Pow</th>
	    <th>TU</th>
	    <th>Neg</th>
	    <th>P/N</th>
	    <th>Pts</th>
	   </tr>
	  </thead>
	  <tbody>
<?php
	if(!$forfeit) {
     while(list($name,$id, $tuh, $pow, $tup, $neg, $pn, $tot) = fetch_row($w_res)){
	 echo "<tr>
	     <td>".link_player($name,$id)."</td>
	     <td>$tuh</td>
	     <td>$pow</td>
	     <td>$tup</td>
	     <td>$neg</td>
	     <td>$pn</td>
	     <td>$tot</td>
	     </tr>";
     }
     echo "<tr class='total'>
        <td>Total</td>
        <td>$wtuh</td>
        <td>$wpows</td>
        <td>$wtups</td>
        <td>$wnegs</td>
        <td>$wpn</td>
        <td>$wtot</td>
     </tr>";
?>
     <tr class="total">
        <td colspan="3"></td>
        <th>BConv</th>
	    <td><?=$wbconv?></td>
	    <th>Points</th>
	    <td><?=$winner[3]?></td>
	   </tr>
<?php  } else { // end of non-forfeit clause ?>
	<tr><td colspan="7">No player statistics.</td></tr>
<?php } // end of forfeit clause ?>
</tbody>
<tbody><tr class="empty"><td colspan="7"></td></tr></tbody>
	  <thead>
	   <tr class="<?=$loserstyle?>">
	    <th colspan="7" class="team"><?=link_team($loser[1],$loser[2])?> (<?=$losertext?>)</th>
	   </tr>
	   <tr>
	    <th>Name</th>
	    <th>TUH</th>
	    <th>Pow</th>
	    <th>TU</th>
	    <th>Neg</th>
	    <th>P/N</th>
	    <th>Pts</th>
	   </tr>
	  </thead>
	  <tbody>
<?php
	if (!$forfeit) {
     while(list($name,$id, $tuh, $pow, $tup, $neg, $pn, $tot) = fetch_row($l_res)){
	 echo "<tr>
	     <td>".link_player($name,$id)."</td>
	     <td>$tuh</td>
	     <td>$pow</td>
	     <td>$tup</td>
	     <td>$neg</td>
	     <td>$pn</td>
	     <td>$tot</td>
	     </tr>";
     }
    echo "<tr class='total'>
        <td>Total</td>
        <td>$ltuh</td>
        <td>$lpows</td>
        <td>$ltups</td>
        <td>$lnegs</td>
        <td>$lpn</td>
        <td>$ltot</td>
     </tr>";
?>
     <tr class="total">
        <td colspan="3"></td>
        <th>BConv</th>
	    <td><?=$lbconv?></td>
	    <th>Points</th>
	    <td><?=$loser[3]?></td>
	   </tr>
<?php  } else { // end of non-forfeit clause ?>
	<tr><td colspan="7">No player statistics.</td></tr>
<?php } // end of forfeit clause ?>
     </tbody>
    </table>
<?php
 }
 require "foot.php";
