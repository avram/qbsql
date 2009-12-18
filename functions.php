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

// things for dealing with NAQT database
function link_player_naqt($id) {
    if(is_numeric($id))
        return "<p class='db-link'><a href='http://www.naqt.com/stats/player.jsp?contact_id=$id'>Player info at NAQT results database</a></p>";
}

function search_naqt($first_name, $last_name) {
    $url = "http://www.naqt.com/stats/player-search.jsp?PASSBACK=PLAYER_SEARCH&FIRST_NAME={$first_name}&LAST_NAME={$last_name}";
    $raw = file_get_contents($url) or warning("NAQT database not accessible.");
    $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($raw));
    $start = strpos($content,'contact_id');
    $end = strpos($content,'</ul>',$start);
    $list = substr($content,$start,$end-$start);
    preg_match_all("|contact_id=(\d+)\">(.*)</a> \((.*)\)|U",$list, $el, PREG_SET_ORDER);
    $name = $el[0][2];
    $id = $el[0][1];
    $sch = strip_tags($el[0][3]);
    return (array($name, $id, $sch));
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

/* Creates a database error warning. Observes debug mode */
/* Halts execution after displaying error. */
function dberror($message, $query) {
	global $debug;

    if ($debug)
        $additional = "<tt>".mysql_error()."</tt><pre class='db-error-query'>$query</pre>";
    else
        $additional = "";

    print("$message (".mysql_errno().") $additional");
    die();
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

/* Verify game 
 * This function pulls a game from the database and verifies that it passes
 * sanity checks. These might include a common divisor of all valid scores,
 * non-negative quantities of tossups, powers, etc. */
function verify_game($id) {
	global $mysql_prefix;
	
	$gcd = 5;
	$messages = array(	"Score 1 GCD",
						"Score 2 GCD",
						"Bonus 1 GCD",
						"Bonus 2 GCD",
						"Round TUH",
						"Forfeit",
						"Tiebreakers",
						"Tiebreakers < TUH",
						"Tossups converted < TUH",
						"Team 1 tups valid",
						"Team 1 pows valid",
						"Team 1 negs valid",
						"Team 1 tossups < TUH",
						"Team 2 tups valid",
						"Team 2 pows valid",
						"Team 2 negs valid",
						"Team 2 tossups < TUH"
	);
	// Do all the checking in SQL
	$query = "SELECT	forfeit = r.team1 || forfeit = r.team2,
						(r.score1 % $gcd) = 0,
						(r.score2 % $gcd) = 0,
						((r.score1 - (p1.tup * 10 + p1.pow * 15 - p1.neg * 5)) % $gcd) = 0,
						((r.score2 - (p2.tup * 10 + p2.pow * 15 - p2.neg * 5)) % $gcd) = 0,
						r.tu_heard >= 0,
						ISNULL(forfeit) || r.forfeit = r.team1 || r.forfeit = r.team2,
						ISNULL(r.tiebreakers) || r.tiebreakers >= 0,
						ISNULL(r.tiebreakers) || r.tiebreakers <= r.tu_heard,
						p1.tup + p1.pow + p2.tup + p2.pow <= r.tu_heard,
						p1.tupv = 0,
						p1.powv = 0,
						p1.negv = 0,
						p1.ct = 0,
						p2.tupv = 0,
						p2.powv = 0,
						p2.negv = 0,
						p2.ct = 0
				FROM {$mysql_prefix}_rounds AS r,
					(SELECT	SUM(tossups) AS tup,
							SUM(powers) AS pow,
							SUM(negs) AS neg,
							SUM(tossups < 0) AS tupv,
							SUM(powers < 0) AS powv,
							SUM(negs < 0) AS negv,
							SUM(tu_heard < tossups + powers + negs) AS ct,
							team_id AS id
						FROM {$mysql_prefix}_rounds_players
						WHERE game_id = '$id' GROUP BY team_id
					) AS p1,
					(SELECT	SUM(tossups) AS tup,
							SUM(powers) AS pow,
							SUM(negs) AS neg,
							SUM(tossups < 0) AS tupv,
							SUM(powers < 0) AS powv,
							SUM(negs < 0) AS negv,
							SUM(tu_heard < tossups + powers + negs) AS ct,
							team_id AS id
						FROM {$mysql_prefix}_rounds_players
						WHERE game_id = '$id' GROUP BY team_id
					) AS p2
				WHERE 	game_id = '$id'
					AND	p1.id = r.team1
					AND p2.id = r.team2";
	$res = query($query) or dbwarning("Validation query failed.", $query);
	
	// mask with desired checks
	$apply = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
	
	$line = fetch_row($res);
	
	// If forfeit, no further checking.
	if($line[0] == 1) 
		return;
	
	for ($i=1;$i<count($line);$i++) {
		if($apply[$i] == 1 && $line[$i] != 1) {
			warning("Data integrity check failed: ".$messages[$i]);
		}
	}
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
  naqtid varchar(30) default NULL,
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
  forfeit int(20) default NULL,
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

/*
 * This export function is based on R. Hentzel's description of the SQBS
 * data file format, and tested against the NAQT results importer. The file
 * created by the exporter has not been tested against SQBS itself, and
 * probably cannot be read by SQBS. It does, however, work with the NAQT
 * importer.
 * 
 * The SQBS data format is a series of newline-separated fields.
 */
function sqbs_export_tourney($prefix) {
    $sqbs = "";
    $res = query("SELECT COUNT(*) FROM {$prefix}_teams");
    list($t_ct) = fetch_row($res);
     
    $sqbs .= "$t_ct\n";

    $tm_id_to_index = array();
    $p_id_to_index = array();

    $t_index = 0;

    /*
     * The first thing we need to do is give each team a number, from 0-N
     * for N teams. Then each player on each team needs a number, from 0-M
     * for M players. We'll keep track of which player and team has what index
     * using two hashes-- one from QBSQL team IDs to SQBS indices, and one from
     * QBSQL player IDs to SQBS indices, $tm_id_to_index and $p_id_to_index.
     */
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

    // We don't ordinary keep a count of games, so we need to count them.
    $g_ct_res = query("SELECT COUNT(*) FROM {$prefix}_rounds");
    list($g_ct) = fetch_row($g_ct_res);
    $sqbs .= "$g_ct\n";

    // This little beast gets all of the game data.
    $g_res = query("SELECT team1, team2, score1, score2, tu_heard, r.id,
                        t1.bons, t1.tuppts, t2.bons, t2.tuppts, r.game_id,
                        r.forfeit
                    FROM {$prefix}_rounds AS r,
                    (SELECT SUM(tossups+powers) AS bons, SUM(tossups*10+powers*15-negs*5) AS tuppts, game_id, team_id
                        FROM {$prefix}_rounds_players GROUP BY game_id, team_id) AS t1,
                    (SELECT SUM(tossups+powers) AS bons, SUM(tossups*10+powers*15-negs*5) AS tuppts, game_id, team_id
                        FROM {$prefix}_rounds_players GROUP BY game_id, team_id) AS t2
                    WHERE t1.team_id = team1 AND t2.team_id = team2 AND t1.game_id=r.game_id AND t2.game_id=r.game_id ORDER BY r.id") or die(mysql_error());
    while(list($t1,$t2, $sc1, $sc2, $tuh, $rnd, $bons1, $tuppts1, $bons2, $tuppts2, $game, $forfeit) = fetch_row($g_res)) {
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
        /* The next four values should be maintained by QBSQL, but we don't
         * yet support them. */
        $sqbs .= "0\n";                         // overtime (1=yes, 0=no)
        $sqbs .= "0\n";                         // team1 # correct OT tups
        $sqbs .= "0\n";                         // team2  --same--
        $forfeit = ($forfeit == $t1) ? 1 : 0; 	// FIXME We cannot rely on the first
        										// team always being the forfeiting one.
        $sqbs .= "$forfeit\n";                  // forfeit (1=team1 forfeit, 0=no)
        /* The next two values are unlikely to be supported by QBSQL */
        $sqbs .= "0\n";         // lightning t1 pts
        $sqbs .= "0\n";         // lightning t2 pts

        // Now the player scores, team 1
        $p1_res_sc = query("SELECT tu_heard, powers, tossups, negs, player_id FROM {$prefix}_rounds_players WHERE game_id = '$game' AND team_id='$t1'") or die(mysql_error());
        $p_buffer = 0;
        // Now the player scores, team 2
        $p2_res_sc = query("SELECT tu_heard, powers, tossups, negs, player_id FROM {$prefix}_rounds_players WHERE game_id = '$game' AND team_id='$t2'") or die(mysql_error());
        /* SQBS requires that there be 7 player lines per team */
        /* unneeded player lines are filled with zeroes */
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
        /* The SQBS data file has more here, but we don't need it to work with the
         * NAQT results database.
         */
    }

    return $sqbs;
}

/* Importer that should be able to read a tournament into the system and treat it
 * like any other QBSQL tournament
 *
 * Our approach here will be just like the SQBS exportert, just reversed; we'll
 * work naively line-by-line and see if it works.
 * 
 * Takes a path to the file as input.
 * Existing tournament must be specified-- the tournament should be
 * 	empty.
 **/
function sqbs_import_tourney($file, $mysql_prefix) {
	// Load the tournament
	$query = "SELECT id FROM tournaments WHERE prefix = '$mysql_prefix' LIMIT 1";
	$res = query($query) or dbwarning("Import error; prefix not found.",$query);
	if(!$res) return;
	
	// Load the file
	$in = file($file) or error("Failed to open SQBS file.");
	if(!$in) return;
	
	// Remove trailing newlines
	$in = array_map("trim", $in);
	
	// Now we will go line-by-line and create a tourney.
	// The first section is general data on rosters and teams.
	$tmct = array_shift($in); // # of teams
	
	/* As in the exporter, we match SQL indexes and SQBS ids */
	$tm_index_to_id = array();
    $p_index_to_id = array();
	
	for($i=0; $i < $tmct; $i++) {
		$player_ct = array_shift($in); // # players team 1
		$tm_name = array_shift($in); // Team 1 name
		$query = "INSERT INTO {$mysql_prefix}_teams SET full_name='$tm_name',
					short_name='$tm_name'";
		query($query) or dberror("Team $i addition failed.",$query);
		$tm_index_to_id[$i] = mysql_insert_id();
		$p_index_to_id[$i] = array();
		
		for($j=0; $j < ($player_ct-1); $j++) {
			// List of players on team 1
			$player_name = array_shift($in); // Player name
			//print_r($in);
			$pcs = explode(" ", $player_name);
			$ln = array_pop($pcs);
			$fn = implode(" ", $pcs);
			$query = "INSERT INTO {$mysql_prefix}_players SET
							first_name = '$fn',
							last_name = '$ln',
							team = {$tm_index_to_id[$i]}";
			query($query) or dberror("Player $j (team $i) addition failed.",$query);
			$p_index_to_id[$i][$j] = mysql_insert_id();
		}
	}
		
	// Now game data
	$g_ct = array_shift($in);	// # of games
	
	for($i=0; $i<$g_ct; $i++) {
		$gid = array_shift($in);	// { game unique ID
		$t1 = array_shift($in);		// { team1 index
		$t1id = $tm_index_to_id[$t1];
		$t2 = array_shift($in);		// { team2 index
		$t2id = $tm_index_to_id[$t2];
		$score1 = array_shift($in);	// team1 score
		$score2 = array_shift($in);	// team2 score
		$tuh = array_shift($in);	// TUH, inc. tiebreakers
		$round = array_shift($in);	// Round number
		$bons1 = array_shift($in);	// Bonus count 1
		$bons2 = array_shift($in);	// Bonus count 2
		$bon_pts1 = array_shift($in); // Bonus pts 1
		$bon_pts2 = array_shift($in); // Bonus pts 2
		$ot = array_shift($in); 	// overtime (1=yes, 0=no) TODO
		$ot_tup1 = array_shift($in); 	// overtime tossups 1 TODO
		$ot_tup2 = array_shift($in); 	// overtime tossups 2 TODO
		$forfeit = array_shift($in); 	// forfeit (1=team1 forfeit, 0=no)
		/* The next two values are unlikely to be supported by QBSQL */
        $light1 = array_shift($in);         // lightning t1 pts
        $light2 = array_shift($in);         // lightning t2 pts
		
        $forfeit = ($forfeit == 1) ? "forfeit = '$t1id'" : "forfeit = NULL";
        
        $query = "INSERT INTO {$mysql_prefix}_rounds SET
        			team1 = '$t1id',
        			team2 = '$t2id',
        			score1 = '$score1',
        			score2 = '$score2',
        			tu_heard = '$tuh',
        			$forfeit,
        			id = '$round'";
        query($query) or dberror("Error adding round $i",$query);
        
        $game_id = mysql_insert_id();
        
        for($j=0; $j <= 7; $j++) {
        	$index = array_shift($in);
        	if(array_key_exists($index, $p_index_to_id[$t1])) {
        		$pid = $p_index_to_id[$t1][$index];
        		$fract = array_shift($in);
        		$ptuh = $fract * $tuh;
        		$pows = array_shift($in);
        		$tups = array_shift($in);
        		$negs = array_shift($in);
        		array_shift($in); // Unused line
        		$tuppts = array_shift($in);
        		$query = "INSERT INTO {$mysql_prefix}_rounds_players SET
        					player_id = '$pid',
        					team_id = '$t1id',
        					powers = '$pows',
        					tossups = '$tups',
        					negs = '$negs',
        					tu_heard = '$ptuh',
        					round_id = '$round',
        					game_id = '$game_id'";
        		query($query) or dberror("Error adding player stats for player $index on team $t1", $query);
        	} else {
        		array_shift($in);
        		array_shift($in);
              	array_shift($in);
        	    array_shift($in);
        	    array_shift($in);
        	    array_shift($in);
        	}
        	$index = array_shift($in);
        	if(array_key_exists($index, $p_index_to_id[$t2])) {
        		$pid = $p_index_to_id[$t2][$index];
        		$fract = array_shift($in);
        		$ptuh = $fract * $tuh;
        		$pows = array_shift($in);
        		$tups = array_shift($in);
        		$negs = array_shift($in);
        		array_shift($in); // Unused line
        		$tuppts = array_shift($in);
        		$query = "INSERT INTO {$mysql_prefix}_rounds_players SET
        					player_id = '$pid',
        					team_id = '$t2id',
        					powers = '$pows',
        					tossups = '$tups',
        					negs = '$negs',
        					tu_heard = '$ptuh',
        					round_id = '$round',
        					game_id = '$game_id'";
        		query($query) or dberror("Error adding player stats for player $index on team $t1", $query);
        	} else {
        		array_shift($in);
        		array_shift($in);
              	array_shift($in);
        	    array_shift($in);
        	    array_shift($in);
        	    array_shift($in);
        	}
        }
        /* The SQBS data file has more here, but we don't need it to work with the
         * NAQT results database, or for our own use.
         */
    }
}

?>
