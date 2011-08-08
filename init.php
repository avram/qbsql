<?php
/* init.php */

    if (isset($_GET["kill"])) {
		session_start();
		session_destroy();
    }
    require_once("functions.php");
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db)
    		or die('Failed to connect to DB server.');

    // If we have a database migration, do it.
    /* Minimum db revision for this version of the code */
    $required_rev = 103;
    require_once("migrations.php");
    migrate($required_rev);

    // set tournament
    if (isset($_GET["t"]) && !preg_match("/[^a-zA-Z0-9_]/", $_GET["t"])) {
        $res = query("SELECT name, username, password, locked, game_length, description, api_key
                        FROM tournaments
                        WHERE prefix = '$_GET[t]'
                        LIMIT 1");

        if($row = fetch_row($res)) {
            list($tourney_name, $tourney_un, $tourney_pass, $tourney_lock,
                $tourney_game_length, $tourney_desc, $tourney_api_key) = $row;
            $mysql_prefix = $_GET["t"];
        } else {
            // redirect to tournament list
           	header("Location: tournaments.php");
            exit();
        }
    } else {
        // redirect to tournament list
       	header("Location: tournaments.php");
        exit();
    }
    
    $auth = FALSE;
    session_start();
    if(isset($_GET["login"])) { 
	if (check_auth($PHP_AUTH_USER,$PHP_AUTH_PW) || 
		check_auth($_POST["login_u"],$_POST["login_p"]) ||
		$_SESSION["auth_{$mysql_prefix}"]) {
	    $auth = TRUE;
	}
    } else { 
	if (check_auth($PHP_AUTH_USER,$PHP_AUTH_PW) || $_SESSION["auth_{$mysql_prefix}"])
	{
	    $auth = TRUE;
	}
    }
    
    // No auth if locked.
    if($tourney_lock) $auth = false;
    
    // initialize session and register, set variables
    $_SESSION["auth_{$mysql_prefix}"]=$auth;
?>
