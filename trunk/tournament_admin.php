<?php
/* tournament_admin.php 
 *
 * Administrative tasks for the TD.
 *
 * This uses wymeditor to edit the tournament description.
 *
 * Author: Avram Lyon 
 * Created: 10 February 2009
 */
 require "init.php";			// set up (connect to DB, etc)
 require "require_admin.php";		// not just anyone can use this
 $title="Administer Tournament";
 require "head.php";			// Generate header as appropriate


 /* Process settings changes */
 if($_GET["a"] == "edit" && isset($_POST["name"]) && $_POST["name"] != "") {
     $q = sprintf("UPDATE tournaments SET name='%s',
         description='%s', game_length='%s' WHERE prefix='%s'",
         $_POST["name"],
         $_POST["desc"],
         $_POST["length"],
         //mysql_escape_string($_POST["name"]),
         //mysql_escape_string($_POST["desc"]),
         //mysql_escape_string($_POST["length"]),
         $mysql_prefix
     );
     $success = query($q) or dbwarning("Tournament settings update failed. Restoring old settings.", $q);
     // if we failed, try to restore
     if(!$success) {
         $q = sprintf("UPDATE tournaments SET name='%s',
             description='%s', game_length='%s' WHERE prefix='%s'",
             $tourney_name,
             $tourney_desc,
             $tourney_game_length,
             $mysql_prefix
         );
         $success = query($q) or dbwarning("Tournament settings restore failed.", $q);
         if (!$success)
             die();
     }
     $res = query("SELECT name, username, password, locked, game_length, description
                        FROM tournaments
                        WHERE prefix = '$mysql_prefix'
                        LIMIT 1");

     if($row = fetch_row($res)) {
         list($tourney_name, $tourney_un, $tourney_pass, $tourney_lock,
             $tourney_game_length, $tourney_desc) = $row;
     }

     message("Tournament updated.");
 }
?>
<h2>Settings</h2>
<form action="?a=edit&t=<?=$mysql_prefix?>" method="post">
<p><label for="name">Tournament Name</label><input size=25 name="name" id="name" value="<?=$tourney_name?>" /></p>
<p><label for="desc">Tournament Description</label><textarea name="desc" rows=10 cols=40 id="desc" class="wymeditor"><?=$tourney_desc?></textarea></p>
<p><label for="length">Default Round Length</label><input name="length" id="length" value="<?=$tourney_game_length?>" /></p>
<p><input type="submit" class="wymupdate" value="Update" /></p>
</form>
<h2>Export</h2>
<p><a href="export.php?t=<?=$mysql_prefix?>">Export Tournament to SQBS</a> - Save the generated file and open it with SQBS. It might work. At the very least, it can be submitted to NAQT.</p>
<?php

 require "foot.php";			// finish off page

 ?>
