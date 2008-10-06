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
/* stats_team.php
 *
 * View all statistics for teams, either as a ranked summary or a team detail. 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Team statistics";
 require "head.php";			// Generate header as appropriate
     query("CREATE TEMPORARY TABLE "."$mysql_prefix"."_tut 
		    SELECT SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id 
		    from "."$mysql_prefix"."_teams,"."$mysql_prefix"."_rounds_players where "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id group by team_id") or die(mysql_error());
     query("CREATE TEMPORARY TABLE "."$mysql_prefix"."_statt SELECT 
			    "."$mysql_prefix"."_teams.id as id,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id AND score1>score2) OR (team2="."$mysql_prefix"."_teams.id AND score2>score1),1,0)) AS wins,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id AND score1<score2) OR (team2="."$mysql_prefix"."_teams.id AND score2<score1),1,0)) AS losses,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id OR team2="."$mysql_prefix"."_teams.id) AND score1=score2,1,0)) AS draws,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id,score1,IF(team2="."$mysql_prefix"."_teams.id,score2,0))) AS pts,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id,score2,IF(team2="."$mysql_prefix"."_teams.id,score1,0))) AS opts,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id OR team2="."$mysql_prefix"."_teams.id,{$mysql_prefix}_rounds.tu_heard,0)) AS tuh
				FROM "."$mysql_prefix"."_rounds,"."$mysql_prefix"."_teams
				GROUP BY {$mysql_prefix}_teams.id")  or die(mysql_error());
     $res1=query("SELECT "."$mysql_prefix"."_teams.full_name,"."$mysql_prefix"."_statt.wins,"."$mysql_prefix"."_statt.losses,"."$mysql_prefix"."_statt.draws,
			    FORMAT(wins/(wins+losses+draws), 3) as pct,
			    FORMAT(pts/(wins+losses+draws),2) as ppg,
			    FORMAT(opts/(wins+losses+draws),2) as oppg,
			    FORMAT(pts/tuh,2) as pptuh,
			    FORMAT(opts/tuh,2) as opptuh,
			    FORMAT(pow/neg,2) AS pn,
			    FORMAT((pts-tup)/tuc,2) as bconv
				FROM "."$mysql_prefix"."_statt,"."$mysql_prefix"."_tut,"."$mysql_prefix"."_teams
				WHERE "."$mysql_prefix"."_statt.id="."$mysql_prefix"."_tut.team_id
				    AND "."$mysql_prefix"."_statt.id="."$mysql_prefix"."_teams.id ORDER BY pct DESC, pptuh DESC") or die(mysql_error());
     table($res1,array("Team","W","L","D","Pct.","PPG","OPPG","PPTUH","OPPTUH","P/N","BConv"),11,TRUE,FALSE,"stats");
     free_result($res1);
     query("drop table if exists "."$mysql_prefix"."_statt");
     query("drop table is exists "."$mysql_prefix"."_tut");
 
 require "foot.php";			// finish off page
?>
