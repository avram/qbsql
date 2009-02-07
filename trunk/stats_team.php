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
 

 // If they've asked for a team detail, give it to them
 if (isset($_GET["team"]) && is_numeric($_GET["team"])) {
     $teamid=$_GET["team"];
     $edit_link = ($auth) ? "<a class='edit-team' href='team_modify.php?edit=$teamid&t=$mysql_prefix'>Edit</a>" : "";
     $res_teams = query("SELECT full_name, bracket FROM "."$mysql_prefix"."_teams WHERE id='$teamid'") or die("could not get team info:".mysql_error());
     list($teamname, $bracket) = fetch_row($res_teams);
     print "<h2>$teamname $edit_link</h2>";
     if (is_numeric($bracket) && $bracket != 0) {
         print "<h3 class='bracket'>Bracket $bracket</h3>\n";
     }
     $edit_query = ($auth) ? ", concat(\"<a href='roster_modify.php?edit=\",{$mysql_prefix}_players.id,\"&t={$mysql_prefix}'>Edit</a>\")" : "";
     $res1 = query("SELECT CONCAT('<a href=\"stats_individual.php?t={$mysql_prefix}&player=',
                                    {$mysql_prefix}_players.id, '\">',
                                    {$mysql_prefix}_players.first_name,' ',{$mysql_prefix}_players.last_name,
                                    '</a>'),
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)) AS pow,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)) AS neg,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5) as tp,
			    FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)),2) AS pptuh,
			    FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0)*15+IF("."$mysql_prefix"."_players.id=player_id,tossups,0)*10-IF("."$mysql_prefix"."_players.id=player_id,negs,0)*5)/SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0))*{$tourney_game_length},2) AS ppg,
			    FORMAT(SUM(IF("."$mysql_prefix"."_players.id=player_id,powers,0))/SUM(IF("."$mysql_prefix"."_players.id=player_id,negs,0)),2) as pn,
			    SUM(IF("."$mysql_prefix"."_players.id=player_id,tu_heard,0)) AS tuh $edit_query
                            FROM "."$mysql_prefix"."_teams, "."$mysql_prefix"."_players
                            LEFT JOIN {$mysql_prefix}_rounds_players 
                                ON {$mysql_prefix}_rounds_players.player_id = {$mysql_prefix}_players.id
                        WHERE 
                            "."$mysql_prefix"."_players.team = $teamid
                            AND {$mysql_prefix}_teams.id = {$mysql_prefix}_players.team 
			GROUP BY {$mysql_prefix}_players.id
			ORDER BY pptuh DESC") or die(mysql_error());
     table($res1,array("Name","15","10","-5","Pts.","PPTUH","PPG","P/N","TUH"),9,TRUE,FALSE,"stats",array());
     free_result($res1);
?>
    <h4>Games</h4>
<?php
     // Now get the info by round. 
     $edit_query = ($auth) ? ", concat(\"<a href='add_game.php?edit=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Edit</a>\")" : "";
 $detail = ", CONCAT(\"<a href='game_detail.php?game=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Detail</a>\")";
 
     $res1 = query("SELECT {$mysql_prefix}_rounds.id,
			    CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=',
			         IF(score1>=score2,{$mysql_prefix}_tut1.team_id,
			                         {$mysql_prefix}_tut2.team_id), '\">', 
			         IF(score1>=score2,t1.full_name,t2.full_name),
			     '</a>') AS name1,
			    IF(score1>=score2,score1,score2) AS winscore,
			    FORMAT(IF(score1>=score2,(score1-{$mysql_prefix}_tut1.tup)/"."$mysql_prefix"."_tut1.tuc,(score2-"."$mysql_prefix"."_tut2.tup)/"."$mysql_prefix"."_tut2.tuc),2) AS winconv,
			    CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=',
			         IF(score1>=score2,{$mysql_prefix}_tut2.team_id,
			                         {$mysql_prefix}_tut1.team_id), '\">', 
			         IF(score1>=score2,t2.full_name,t1.full_name),
			     '</a>') AS name2,
			    IF(score1>=score2,score2,score1) AS losescore,
			    FORMAT(IF(score1>=score2,(score2-{$mysql_prefix}_tut2.tup)/"."$mysql_prefix"."_tut2.tuc,(score1-"."$mysql_prefix"."_tut1.tup)/"."$mysql_prefix"."_tut1.tuc),2) AS loseconv,
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
                            AND (t1.id=$teamid OR t2.id=$teamid)
			ORDER BY "."$mysql_prefix"."_rounds.id ASC") or die(mysql_error());
     table($res1,array("Round","W","","BConv","L","","BConv","Margin","Detail"),9,TRUE,FALSE,"stats",array());
     free_result($res1);
 }

 // If they haven't asked for anything, or they asked wrong, show the list
     if (!isset($_GET["team"]) || !is_numeric($_GET["team"])) {
         // See if we have brackets.
        $brackets = fetch_brackets();
        if (count($brackets) == 0)
            $brackets[] = 0;
        
        // Now see if we're missing anyone. If are NULL-bracket teams, add a 0-bracket
        $res = query("SELECT COUNT(*) FROM {$mysql_prefix}_teams WHERE bracket IS NULL");
        list($null_ct) = fetch_row($res);
        if($null_ct > 0)
            $brackets[] = 0;

        foreach($brackets as $bracket) {
            if($bracket != 0) {
                print "<h2>Bracket $bracket</h2>\n";
            }
            $brk_q = " AND {$mysql_prefix}_teams.bracket = '$bracket' ";

            $res1=query("SELECT CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=', {$mysql_prefix}_statt.id, '\">', {$mysql_prefix}_teams.full_name,
                                '</a>'),
     {$mysql_prefix}_statt.wins,"."$mysql_prefix"."_statt.losses,"."$mysql_prefix"."_statt.draws,
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
                                AND "."$mysql_prefix"."_statt.id="."$mysql_prefix"."_teams.id
                                $brk_q ORDER BY pct DESC, pptuh DESC") or die(mysql_error());
            table($res1,array("Team","W","L","D","Pct.","PPG","OPPG","PPTUH","OPPTUH","P/N","BConv"),11,TRUE,FALSE,"stats",array("ranked"));
            free_result($res1);
        }

 }
 require "foot.php";			// finish off page
?>
