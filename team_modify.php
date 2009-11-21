<?php
/* team_modify.php
 * 
 * Handle the creation and maintenance of teams.
 *
 * Author: Avram Lyon 
 * Created: 21 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 require "require_admin.php";		// not just anyone can use this
 $title="Manage teams";
 require "head.php";			// Generate header as appropriate
 
 // try to apply deletes
 if(isset($_GET["delete"]) && is_numeric($_GET["delete"]) && $_POST["confirm"] == "yes") {
    $tid = $_GET["delete"];
    $round_query = "SELECT t.full_name, COUNT(r1.game_id)+COUNT(r2.game_id)
 	              FROM {$mysql_prefix}_teams AS t
 	              LEFT JOIN
 	                  {$mysql_prefix}_rounds AS r1
 	                  ON r1.team1 = t.id 
 	              LEFT JOIN
 	                  {$mysql_prefix}_rounds AS r2
 	                  ON r2.team2 = t.id 
 	              WHERE t.id='$tid'
 	              GROUP BY t.id
 	              LIMIT 1";
    $r_res = query($round_query) or die(mysql_error());
    list($team, $rnd_ct) = fetch_row($r_res);
    
    $player_query = "SELECT COUNT(p.id)
 	              FROM {$mysql_prefix}_teams AS t,
                        {$mysql_prefix}_players AS p
                        WHERE t.id='$tid'
                        AND p.team=t.id";
    $p_res = query($player_query) or die(mysql_error());
    list($p_ct) = fetch_row($p_res);
    
    if($rnd_ct > 0 || $p_ct > 0) { // We refuse to delete if there are records that would be orphaned.
        if ($rnd_ct > 0)
            warning("Cannot delete team <b>$team</b>. This team has game records, which must be deleted first.",
                "Go to \"Round summaries\"","stats_round.php");
        if ($p_ct > 0)
            warning("Cannot delete team <b>$team</b>. This team has player records, which must be deleted first.",
                "Go to \"Team rosters\"","rosters.php");
    } elseif(isset($team)) {
        $del_query = "DELETE FROM {$mysql_prefix}_teams WHERE id = '$tid' LIMIT 1";
        $del_res = query($del_query);
        if($del_res) {
            message("Team <b>$team</b> deleted.");
        } else {
            warning("Delete failed for unknown reasons.");
        }
    } else {
 	    warning("Cannot delete. Team not found.");
    }
 }

 if ((isset($_GET["edit"]) && is_numeric($_GET["edit"])) || 
 		(isset($_GET["delete"]) && is_numeric($_GET["delete"]) && $_POST["confirm"] != "yes")) {
 	$tid = (isset($_GET["edit"])) ? $_GET["edit"] : $_GET["delete"];
        $team_query = "SELECT full_name, short_name, bracket FROM {$mysql_prefix}_teams WHERE id = '$tid' LIMIT 1";
 	list($fn,$sn,$brk) = fetch_row(query($team_query));
 ?>

 <h2>Editing <?=$fn?></h2>
 <p class="instructions">To delete a team, first delete all of games it has played,
 then each of its players, then check the confirmation box below and click "Delete".</p>
 <form action="?delete=<?=$tid?>&t=<?=$mysql_prefix?>" method="post">
 <p id="delete"><input type="checkbox" name="confirm" value="yes" />
 	<input type="submit" value="Delete" /></p>
 </form>
 <form action="?modify=<?=$tid?>&t=<?=$mysql_prefix?>" method="post">
 <p>Full Name: <input type="text" name="t_fn" size="12" value="<?=$fn?>" /></p>
 <p>Short Name: <input type="text" name="t_sn" size="12" value="<?=$sn?>" /></p>
 <p class="instructions">The bracket field, if specified, should
     be an integer. If no brackets are specified, then results will not be
     broken down by bracket.</p>
 <p>Bracket: <input type="text" name="t_brk" size="5" value="<?=$brk?>" /> (leave blank if no bracket)</p>
 <p><input type="submit" value="Apply Changes" /></p>
 </form>
 <?php
 } else if (isset($_GET["modify"]) && is_numeric($_GET["modify"])) {
     $tid = $_GET["modify"];
     $bracket_query = (is_numeric($_POST["t_brk"]) || $_POST["t_brk"] == "") ? ", bracket = '$_POST[t_brk]'" : "";
 	$mod_query = "UPDATE {$mysql_prefix}_teams SET full_name='$_POST[t_fn]', short_name='$_POST[t_sn]' $bracket_query WHERE id=$tid";
 	$res = query($mod_query) or dbwarning("Team info update failed.", $mod_query);
        if($res)
            message("Applied changes");
 } else if ($_GET["action"]=="add_teams") {
     $team_id = $_POST["team"];
     $i = 0;
     foreach($_POST["full"] as $team_full) {
        if($team_full != "") {
        // If short name undefined, use long name instead
	    $team_short = ($_POST["short"][$i] != "") ? $_POST["short"][$i] : $team_full;
        // If a valid bracket is specified, add the appropriate piece to our query
	    $bracket_query = is_numeric($_POST["bracket"][$i])
        		? ", bracket = '{$_POST["bracket"][$i]}'" : "";
	    query("INSERT INTO {$mysql_prefix}_teams SET full_name='$team_full', short_name='$team_short' $bracket_query"); 
	    $i++;
	 }
     }
     message("$i team(s) added without incident.");
 } else { 
?>
     <form action="?action=add_teams&t=<?=$mysql_prefix?>" method="POST">
     <p class="instructions">Enter the name and short names of
     the teams to be added. Short names are optional, but they might be
     helpful for teams with particularly long names that will look odd in
     some parts of the interface.</p>
     <p class="instructions">The bracket field, if specified, should
     be an integer. If no brackets are specified, then results will not be
     broken down by bracket.</p>
     <p class="form">
      <input type="text" disabled size="30" value="Team name" />
      <input type="text" disabled size="30" value="Team short name" />
      <input type="text" disabled size="4" value="Bracket" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" />
      <input type="text" size="4" name="bracket[]" /><br />
      <input type="submit" value="Add teams" />
     </p>
    <p>To place teams in brackets, edit them individually and set the bracket numbers.</p>
<?php
 }

 require "foot.php";			// finish off page

 ?>
