<?php
/*
 * Functions
 */

require "config.php";

// functions for links
function link_team($name, $id) {
    global $mysql_prefix;
    return "<a href='stats_team.php?t={$mysql_prefix}&team=$id'>$name</a>";
}

function link_player($name, $id) {
    global $mysql_prefix;
    return "<a href='stats_individual.php?t={$mysql_prefix}&player=$id'>$name</a>";
}

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

function powered_by() {
    echo '<p id="powered">Tournament stats powered by <a href="http://code.google.com/p/qbsql/"><tt>qbsql</tt></a>.</p>'."\n";
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

/* Returns the queries to create tables for a given tournament */
function table_create_queries($prefix) {
         $query = <<<CREATE
CREATE TABLE {$prefix}_players (
  last_name varchar(40) default NULL,
  first_name varchar(40) default NULL,
  short_name varchar(40) default NULL,
  team int(20) NOT NULL default '0',
  id int(20) NOT NULL auto_increment,
  KEY id (id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_rounds (
  name varchar(40) default NULL,
  team1 int(20) NOT NULL default '0',
  team2 int(20) NOT NULL default '0',
  score1 int(20) default NULL,
  score2 int(20) default NULL,
  tu_heard int(20) default NULL,
  id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (game_id),
  KEY id (id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_rounds_players (
  player_id int(20) NOT NULL default '0',
  team_id int(20) NOT NULL default '0',
  powers int(20) default NULL,
  tossups int(20) default NULL,
  negs int(20) default NULL,
  tu_heard int(20) default NULL,
  round_id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL default '0',
  KEY round_id (round_id),
  KEY game_id (game_id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_teams (
  full_name varchar(30) default NULL,
  short_name varchar(30) default NULL,
  id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
CREATE;
         return $query;
}

?>
