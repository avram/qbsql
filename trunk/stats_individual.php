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

 $res1 = query("SELECT 
                            CONCAT("."$mysql_prefix"."_players.first_name,\" \","."$mysql_prefix"."_players.last_name),
			    "."$mysql_prefix"."_teams.short_name,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)) AS pow,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) AS neg,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5) as tp,
                            FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)),2) AS pptuh,
                            FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0))*{$tourney_game_length},2) AS ppg,
			    FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0))/SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)),2) as pn,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS tuh
                            FROM "."$mysql_prefix"."_players,
                                "."$mysql_prefix"."_rounds_players,
                                "."$mysql_prefix"."_teams
                            WHERE "."$mysql_prefix"."_rounds_players.team_id="."$mysql_prefix"."_teams.id 
                                 AND "."$mysql_prefix"."_players.id="."$mysql_prefix"."_rounds_players.player_id
                            GROUP BY player_id ORDER BY pptuh DESC") or die(mysql_error());
     table($res1,array("Name","Team","15","10","-5","Pts.","PPTUH","PPG","P/N","TUH"),10,TRUE,FALSE,"stats",array("ranked"));
 free_result($res1);
?>
    <p><strong>Note:</strong> The PPG above is calculated on the basis of tossups heard, 
not number of games played. (<?=$tourney_game_length?> tossups per game)</p>
<?php
 
 require "foot.php";			// finish off page
?>
