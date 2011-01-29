<?php
/* 
 * head.php
 *
 * Takes:
 * $title -- the page title
 *
 * Author: Avram Lyon
 * Created: 21 February 2004
 */
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
    echo "\n"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 <meta http-equiv="Content-type" content='text/html; charset="UTF-8"' />
 <title><?php echo $title ?></title>
 <link rel="stylesheet" type="text/css" href="qbsql.css" />
 <link rel="stylesheet" type="text/css" href="local.css" />
<?php if(isset($js_includes) && $js_includes) { // Don't include JS if not needed ?> 
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js"></script>
 <script type="text/javascript" src="inc/jquery.tablesorter.min.js"></script>
 <script type="text/javascript" src="inc/tiny_mce.js"></script>
 <script type="text/javascript" src="inc/jquery.tinymce.js"></script>
 <script type="text/javascript" src="inc/qbsql.js"></script>
<?php } ?>
</head>
<body>
<div class="headline"><strong><?=$tourney_name?></strong> | <a href="tournaments.php">Other Tournaments</a>
<?php if (isset($mysql_prefix) && $mysql_prefix != "") {?> 
<div id="login">
<?php if($tourney_lock) { ?>
<h3>Tournament locked</h3>
<? } else if(!$auth) { ?>
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
 } // end if not locked
} // end if prefix set
?>
</div>
</div>
 <h1><?php echo $title ?></h1>
