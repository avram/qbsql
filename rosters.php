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
/* rosters.php
 *
 * View individual and team scores, by roster
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Team Rosters";
 require "head.php";			// Generate header as appropriate

 // get the list of teams:
 $res_teams = query("SELECT "."$mysql_prefix"."_teams.full_name,id FROM "."$mysql_prefix"."_teams ORDER BY full_name") or die("could not get team list:".mysql_error());
 while(list($teamname,$teamid)=fetch_row($res_teams)) {
     print "<h2>$teamname</h2>";
     $edit_query = ($auth) ? ", concat(\"<a href='roster_modify.php?edit=\",{$mysql_prefix}_players.id,\"'>Edit</a>\")" : "";
     $res1 = query("SELECT CONCAT("."$mysql_prefix"."_players.first_name,\" \","."$mysql_prefix"."_players.last_name),
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)) AS pow,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) AS neg,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5) as tp,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS pptuh,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0))/SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) as pn,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS tuh $edit_query
				FROM "."$mysql_prefix"."_players,"."$mysql_prefix"."_rounds_players,"."$mysql_prefix"."_teams
			WHERE "."$mysql_prefix"."_rounds_players.team_id="."$mysql_prefix"."_teams.id
			    AND "."$mysql_prefix"."_players.id="."$mysql_prefix"."_rounds_players.player_id
			    AND "."$mysql_prefix"."_teams.id=$teamid
			GROUP BY player_id
			ORDER BY pptuh DESC");
     table($res1,array("Name","15","10","-5","Pts.","PPTUH","P/N","TUH"),8,TRUE,FALSE,"stats");
 }
     free_result($res1);
 
 require "foot.php";			// finish off page
?>
