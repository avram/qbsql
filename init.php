<?php
/* init.php */

    if (isset($_GET["kill"])) {
	session_start();
	session_destroy();
    }
    require_once("functions.php");
    $auth = FALSE;
    session_start();
    if(isset($_GET["login"])) { 
	if (check_auth($PHP_AUTH_USER,$PHP_AUTH_PW) || 
		check_auth($_POST["login_u"],$_POST["login_p"]) ||
		$_SESSION["authorized"]) {
	    $auth = TRUE;
	}
    } else { 
	if (check_auth($PHP_AUTH_USER,$PHP_AUTH_PW) || $_SESSION["authorized"])
	{
	    $auth = TRUE;
	}
    }


    // initialize session and register, set variables
    $_SESSION["authorized"]=$auth;
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');
?>
