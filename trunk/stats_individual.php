<?php
/* stats_individual.php
 *
 * View all statistics for individuals, either as a ranked summary or a detail. 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Individual statistics";
 require "head.php";			// Generate header as appropriate

     $res1 = query("SELECT CONCAT("."$mysql_prefix"."_players.first_name,\" \","."$mysql_prefix"."_players.last_name),
			    "."$mysql_prefix"."_teams.short_name,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)) AS pow,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) AS neg,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5) as tp,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS pptuh,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0))/SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) as pn,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS tuh
				FROM "."$mysql_prefix"."_players,"."$mysql_prefix"."_rounds_players,"."$mysql_prefix"."_teams WHERE "."$mysql_prefix"."_rounds_players.team_id="."$mysql_prefix"."_teams.id AND "."$mysql_prefix"."_players.id="."$mysql_prefix"."_rounds_players.player_id GROUP BY player_id ORDER BY pptuh DESC");
     table($res1,array("Name","Team","15","10","-5","Pts.","PPTUH","P/N","TUH"),9,TRUE,FALSE,"stats");
     free_result($res1);
 
 require "foot.php";			// finish off page
?>
