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
/* stats_round.php
 *
 * View results of each round/game 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Round summaries";
 require "head.php";			// Generate header as appropriate

     query("CREATE TEMPORARY TABLE "."$mysql_prefix"."_tut1 
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id, round_id 
		    from "."$mysql_prefix"."_teams,"."$mysql_prefix"."_rounds_players where "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id group by round_id,team_id") or die(mysql_error());
     query("CREATE TEMPORARY TABLE "."$mysql_prefix"."_tut2 
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id, round_id 
		    from "."$mysql_prefix"."_teams,"."$mysql_prefix"."_rounds_players where "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id group by round_id,team_id") or die(mysql_error());
	$edit_query = ($auth) ? ", concat(\"<a href='add_game.php?edit=\",{$mysql_prefix}_rounds.game_id,\"'>Edit</a>\")" : "";
     $res1 = query("SELECT "."$mysql_prefix"."_rounds.id,
			    IF(score1>=score2,t1.full_name,t2.full_name) AS name1,
			    IF(score1>=score2,score1,score2) AS winscore,
			    IF(score1>=score2,(score1-"."$mysql_prefix"."_tut1.tup)/"."$mysql_prefix"."_tut1.tuc,(score2-"."$mysql_prefix"."_tut2.tup)/"."$mysql_prefix"."_tut2.tuc) AS winconv,
			    IF(score1>=score2,t2.full_name,t1.full_name) AS name2,
			    IF(score1>=score2,score2,score1) AS losescore,
			    IF(score1>=score2,(score2-"."$mysql_prefix"."_tut2.tup)/"."$mysql_prefix"."_tut2.tuc,(score1-"."$mysql_prefix"."_tut1.tup)/"."$mysql_prefix"."_tut1.tuc) AS loseconv,
			    ABS(score1-score2)$edit_query
			FROM
			    "."$mysql_prefix"."_tut1, "."$mysql_prefix"."_tut2,
			    "."$mysql_prefix"."_rounds, 
			    "."$mysql_prefix"."_teams AS t1, "."$mysql_prefix"."_teams AS t2
			WHERE t1.id="."$mysql_prefix"."_rounds.team1
			    AND t2.id="."$mysql_prefix"."_rounds.team2
			    AND "."$mysql_prefix"."_tut1.round_id="."$mysql_prefix"."_rounds.id
			    AND "."$mysql_prefix"."_tut1.team_id=t1.id
			    AND "."$mysql_prefix"."_tut2.round_id="."$mysql_prefix"."_rounds.id
			    AND "."$mysql_prefix"."_tut2.team_id=t2.id
			ORDER BY "."$mysql_prefix"."_rounds.id ASC") or die(mysql_error());
     table($res1,array("Round","W","","BConv","L","","BConv","Margin"),8,TRUE,FALSE,"stats");
     free_result($res1);
     query("DROP TABLE IF EXISTS "."$mysql_prefix"."_tut1");
     query("DROP TABLE IF EXISTS "."$mysql_prefix"."_tut2");
 
 require "foot.php";			// finish off page
?>
