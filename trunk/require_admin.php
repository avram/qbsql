<?php
    if (!$auth) {
	header("HTTP/1.0 401 Unauthorized");
	die("You must log in to use this system.");
    } else if($tourney_lock) {
	header("HTTP/1.0 401 Unauthorized");
	die("Tournament locked");
    }
?>
