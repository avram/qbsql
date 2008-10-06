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
