<?php
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
     $res1=query("SELECT "."$mysql_prefix"."_teams.full_name,"."$mysql_prefix"."_statt.wins,"."$mysql_prefix"."_statt.losses,"."$mysql_prefix"."_statt.draws,
			    FORMAT(wins/(wins+losses+draws), 3) as pct,
			    FORMAT(pts/(wins+losses+draws),2) as ppg,
			    FORMAT(opts/(wins+losses+draws),2) as oppg,
			    FORMAT(pts/tuh,2) as pptuh,
			    FORMAT(opts/tuh,2) as opptuh,
			    FORMAT(pow/neg,2) AS pn,
			    FORMAT((pts-tup)/tuc,2) as bconv
                            FROM 
                            (SELECT 
			    "."$mysql_prefix"."_teams.id as id,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id AND score1>score2) OR (team2="."$mysql_prefix"."_teams.id AND score2>score1),1,0)) AS wins,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id AND score1<score2) OR (team2="."$mysql_prefix"."_teams.id AND score2<score1),1,0)) AS losses,
			    SUM(IF((team1="."$mysql_prefix"."_teams.id OR team2="."$mysql_prefix"."_teams.id) AND score1=score2,1,0)) AS draws,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id,score1,IF(team2="."$mysql_prefix"."_teams.id,score2,0))) AS pts,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id,score2,IF(team2="."$mysql_prefix"."_teams.id,score1,0))) AS opts,
			    SUM(IF(team1="."$mysql_prefix"."_teams.id OR team2="."$mysql_prefix"."_teams.id,{$mysql_prefix}_rounds.tu_heard,0)) AS tuh
				FROM "."$mysql_prefix"."_rounds,"."$mysql_prefix"."_teams
				GROUP BY {$mysql_prefix}_teams.id) AS "."$mysql_prefix"."_statt,
                            (SELECT SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(negs) AS neg,
		    team_id 
                    FROM "."$mysql_prefix"."_teams, "."$mysql_prefix"."_rounds_players
                    WHERE "."$mysql_prefix"."_teams.id="."$mysql_prefix"."_rounds_players.team_id GROUP BY team_id) AS "."$mysql_prefix"."_tut,
                             "."$mysql_prefix"."_teams
				WHERE "."$mysql_prefix"."_statt.id="."$mysql_prefix"."_tut.team_id
				    AND "."$mysql_prefix"."_statt.id="."$mysql_prefix"."_teams.id ORDER BY pct DESC, pptuh DESC") or die(mysql_error());
     table($res1,array("Team","W","L","D","Pct.","PPG","OPPG","PPTUH","OPPTUH","P/N","BConv"),11,TRUE,FALSE,"stats",array("ranked"));
     free_result($res1);
 
 require "foot.php";			// finish off page
?>
