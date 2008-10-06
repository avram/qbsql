<?php
/*
 * qbsql - a program for quiz bowl stats keeping
 * Copyright 2008  Avram Lyon <ajlyon+qbsql@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301  USA
 */
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
     <form action="?action=add_teams" method="POST">
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
