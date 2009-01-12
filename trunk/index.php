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
<div id="login">
<? if(!$auth) { ?>
<form method="post" action="?login&t=<?=$mysql_prefix?>">
 <h3>Login</h3>
 <p>Username: <input type="text" name="login_u" size="10" /> Password: <input type="password" name="login_p" size="10" /> <input type="submit" value="Log in" /></p>
 </form>
<? } else {?>
<form action="?" method="get">
<p>Currently logged in to tournament.
<input type="submit" value="Log out" />
<input type="hidden" name="kill" value="now" />
<input type="hidden" name="t" value="<?=$mysql_prefix?>" /></p></form>     
 <?php
}
?>
</div>
<?php
 require "foot.php";			// finish off page
 ?>