<?php
/*
 * Functions
 */

require "config.php";

// database wrappers
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
    if(!isset($result))
        return false;
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
 *                      bool head, bool MySQL_fields, string class, array options) 
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
 * $options - array of additional options
 *      "ranked"
 */
function table($result, $fields, $columns, $head, $names, $class, $options) {
    // should we show row numbers (ranks)? 
    $ranks = (array_key_exists("ranked",$options) || in_array("ranked",$options));
    print "<table class=\"$class\">\n";
    if ($head) {
        print "\t<thead>\n\t<tr>\n";
        if ($ranks)
           print "\t\t<th>Rank</th>\n"; 
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
    $i = 0;
    while ($line = fetch_assoc($result)) {
        print "\t<tr>\n";
        if ($ranks)
            print "\t\t<td>".++$i."</td>\n";
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

/* creates a nice little warning box with the given text 
 warning("That's incorrect input");
 warning("You need to create teams first.", "Go to 'Add Teams'", "add_teams.php"); */
function warning() {
    global $mysql_prefix;
    $args = func_get_args();
    $text = $args[0];
    if(count($args) == 3) {
        $target = $args[2]."?t=".$mysql_prefix;
        $redirect_text = $args[1];
        $redirect = "<p class='redirect'><a href='$target'>$redirect_text</a></p>\n";
    }
    $warning =  <<<EOP
<div class="warning">
 <p>$text</p>
 $redirect
</div>
EOP;
    print $warning;
}

/* creates a nice little message box with the given text */
function message($text) {
   $box =  <<<EOP
<div class="message">
 <p>$text</p>
</div>
EOP;
    print $box;
}

?>