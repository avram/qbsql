<?php
/* index.php
 *
 * Central command for qbsql 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
require "init.php";			// set up (connect to DB, etc)

$title = $tourney_name;
require "head.php";			// Generate header as appropriate
?>
    <p id="description"><?=$tourney_desc?></p>
    <p>See the <a href="tournaments.php">Tournament List</a> for results from other tournaments.</p>
<? if(!$auth) { ?>
<form method="post" action="?login&t=<?=$mysql_prefix?>">
 <p><input type="text" name="login_u" size="10" /><input type="password" name="login_p" size="10" /> <input type="submit" value="Log in" /></p>
 </form>
<? } else {?>
<p><a href="?kill=now&t=<?=$mysql_prefix?>">[Log out]</a></p>     
 <?
}
 require "foot.php";			// finish off page

 ?>
