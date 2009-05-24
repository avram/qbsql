<?php
/* roster_modify.php
 * 
 * Handle the creation and maintenance of rosters.
 *
 * Author: Avram Lyon 
 * Created: 21 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 require "require_admin.php";		// not just anyone can use this
 $title="Modify Rosters";
 require "head.php";			// Generate header as appropriate

 // try to apply deletes
 if(isset($_GET["delete"]) && is_numeric($_GET["delete"]) && $_POST["confirm"] == "yes") {
    $pid = $_GET["delete"];
 	$player_query = "SELECT p.first_name, p.last_name, t.full_name, COUNT(rp.game_id)
 	              FROM {$mysql_prefix}_teams AS t,
 	                  {$mysql_prefix}_players AS p
 	              LEFT JOIN
 	                  {$mysql_prefix}_rounds_players AS rp
 	                  ON rp.player_id = p.id 
 	              WHERE p.team = t.id AND p.id='$pid'
 	              GROUP BY p.id
 	              LIMIT 1";
    $p_res = query($player_query) or die(mysql_error());
    list($fn, $ln, $team, $rnd_ct) = fetch_row($p_res);
    if($rnd_ct > 0) { // We refuse to delete if there are records that would be orphaned.
        warning("Cannot delete player <b>$fn $ln</b> ($team). This player has game records, which must be deleted first.",
                "Go to \"Round summaries\"","stats_round.php");
    } elseif (isset($fn)) {
        $del_query = "DELETE FROM {$mysql_prefix}_players WHERE id = '$pid' LIMIT 1";
        $del_res = query($del_query);
        if($del_res) {
            message("Player <b>$fn $ln</b> ($team) deleted.");
        } else {
            warning("Delete failed for unknown reasons.");
        }
    } else {
 	    warning("Cannot delete. Player not found.");
    }
 }

 if ((isset($_GET["edit"]) && is_numeric($_GET["edit"])) || 
 		(isset($_GET["delete"]) && is_numeric($_GET["delete"]) && $_POST["confirm"] != "yes")) {
 	$pid = (isset($_GET["edit"])) ? $_GET["edit"] : $_GET["delete"];
 	$player_query = "SELECT p.first_name, p.last_name, t.full_name, p.naqtid FROM {$mysql_prefix}_players AS p," .
 			"			{$mysql_prefix}_teams AS t" .
 			"		WHERE p.team = t.id AND p.id=$pid LIMIT 1";
 	list($fn,$ln,$team,$naqtid) = fetch_row(query($player_query));
 ?>

 <h2>Editing <?=$fn?> <?=$ln?>, for <?=$team?></h2>
 <form action="?delete=<?=$pid?>&t=<?=$mysql_prefix?>" method="post">
 <p id="delete"><input type="checkbox" name="confirm" value="yes" />
 	<input type="submit" value="Delete" /></p>
 </form>
 <form action="?modify=<?=$pid?>&t=<?=$mysql_prefix?>" method="post">
 <p>First Name: <input type="text" name="p_fn" size="12" value="<?=$fn?>" /></p>
 <p>Last Name: <input type="text" name="p_ln" size="12" value="<?=$ln?>" /></p>
 <p>NAQT ID: <input type="text" name="naqtid" size="8" value="<?=$naqtid?>" /></p>
 <p><input type="submit" value="Apply Changes" /></p>
 </form>
 <?php
 } else if (isset($_GET["modify"]) && is_numeric($_GET["modify"])) {
 	$pid = $_GET["modify"];
 	$mod_query = "UPDATE {$mysql_prefix}_players SET first_name='$_POST[p_fn]', last_name='$_POST[p_ln]', naqtid='$_POST[naqtid]'" .
 			"			WHERE id=$pid";
 	query($mod_query) or die(mysql_error());
 	print "Applied changes";
 } else if ($_GET["action"]=="add_players") {
     $team_id = $_POST["team_id"];
     $i = 0;
     foreach($_POST["last"] as $player_last) {
	 $player_first = $_POST["first"][$i];
	 if (($player_first != "") && ($player_last != "")) { 
	     query("INSERT INTO "."$mysql_prefix"."_players SET last_name=\"$player_last\",first_name=\"$player_first\",team=\"$team_id\"");
	     $i++;
	 } 
     }
     message("$i player(s) added without incident.");
 } else {  // We present to form to add new players.
  // see if we have any teams
 $tm_res = query("SELECT COUNT(*) FROM {$mysql_prefix}_teams");
 list($num_teams) = fetch_row($tm_res);
  if($num_teams == 0) {
    warning("There are no teams in the tournament. You must add teams before adding players.",
            "Go to \"Add Teams\"", "team_modify.php");
    } else { ?>
    <p class="form">
     <form action="?action=add_players&t=<?=$mysql_prefix?>" method="POST">
     Team: <select name="team_id">
<?php
	// grab the id of a team that they might have selected
	if(isset($_GET["team"]) && is_numeric($_GET["team"]))
		$sel_team = $_GET["team"]; 
	else
		$sel_team = "-1";
    // We need to get the list of teams to present a drop-down menu of them
     $res = query("SELECT full_name,id FROM "."$mysql_prefix"."_teams ORDER BY full_name") or die(mysql_error());
     while(list($team_name,$team_id) = fetch_row($res)) {
	 	$selected = ($sel_team==$team_id) ? "selected" : "";
	 	echo "<option value=\"$team_id\" $selected>$team_name</option>\n";
     }
     free_result($res);
?>
     </select>
     </p>
     <h2>Players</h2>
     <p class="form">
      <input type="text" disabled size="30" value="First name" />
      <input type="text" disabled size="30" value="Last name" /> <br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />
      <input type="text" size="30" name="first[]" />
      <input type="text" size="30" name="last[]" /><br />

      <input type="submit" value="Add players" />
     </p>
<?php
}
 }

 require "foot.php";			// finish off page

 ?>
