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

 if ($_GET["action"]=="add_teams") {
     $team_id = $_POST["team"];
     $i = 0;
     foreach($_POST["full"] as $team_full) {
	 if($team_full != "") {
	    $team_short = $_POST["short"][$i];
	    query("INSERT INTO "."$mysql_prefix"."_teams SET full_name=\"$team_full\",short_name=\"$team_short\""); 
	    $i++;
	 }
     }
     echo "<p>$i teams added without incident.</p>";
 } else { 
?>
     <form action="?action=add_teams&t=<?=$mysql_prefix?>" method="POST">
     <p class="form">
      <input type="text" disabled size="30" value="Team name" />
      <input type="text" disabled size="30" value="Team short name" /> <br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />
      <input type="text" size="30" name="full[]" />
      <input type="text" size="30" name="short[]" /><br />

      <input type="submit" value="Add teams" />
     </p>
<?php
 }

 require "foot.php";			// finish off page

 ?>
