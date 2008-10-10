<?php
/* add_game.php
 *
 * Enter results from a game and edit extant games. 
 *
 * Author: Avram Lyon 
 * Created: 21 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 require "require_admin.php";		// not just anyone can use this
 $title="Add game";
 require "head.php";			// Generate header as appropriate

 // if we have two distinct teams and round, we should show the stats screen
 if(isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
 	if($_POST["confirm"] == "yes") {
 		// get game info so we can tell them what we did
 		$query = "SELECT t1.full_name, t2.full_name, rounds.id" .
 				" FROM {$mysql_prefix}_rounds AS rounds," .
 				" 	{$mysql_prefix}_teams AS t1," .
 				"	{$mysql_prefix}_teams AS t2" .
 				" WHERE t1.id=rounds.team1 AND t2.id=rounds.team2" .
 				"	AND rounds.game_id=$_GET[delete] LIMIT 1";
 		$res = query($query) or die("Failed with query: <pre>$query \n error: ".mysql_error()."</pre>");
 		list($team1, $team2, $round) = fetch_row($res);
 		free_result($res);
 		$round_del = "DELETE FROM {$mysql_prefix}_rounds WHERE game_id='$_GET[delete]' LIMIT 1";
 		$indiv_del = "DELETE FROM {$mysql_prefix}_rounds_players WHERE game_id='$_GET[delete]'";
 		query($round_del) or die("Failed to delete round data; db should be untouched: ".mysql_error());
 		query($indiv_del) or die("Failed to delete indiv data; there may be orphaned rows in rounds_players: ".mysql_error());  
 		print "<p class='message'>Deleted round $round, $team1 vs. $team2</p>";
 	} else {
 		$_GET["edit"] = $_GET["delete"];
 	}
 }
 if(isset($_GET["modify"]) && is_numeric($_GET["modify"])) {
 	// all the new round info should be in the POST arrays
 	// we go through the POST array and check the data
 	$round_valid = (is_numeric($_POST["team1_score"]) && is_numeric($_POST["team2_score"])
 			&& is_numeric($_POST["total_tuh"]));
 			
 	$round_query = "UPDATE {$mysql_prefix}_rounds SET score1='$_POST[team1_score]', " .
 				"	score2='$_POST[team2_score]', tu_heard='$_POST[total_tuh]' " .
 				"		WHERE game_id='$_GET[modify]' LIMIT 1";
 	$players_valid = TRUE;
 	for($i = 0; $i < $_POST["team1_size"]; $i++) {
 		$players_valid = ($players_valid && is_numeric($_POST["team1_pid"][$i])
 							&& is_numeric($_POST["team1_tuh"][$i])
 							&& is_numeric($_POST["team1_pow"][$i])
 							&& is_numeric($_POST["team1_tup"][$i])
 							&& is_numeric($_POST["team1_pid"][$i])
 							&& is_numeric($_POST["team1_neg"][$i]));
 		$player1_query[$i] = "UPDATE {$mysql_prefix}_rounds_players SET" .
 				"				tu_heard='{$_POST[team1_tuh][$i]}', powers='{$_POST[team1_pow][$i]}'," .
 				"				tossups='{$_POST[team1_tup][$i]}', negs='{$_POST[team1_neg][$i]}' " .
 				"			WHERE game_id='$_GET[modify]' AND player_id='{$_POST[team1_pid][$i]}' LIMIT 1";
 	}
 	for($i = 0; $i < $_POST["team2_size"]; $i++) {
 		$players_valid = ($players_valid && is_numeric($_POST["team2_pid"][$i])
 							&& is_numeric($_POST["team2_tuh"][$i])
 							&& is_numeric($_POST["team2_pow"][$i])
 							&& is_numeric($_POST["team2_tup"][$i])
 							&& is_numeric($_POST["team2_pid"][$i])
 							&& is_numeric($_POST["team2_neg"][$i]));
 		$player2_query[$i] = "UPDATE {$mysql_prefix}_rounds_players SET" .
 				"				tu_heard='{$_POST[team2_tuh][$i]}', powers='{$_POST[team2_pow][$i]}'," .
 				"				tossups='{$_POST[team2_tup][$i]}', negs='{$_POST[team2_neg][$i]}' " .
 				"			WHERE game_id='$_GET[modify]' AND player_id='{$_POST[team2_pid][$i]}' LIMIT 1";
 	}
 	if($round_valid && $players_valid) {
 		//print "<pre>$round_query\n";
 		query($round_query) or die(mysql_error());
 		for($k=0; $k < $_POST["team1_size"]; $k++) {
 			//print "$player1_query[$k]\n";
 			query($player1_query[$k]) or die(mysql_error());
 		}
  		for($l=0; $l < $_POST["team2_size"]; $l++) {
 			//print "$player2_query[$l]\n";
 			query($player2_query[$l]) or die(mysql_error());
  		}
  		//print "</pre>";	
 	} else {
 		print "Invalid inputs";
 	}
 }
 if (($_GET["team1"] != $_GET["team2"]) && $_GET["round"]) {
     $round = $_GET["round"];		// move GET vars to normal vars.
     $team1_id = $_GET["team1"];
     $team2_id = $_GET["team2"];

     // pull team names from DB
     list($team1) = fetch_row(query("SELECT full_name FROM "."$mysql_prefix"."_teams WHERE id=\"$team1_id\" LIMIT 1"));
     list($team2) = fetch_row(query("SELECT full_name FROM "."$mysql_prefix"."_teams WHERE id=\"$team2_id\" LIMIT 1"));
     
     // pull rosters from DB
     $res1 = query("SELECT last_name,first_name,id FROM "."$mysql_prefix"."_players WHERE team=\"$team1_id\" ORDER BY last_name");
     
     $res2 = query("SELECT last_name,first_name,id FROM "."$mysql_prefix"."_players WHERE team=\"$team2_id\" ORDER BY last_name");
     
     echo "
	 <h2>Round $round, $team1 vs. $team2</h2>
	 <form action=\"add_game.php?submit=bork&t=$mysql_prefix\" method=\"POST\">
	 <h3>Scores</h3>
	 <p>$team1: <input type=\"text\" size=\"5\" name=\"team1_score\" />
	 $team2: <input type=\"text\" size=\"5\" name=\"team2_score\" />
	 <input type=\"hidden\" name=\"round\" value=\"$round\" />
	 <input type=\"hidden\" name=\"team1_id\" value=\"$team1_id\" />
	 <input type=\"hidden\" name=\"team2_id\" value=\"$team2_id\" />
 	 </p>
	 <p>Toss-ups heard: <input type=\"text\" size=\"5\" name=\"total_tuh\" value=\"$tourney_game_length\" /></p>
	 <h3>Individual scores</h3>
	 <table>
	  <thead>
	   <tr>
	    <th colspan=\"5\">$team1</th>
	   </tr>
	   <tr>
	    <th>Name</th>
	    <th>TUH</th>
	    <th>Pow</th>
	    <th>TU</th>
	    <th>Neg</th>
	   </tr>
	  </thead>
	  <tbody>";
     $l = 0;
     while(list($team1_last,$team1_first,$team1_id_num) = fetch_row($res1)){
	 echo "<tr>
	     <td>$team1_first $team1_last <input type=\"hidden\" name=\"team1_id_num[]\" value=\"$team1_id_num\" /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_tuh[]\" value=\"$tourney_game_length\" /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_pow[]\" /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_tu[]\" /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_neg[]\" /></td>
	     </tr>";
	 $l++;
     }
     echo "</tbody>
	 <input type=\"hidden\" name=\"team1_size\" value=\"$l\" />
          <thead>
           <tr>
            <th colspan=\"5\">$team2</th>
           </tr>
           <tr>
            <th>Name</th>
            <th>TUH</th>
            <th>Pow</th>
            <th>TU</th>
            <th>Neg</th>
           </tr>
          </thead>
          <tbody>";
     $k = 0;
     while(list($team2_last,$team2_first,$team2_id_num) = fetch_row($res2)){
         echo "<tr>
             <td>$team2_first $team2_last <input type=\"hidden\" name=\"team2_id_num[]\" value=\"$team2_id_num\" /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_tuh[]\" value=\"$tourney_game_length\" /></td>             
	     <td><input type=\"text\" size=\"5\" name=\"team2_pow[]\" /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_tu[]\" /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_neg[]\" /></td>
             </tr>";
	 $k++;
     }
     echo "</tbody>
         </table>
	 <input type=\"hidden\" value=\"$k\" name=\"team2_size\" />
	 <input type=\"submit\" value=\"Submit game\" />
	 </form>";
    mysql_free_result($res1);
    mysql_free_result($res2);
 } else if ($_GET["submit"]=="bork") {
     // check data integrity
     $integrity = (is_numeric($_POST["team1_id"]) && is_numeric($_POST["team2_id"])
          && is_numeric($_POST["team1_score"]) && is_numeric($_POST["team2_score"])
              && is_numeric($_POST["total_tuh"]) && is_numeric($_POST["round"]));
     if (!$integrity)
         die("Invalid scores. Go back and try again.");

     // check logical validity
     // distinct teams, no previous games this round
     if($_POST["team1_id"] != $_POST["team2_id"]) {
         $query = "SELECT COUNT(*) FROM {$mysql_prefix}_rounds
             WHERE (team1='$_POST[team1_id]' OR team2='$_POST[team2_id]') AND id='$_POST[round]'";
         $res = query($query);
         list($count) = fetch_row($res);
         if($count == 0)
             $logic = true;
         else
             die("One or more of the teams already has a game entered for this round.");
     } else
         die("The two teams must be distinct.");


     // add to table "rounds"
     $rnd_query="INSERT INTO {$mysql_prefix}_rounds SET team1='$_POST[team1_id]',
                    team2='$_POST[team2_id]', score1='$_POST[team1_score]',
                    score2='$_POST[team2_score]', tu_heard='$_POST[total_tuh]',
                    id='$_POST[round]'";
     query($rnd_query) or die("Choked adding round info: $rnd_query, probably lost individual stats");

     $game_id = mysql_insert_id(); 	

     $team1_num_players = $_POST["team1_size"];
     $team2_num_players = $_POST["team2_size"];
     
     // add to "rounds_players"
     for($i = 0;$i<$team1_num_players;$i++){ 
	 	$id = $_POST["team1_id_num"][$i];
	 	$play_query =  "INSERT INTO "."$mysql_prefix"."_rounds_players SET player_id=\"$id\" , team_id=\"$_POST[team1_id]\", powers=\"".$_POST["team1_pow"][$i]."\", tossups=\"".$_POST["team1_tu"][$i]."\", negs=\"".$_POST["team1_neg"][$i]."\", round_id=\"".$_POST["round"]."\", tu_heard=\"{$_POST["team1_tuh"][$i]}\", game_id=\"$game_id\"";
	 	query($play_query) or die("Choked adding records: $play_query (probably lost team 2 records");
     }
     for($i = 0;$i<$team2_num_players;$i++){
         $id = $_POST["team2_id_num"][$i];
         $play_query =  "INSERT INTO "."$mysql_prefix"."_rounds_players SET player_id=\"$id\" , team_id=\"$_POST[team2_id]\", powers=\"".$_POST["team2_pow"][$i]."\", tossups=\"".$_POST["team2_tu"][$i]."\", negs=\"".$_POST["team2_neg"][$i]."\", round_id=\"".$_POST["round"]."\", tu_heard=\"".$_POST["team2_tuh"][$i]."\", game_id=\"$game_id\"";
         query($play_query) or die("Choked adding records: $play_query");
     } 
     echo "<p>The round was added successfully.</p>";
 } else if(isset($_GET["edit"]) && is_numeric($_GET["edit"])) {
 	// now we edit the requested game
 	$game_id = $_GET["edit"];
 	
 	print "<p><form action='?delete=$game_id&t=$mysql_prefix' method='POST'>" .
 			"Confirm delete: <input type='checkbox' name='confirm' value='yes' />" .
 			"<input type='submit' value='Delete this round' /></form></p>\n";
 	
 	// get the entry from the rounds table
 	list($team1_name, $team2_name, $game_name, $team1_id, $team2_id, $team1_score, $team2_score, $tuh, $round_id) =
 			fetch_row(query("SELECT t1.full_name, t2.full_name, rounds.name, rounds.team1," .
 			"rounds.team2, rounds.score1, rounds.score2," .
 			"rounds.tu_heard, rounds.id " .
 			"	FROM {$mysql_prefix}_rounds AS rounds, {$mysql_prefix}_teams AS t1, {$mysql_prefix}_teams AS t2 " .
 			"	WHERE rounds.game_id = $game_id AND " .
 			"		rounds.team1=t1.id AND " .
 			"		rounds.team2=t2.id"));
 	
 	
 	// get all the individual stats
 	$team1_query = "SELECT {$mysql_prefix}_rounds_players.player_id, " .
 			"{$mysql_prefix}_rounds_players.team_id, {$mysql_prefix}_rounds_players.powers, " .
 			"{$mysql_prefix}_rounds_players.tossups, {$mysql_prefix}_rounds_players.negs, " .
 			"{$mysql_prefix}_rounds_players.tu_heard, {$mysql_prefix}_rounds_players.round_id, " .
 			"{$mysql_prefix}_players.first_name, {$mysql_prefix}_players.last_name" .
 			"	FROM {$mysql_prefix}_rounds_players, {$mysql_prefix}_players" .
 			"	WHERE {$mysql_prefix}_rounds_players.game_id = $game_id" .
 			"		AND {$mysql_prefix}_rounds_players.team_id=$team1_id" .
 			"		AND {$mysql_prefix}_rounds_players.player_id = {$mysql_prefix}_players.id";
 	$team1_res = query($team1_query) or die(mysql_error());
	$team2_query = "SELECT {$mysql_prefix}_rounds_players.player_id, " .
 			"{$mysql_prefix}_rounds_players.team_id, {$mysql_prefix}_rounds_players.powers, " .
 			"{$mysql_prefix}_rounds_players.tossups, {$mysql_prefix}_rounds_players.negs, " .
 			"{$mysql_prefix}_rounds_players.tu_heard, {$mysql_prefix}_rounds_players.round_id, " .
 			"{$mysql_prefix}_players.first_name, {$mysql_prefix}_players.last_name" .
 			"	FROM {$mysql_prefix}_rounds_players, {$mysql_prefix}_players" .
 			"	WHERE {$mysql_prefix}_rounds_players.game_id = $game_id" .
 			"		AND {$mysql_prefix}_rounds_players.team_id=$team2_id" .
 			"		AND {$mysql_prefix}_rounds_players.player_id = {$mysql_prefix}_players.id";
 	$team2_res = query($team2_query) or die(mysql_error());
 	
 	print "<h2>Editing Round $round_id: $team1_name vs. $team2_name</h2>\n";
?>
    <form action="?modify=<?=$game_id?>&t=<?=$mysql_prefix?>" method="POST">
   <h3>Points</h3>
   <p><?=$team1_name?>: <input type="text" name="team1_score" size="5" value="<?=$team1_score?>" />
      <?=$team2_name?>: <input type="text" name="team2_score" size="5" value="<?=$team2_score?>" /></p>
   	 <p>Toss-ups heard: <input type="text" size="5" name="total_tuh" value="<?=$tuh?>" />
   	  <input type="hidden" name="game_id" value="<?=$game_id?>" />
   	 </p>
	 <h3>Individual scores</h3>
	 <table>
	  <thead>
	   <tr>
	    <th colspan="5"><?=$team1_name?></th>
	   </tr>
	   <tr>
	    <th>Name</th>
	    <th>TUH</th>
	    <th>Pow</th>
	    <th>TU</th>
	    <th>Neg</th>
	   </tr>
	  </thead>
	  <tbody>
<?php
	 $l = 0;
     while(list($team1_pid, $team1_tid, $team1_pow, $team1_tup, $team1_neg,
     			$team1_tuh, $team1_rid, $team1_fn, $team1_ln) = fetch_row($team1_res)){
	 echo "<tr>
	     <td>$team1_fn $team1_ln <input type=\"hidden\" name=\"team1_pid[]\" value=\"$team1_pid\" /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_tuh[]\" value='$team1_tuh' /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_pow[]\" value='$team1_pow' /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_tup[]\" value='$team1_tup' /></td>
	     <td><input type=\"text\" size=\"5\" name=\"team1_neg[]\" value='$team1_neg' /></td>
	     </tr>";
	 $l++;
     }
     echo "</tbody>
	 </table>
	 <input type=\"hidden\" name=\"team1_size\" value=\"$l\" />
         <table>
          <thead>
           <tr>
            <th colspan=\"5\">$team2_name</th>
           </tr>
           <tr>
            <th>Name</th>
            <th>TUH</th>
            <th>Pow</th>
            <th>TU</th>
            <th>Neg</th>
           </tr>
          </thead>
          <tbody>";
     $k = 0;
     while(list($team2_pid, $team2_tid, $team2_pow, $team2_tup, $team2_neg,
     			$team2_tuh, $team2_rid, $team2_fn, $team2_ln) = fetch_row($team2_res)){
         echo "<tr>
             <td>$team2_fn $team2_ln <input type=\"hidden\" name=\"team2_pid[]\" value=\"$team2_pid\" /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_tuh[]\" value='$team2_tuh'  /></td>             
	     <td><input type=\"text\" size=\"5\" name=\"team2_pow[]\" value='$team2_pow'  /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_tup[]\" value='$team2_tup'  /></td>
             <td><input type=\"text\" size=\"5\" name=\"team2_neg[]\" value='$team2_neg'  /></td>
             </tr>";
	 $k++;
     }
     echo "</tbody>
         </table>
	 <input type=\"hidden\" value=\"$k\" name=\"team2_size\" />
	 <input type=\"submit\" value=\"Apply changes\" />
  </form>";
 	
 } else {				// show the team picker
?>
    <form action="" method="GET">
    <h2>Select teams</h2>
    <p>
    <select name="team1">
<?php
    // We need to get the list of teams to present a drop-down menu of them
     $res = query("SELECT full_name,id FROM "."$mysql_prefix"."_teams ORDER BY full_name");
     while(list($team_name,$team_id) = fetch_row($res)) {
	 echo "<option value=\"$team_id\">$team_name</option>\n";
     }
     mysql_free_result($res);
?>
     </select> versus <select name="team2">
<?php
     $res2 = query("SELECT full_name,id FROM "."$mysql_prefix"."_teams ORDER BY full_name");
     while(list($team_name,$team_id) = fetch_row($res2)) {
	 echo "<option value=\"$team_id\">$team_name</option>\n";
     }
     free_result($res2);
?>
      </select>
      Round: 
      <input type="text" size="5" name="round" />
      <input type="hidden" value="<?=$mysql_prefix?>" name="t" />
      <input type="submit" value="Continue" />
     </p>
     </form>
<?php
 }

 require "foot.php";			// finish off page

 ?>
