<?php
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
     $edit_query = ($auth) ? ", concat(\"<a href='roster_modify.php?edit=\",{$mysql_prefix}_players.id,\"&t={$mysql_prefix}'>Edit</a>\")" : "";
     $res1 = query("SELECT CONCAT("."$mysql_prefix"."_players.first_name,\" \","."$mysql_prefix"."_players.last_name),
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)) AS pow,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) AS neg,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5) as tp,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS pptuh,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0))/SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) as pn,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS tuh $edit_query
                            FROM "."$mysql_prefix"."_teams, "."$mysql_prefix"."_players
                            LEFT JOIN {$mysql_prefix}_rounds_players 
                                ON {$mysql_prefix}_rounds_players.player_id = {$mysql_prefix}_players.id
                        WHERE 
                            "."$mysql_prefix"."_players.team = $teamid
                            AND {$mysql_prefix}_teams.id = {$mysql_prefix}_players.team 
			GROUP BY {$mysql_prefix}_players.id
			ORDER BY pptuh DESC");
     table($res1,array("Name","15","10","-5","Pts.","PPTUH","P/N","TUH"),8,TRUE,FALSE,"stats",array());
 }
     free_result($res1);
 
 require "foot.php";			// finish off page
?>
