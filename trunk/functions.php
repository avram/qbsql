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
    StatsTable::old($result, $fields, $columns, $head, $names, $class, $options);
}

class StatsTable {
    var $fields;
    var $class;
    var $sort;
    var $ranks;

    var $html;
    var $next_rank;

    function StatsTable($fields, $sort, $ranks, $class) {
        $this->next_rank = 1;
        $this->ranks = $ranks;
        $this->fields = $fields;
        $this->class = $class;
        $this->sort = $sort;
    }

    function next_rank($rank) {
        $this->next_rank = $rank;
    }

    function table() {
        if($this->sort) {
            $sort_id = "sort";
        $final = <<<EOS
 <script language="javascript">
$(document).ready(function() 
    { 
        $(".sort").tablesorter({widgets: ['zebra']});
    } 
);
 </script>
EOS;
        }
        $final .= "<table class='$this->class $sort_id'>\n";
        $final .= $this->html;
        $final .= "</table>";
        return $final;
    }

    function names() {
        $columns = count($this->fields);
        $this->html .=  "\t<thead>\n\t<tr>\n";
        if ($this->ranks)
            $this->html .=  "\t\t<th>Rank</th>\n"; 
        for ($i = 0; $i < $columns; $i++) {
            $this->html .=  "\t\t<th>".$this->fields[$i] .  "</th>\n";
        }
        $this->html .=  "\t</tr>\t</thead>\n";
    }

    function interstitial($str) {
        $cols = count($this->fields);
        if($this->ranks)
            $cols++;
        $this->html .= "<thead><tr><th colspan='$cols'>$str</th></tr></thead>\n";
    } 

    function body_res($result) {
        $this->html .= "<tbody>\n";
        $i = $this->next_rank;
        $columns = 0;
        while ($line = fetch_assoc($result)) {
            $columns = 0;
            $this->html .= "\t<tr>\n";
            if ($this->ranks) {
                $columns++;
                $this->html .= "\t\t<td>$i</td>\n";
            }
            foreach ($line as $col_value) {
                $columns++;
                $this->html .= "\t\t<td>$col_value</td>\n";
            }
            $this->html .= "\t</tr>\n";
            $i++;
        }
        if ($i == $this->next_rank) // No lines
            $this->html .= "\t<tr><td colspan='$columns' class='table-no-data'>No data</td></tr>\n";
        $this->html .= "</tbody>\n";
    }

    function old($res, $fields, $columns, $head, $names, $class, $options) {
        $table = new StatsTable($fields, array_key_exists("sort",$options) || in_array("sort",$options),
                array_key_exists("ranked",$options) || in_array("ranked",$options), $class);
        $table->names();
        $table->body_res($res);
        print $table->table();
    }
}


// Define the functions that we use:

/* from James Michael-Hill, June 2003 for UCDB at Grinnell College */
function popupHelp($topic){
    return "<sup> <a href='#' onClick=\"javaScript:window.open('help.php?topic=$topic','UCDB_Help','height=500,width=500,scrollbars,resizable,')\">[?]</a> </sup>\n";
}

/* Creates a database error warning. Observes debug mode */
/* Does not halt execution. */
function dbwarning($message, $query) {
    global $debug;

    if ($debug)
        $additional = "<tt>".mysql_error()."</tt><pre class='db-error-query'>$query</pre>";
    else
        $additional = "";

    warning("$message (".mysql_errno().") $additional");
}

/* creates a nice little warning box with the given text 
 warning("That's incorrect input");
 warning("You need to create teams first.", "Go to 'Add Teams'", "add_teams.php"); */
function warning() {
    global $mysql_prefix;
    $args = func_get_args();
    $text = $args[0];
    $redirect = "";
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

/* returns the brackets in use */
function fetch_brackets() {
    global $mysql_prefix;

    $query = "SELECT DISTINCT bracket FROM {$mysql_prefix}_teams";
    $res = query($query) or dbwarning("Failed to fetch brackets.",$query);
    $brks = array();
    while (list($brk) = fetch_row($res)) {
        if(is_numeric($brk))
            $brks[] = $brk;
    }
    return $brks;
}

/* Exporting functions */
/* Returns the queries to create tables for a given tournament */
/* Result is an array of queries */
function table_create_queries($prefix) {
    $queries = array();
    $query = <<<CREATE
CREATE TABLE {$prefix}_players (
  last_name varchar(40) default NULL,
  first_name varchar(40) default NULL,
  short_name varchar(40) default NULL,
  team int(20) NOT NULL default '0',
  id int(20) NOT NULL auto_increment,
  KEY id (id)
) ENGINE=MyISAM;
CREATE;
    $queries[] = $query;
    $query = <<<CREATE
CREATE TABLE {$prefix}_rounds (
  name varchar(40) default NULL,
  team1 int(20) NOT NULL default '0',
  team2 int(20) NOT NULL default '0',
  score1 int(20) default NULL,
  score2 int(20) default NULL,
  tu_heard int(20) default NULL,
  tiebreakers int(20) default NULL,
  id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (game_id),
  KEY id (id)
) ENGINE=MyISAM;
CREATE;
    $queries[] = $query;
    $query = <<<CREATE
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
CREATE;
    $queries[] = $query;
    $query = <<<CREATE
CREATE TABLE {$prefix}_teams (
  full_name varchar(30) default NULL,
  short_name varchar(30) default NULL,
  bracket int(20) NOT NULL default '0',
  id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
CREATE;
    $queries[] = $query;
    return $queries;
}

function export_table($name) {
    print "/* Table $name */\n";
    $res = query("SELECT * FROM $name");
    while($row = fetch_row($res)) {
        print 'INSERT INTO '.$name.' VALUES ("';
        print implode('","', $row);
        print "\");\n";
    }
}

function export_tournament($prefix) {
    print "/* Tournament $prefix */\n";
    foreach (table_create_queries($prefix) as $q) {
        print $q;
    }
    export_table("{$prefix}_players");
    export_table("{$prefix}_rounds");
    export_table("{$prefix}_rounds_players");
    export_table("{$prefix}_teams");
}

function export_database() {
    print "/* Exporting Database */\n";
    $res = query("SELECT * FROM tournaments") or print(mysql_error());
    while($row = fetch_row($res)) {
        print 'INSERT INTO tournaments VALUES ("';
        print implode('","', $row);
        print "\");\n";
        export_tournament($row[2]);
    }
}

function sqbs_export_tourney($prefix) {
    $sqbs = "";
    $res = query("SELECT COUNT(*) FROM {$prefix}_teams");
    list($t_ct) = fetch_row($res);
    $sqbs .= "$t_ct\n";

    $tm_id_to_index = array();
    $p_id_to_index = array();

    $t_index = 0;

    $t_res = query("SELECT full_name, id FROM {$prefix}_teams ORDER BY full_name");
    while(list($tname, $tid) = fetch_row($t_res)) {
        $p_ct_res = query("SELECT COUNT(*) FROM {$prefix}_players WHERE team='$tid'");
        list($p_ct) = fetch_row($p_ct_res);
        $p_ct++;
        $sqbs .= "$p_ct\n";
        $sqbs .= "$tname\n";

        // Remember what index we gave the team
        $tm_id_to_index[$tid] = $t_index;
        $t_index++;

        $p_res = query("SELECT first_name, last_name, id FROM {$prefix}_players WHERE team='$tid' ORDER BY last_name");
        $p_index = 0;
        while(list($fn, $ln, $pid) = fetch_row($p_res)) {
            $sqbs .= "$fn $ln\n";
            // Remember what index we gave the player
            $p_id_to_index[$pid] = $p_index;
            $p_index++;
        }
    }

    $g_ct_res = query("SELECT COUNT(*) FROM {$prefix}_rounds");
    list($g_ct) = fetch_row($g_ct_res);
    $sqbs .= "$g_ct\n";

    $g_res = query("SELECT team1, team2, score1, score2, tu_heard, r.id,
                        t1.bons, t1.tuppts, t2.bons, t2.tuppts, r.game_id
                    FROM {$prefix}_rounds AS r,
                    (SELECT SUM(tossups+powers) AS bons, SUM(tossups*10+powers*15-negs*5) AS tuppts, game_id, team_id
                        FROM {$prefix}_rounds_players GROUP BY game_id, team_id) AS t1,
                    (SELECT SUM(tossups+powers) AS bons, SUM(tossups*10+powers*15-negs*5) AS tuppts, game_id, team_id
                        FROM {$prefix}_rounds_players GROUP BY game_id, team_id) AS t2
                    WHERE t1.team_id = team1 AND t2.team_id = team2 AND t1.game_id=r.game_id AND t2.game_id=r.game_id ORDER BY r.id") or die(mysql_error());
    while(list($t1,$t2, $sc1, $sc2, $tuh, $rnd, $bons1, $tuppts1, $bons2, $tuppts2, $game) = fetch_row($g_res)) {
        $sqbs .= "$game\n";                     // unique identifier for game
        $sqbs .= $tm_id_to_index[$t1]."\n";     // index of team 1
        $sqbs .= $tm_id_to_index[$t2]."\n";     // index of team 2
        $sqbs .= "$sc1\n";                      // team1 score
        $sqbs .= "$sc2\n";
        $sqbs .= "$tuh\n";                      // tossups heard, inc. tiebreakers
        $sqbs .= "$rnd\n";                      // round
        $sqbs .= "$bons1\n";                    // team1 bonus ct
        $sqbs .= ($sc1-$tuppts1)."\n";          // team1 bonus pts
        $sqbs .= "$bons2\n";
        $sqbs .= ($sc2-$tuppts2)."\n";
        $sqbs .= "0\n";                         // overtime (1=yes, 0=no)
        $sqbs .= "0\n";                         // team1 # correct OT tups
        $sqbs .= "0\n";                         // team2  --same--
        $sqbs .= "0\n";                         // forfeit (1=team1 forfeit, 0=no)
        $sqbs .= "0\n";         // lightning t1 pts
        $sqbs .= "0\n";         // lightning t2 pts

        // Now the player scores, team 1
        $p1_res_sc = query("SELECT tu_heard, powers, tossups, negs, player_id FROM {$prefix}_rounds_players WHERE game_id = '$game' AND team_id='$t1'") or die(mysql_error());
        $p_buffer = 0;
        // Now the player scores, team 2
        $p2_res_sc = query("SELECT tu_heard, powers, tossups, negs, player_id FROM {$prefix}_rounds_players WHERE game_id = '$game' AND team_id='$t2'") or die(mysql_error());
        while ($p_buffer <= 7) {
            if(list($p_tuh, $pows, $tups, $negs, $pid) = fetch_row($p1_res_sc)) {
                $sqbs .= $p_id_to_index[$pid]."\n"; // player index of player1
                $sqbs .= $p_tuh/$tuh."\n";          // fraction of game played
                $sqbs .= "$pows\n";
                $sqbs .= "$tups\n";
                $sqbs .= "$negs\n";
                $sqbs .= "0\n";                    // always 0
                $sqbs .= ($pows*15+$tups*10-$negs*5)."\n";                    // tossup pts
            } else {
                $sqbs .= "-1\n0\n0\n0\n0\n0\n0\n";    // fill remaining player lines with emptiness 
            }
            if(list($p_tuh, $pows, $tups, $negs, $pid) = fetch_row($p2_res_sc)) {
                $sqbs .= $p_id_to_index[$pid]."\n"; // player index of player1
                $sqbs .= $p_tuh/$tuh."\n";          // fraction of game played
                $sqbs .= "$pows\n";
                $sqbs .= "$tups\n";
                $sqbs .= "$negs\n";
                $sqbs .= "0\n";                    // always 0
                $sqbs .= ($pows*15+$tups*10-$negs*5)."\n";                    // tossup pts
            } else {
                $sqbs .= "-1\n0\n0\n0\n0\n0\n0\n";    // fill remaining player lines with emptiness 
            }
            $p_buffer++;
        }
    }

    return $sqbs;
}

?>
