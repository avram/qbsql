<?php
/* stats_round.php
 *
 * View results of each round/game 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
$title="Round summaries";
$js_includes = true;
 require "head.php";			// Generate header as appropriate

 $edit_query = ($auth) ? ", concat(\"<a href='add_game.php?edit=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Edit</a>\")" : "";
 $detail = ", CONCAT(\"<a href='game_detail.php?game=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Detail</a>\")";
 
 $res1 = query("SELECT "."$mysql_prefix"."_rounds.id,
			    CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=',
			         IF(score1>=score2,{$mysql_prefix}_tut1.team_id,
			                         {$mysql_prefix}_tut2.team_id), '\">', 
			         IF(score1>=score2,t1.full_name,t2.full_name),
			     '</a>') AS name1,
			    IF(score1>=score2,score1,score2) AS winscore,
			    FORMAT(IF(score1>=score2,(score1-"."$mysql_prefix"."_tut1.tup)/"."$mysql_prefix"."_tut1.tuc,(score2-"."$mysql_prefix"."_tut2.tup)/"."$mysql_prefix"."_tut2.tuc),2) AS winconv,
			    CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=',
			         IF(score1>=score2,{$mysql_prefix}_tut2.team_id,
			                         {$mysql_prefix}_tut1.team_id), '\">', 
			         IF(score1>=score2,t2.full_name,t1.full_name),
			     '</a>') AS name2,
			    IF(score1>=score2,score2,score1) AS losescore,
			    FORMAT(IF(score1>=score2,(score2-"."$mysql_prefix"."_tut2.tup)/"."$mysql_prefix"."_tut2.tuc,(score1-"."$mysql_prefix"."_tut1.tup)/"."$mysql_prefix"."_tut1.tuc),2) AS loseconv,
			    ABS(score1-score2) $detail $edit_query
			FROM
                        (
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id, round_id 
                    from "."$mysql_prefix"."_teams,"."$mysql_prefix"."_rounds_players where "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id group by round_id,team_id
                            ) AS "."$mysql_prefix"."_tut1, 
                        (
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id, round_id 
                    from "."$mysql_prefix"."_teams,"."$mysql_prefix"."_rounds_players where "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id group by round_id,team_id
                            ) AS "."$mysql_prefix"."_tut2,
			    "."$mysql_prefix"."_rounds, 
			    "."$mysql_prefix"."_teams AS t1, "."$mysql_prefix"."_teams AS t2
			WHERE t1.id="."$mysql_prefix"."_rounds.team1
			    AND t2.id="."$mysql_prefix"."_rounds.team2
			    AND "."$mysql_prefix"."_tut1.round_id="."$mysql_prefix"."_rounds.id
			    AND "."$mysql_prefix"."_tut1.team_id=t1.id
			    AND "."$mysql_prefix"."_tut2.round_id="."$mysql_prefix"."_rounds.id
			    AND "."$mysql_prefix"."_tut2.team_id=t2.id
			ORDER BY "."$mysql_prefix"."_rounds.id ASC") or die(mysql_error());
     table($res1,array("Round","W","","BConv","L","","BConv","Margin","Detail"),9,TRUE,FALSE,"stats",array("sort"));
     free_result($res1);
 
 require "foot.php";			// finish off page
?>
