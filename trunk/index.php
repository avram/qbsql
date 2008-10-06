<?php
/* index.php
 *
 * Central command for qbsql 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Name of Tournament";
 require "head.php";			// Generate header as appropriate
?>
<? if(!$auth) { ?>
 <form method="post" action="?login">
 <p><input type="text" name="login_u" size="10" /><input type="password" name="login_p" size="10" /> <input type="submit" value="Log in" /></p>
 </form>
<? } else {?>
 <p><a href="?kill=now">[Log out]</a></p>     
 <?
}
 require "foot.php";			// finish off page

 ?>
