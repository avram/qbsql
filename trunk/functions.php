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
/*
 * Settings & functions
 */

// Tournament settings
// l/p for modifying tournament info
$tourney_un = "quizbowl";
$tourney_pass = "password";
$tourney_lock = 0; // set to 1 to prevent modifications

// These three lines should be changed to reflect your MySQL configuration.
$mysql_host = ":/tmp/mysql.sock";
$mysql_username = "mysql_un";
$mysql_pass = "mysql_pw";
$mysql_db = "qbsql";
// the prefix must be unique within the database
$mysql_prefix = "im1";

// set mysql library in use (mysql or mysqli)
//$mysql_library = "mysqli";
$mysql_library = "mysql";

// DO NOT EDIT BENEATH THIS COMMENT

// wrappers
function connect($host, $username, $pass, $db) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_connect($host, $username, $pass, $db);
    else {
      $link_local =  mysql_connect($host, $username, $pass, $db);
      mysql_select_db($db);
      return $link_local;
    }
}

function query($query) {
    global $mysql_library;
    if ($mysql_library=="mysqli") {
	global $link;
	return mysqli_query($link,$query);
    }
    else
      return mysql_query($query);
}

function field_name($result, $num) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_fetch_field_direct($result, $num);
    else
      return mysql_field_name($result, $num);
}

function fetch_assoc($result) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_fetch_assoc($result);
    else
      return mysql_fetch_assoc($result);
}

function fetch_row($result) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_fetch_row($result);
    else
      return mysql_fetch_row($result);
}

function free_result($result) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_free_result($result);
    else
      return mysql_free_result($result);
}

function close($link) {
    global $mysql_library;
    if ($mysql_library=="mysqli")
      return mysqli_close($link);
    else
      return mysql_close($link);
}

/* check_auth (string, string)
 * 
 * Checks password with the known hash, to allow or deny access to database.
 */
function check_auth($username, $pass) {
    global $tourney_un;
    global $tourney_pass;
    if (!isset($username) || !isset($pass))
        return FALSE;

    if ($username==$tourney_un && $pass == $tourney_pass)
       return TRUE;
    else
      return FALSE;
}

/* table (MySQL result, array/result fields, int cols, 
 *                      bool head, bool MySQL_fields, string class) 
 * 
 * Prints out the result of a MySQL query as a well-formed HTML table, 
 * optionally using field names in the header. A table class is supported
 * (for using CSS).
 *
 * Takes:
 * $result  - MySQL result object
 * $fields  - either array or MySQL fields
 * $columns - # cols
 * $head    - yes/no header
 * $names   - yes = MySQL fields, no=array
 * $class   - CSS class attribute
 */
function table($result, $fields, $columns, $head, $names, $class) {
    print "<table class=\"$class\">\n";
    if ($head) {
        print "\t<thead>\n\t<tr>\n";
        for ($i = 0; $i < $columns; $i++) {
            print "\t\t<th>";
            if ($names)
              print field_name($fields, $i);
            else
              print $fields[$i];
            print "</th>\n";
        }
    print "\t</tr>\t</thead>\n<tbody>\n";
    }
    while ($line = fetch_assoc($result)) {
        print "\t<tr>\n";
        foreach ($line as $col_value) {
            print "\t\t<td>$col_value</td>\n";
        }
        print "\t</tr>\n";
    }
    print "</tbody>\n</table>\n";
}



// Define the functions that we use:

/* from James Michael-Hill, June 2003 for UCDB at Grinnell College */
function popupHelp($topic){
    return "<sup> <a href='#' onClick=\"javaScript:window.open('help.php?topic=$topic','UCDB_Help','height=500,width=500,scrollbars,resizable,')\">[?]</a> </sup>\n";
}

?>
