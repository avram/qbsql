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
$js_includes = true;
 require "head.php";			// Generate header as appropriate

 // If they've asked for a team detail, give it to them
 if (isset($_GET["team"]) && is_numeric($_GET["team"])) {
     $teamid=$_GET["team"];
     $edit_link = ($auth) ? "<a class='edit-team' href='team_modify.php?edit=$teamid&t=$mysql_prefix'>Edit</a>" : "";
     $res_teams = query("SELECT full_name, bracket FROM {$mysql_prefix}_teams WHERE id='$teamid'") or die("could not get team info:".mysql_error());
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
			    SUM(IF({$mysql_prefix}_players.id=player_id,powers,0)) AS pow,
			    SUM(IF({$mysql_prefix}_players.id=player_id,tossups,0)) AS tu,
			    SUM(IF({$mysql_prefix}_players.id=player_id,negs,0)) AS neg,
			    SUM(IF({$mysql_prefix}_players.id=player_id,powers,0)*15+IF({$mysql_prefix}_players.id=player_id,tossups,0)*10-IF({$mysql_prefix}_players.id=player_id,negs,0)*5) as tp,
			    FORMAT(SUM(IF({$mysql_prefix}_players.id=player_id,powers,0)*15+IF({$mysql_prefix}_players.id=player_id,tossups,0)*10-IF({$mysql_prefix}_players.id=player_id,negs,0)*5)/SUM(IF({$mysql_prefix}_players.id=player_id,tu_heard,0)),2) AS pptuh,
			    FORMAT(SUM(IF({$mysql_prefix}_players.id=player_id,powers,0)*15+IF({$mysql_prefix}_players.id=player_id,tossups,0)*10-IF({$mysql_prefix}_players.id=player_id,negs,0)*5)/SUM(IF({$mysql_prefix}_players.id=player_id,tu_heard,0))*{$tourney_game_length},2) AS ppg,
			    FORMAT(SUM(IF({$mysql_prefix}_players.id=player_id,powers,0))/SUM(IF({$mysql_prefix}_players.id=player_id,negs,0)),2) as pn,
			    SUM(IF({$mysql_prefix}_players.id=player_id,tu_heard,0)) AS tuh $edit_query
                            FROM {$mysql_prefix}_teams, {$mysql_prefix}_players
                            LEFT JOIN {$mysql_prefix}_rounds_players 
                                ON {$mysql_prefix}_rounds_players.player_id = {$mysql_prefix}_players.id
                        WHERE 
                            {$mysql_prefix}_players.team = $teamid
                            AND {$mysql_prefix}_teams.id = {$mysql_prefix}_players.team 
			GROUP BY {$mysql_prefix}_players.id
			ORDER BY pptuh DESC") or die(mysql_error());
     table($res1,array("Name","15","10","-5","Pts.","PPTUH","PPG","P/N","TUH"),9,TRUE,FALSE,"stats",array("sort" => ""));
     free_result($res1);
?>
    <h4>Games</h4>
<?php
     // This is the not so pretty yet new style that people wanted.
     // Now get the info by round. 
     $edit_query = ($auth) ? ", concat(\"<a href='add_game.php?edit=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Edit</a>\")" : "";
 $detail = ", CONCAT(\"<a href='game_detail.php?game=\",{$mysql_prefix}_rounds.game_id,\"&t=$mysql_prefix'>Detail</a>\")";
     $query="SELECT {$mysql_prefix}_rounds.id,
			    CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=',
			         IF(t1.id=$teamid,
			         	t2.id,
			         	t1.id),
				'\">', 
			         IF(t1.id=$teamid,
			         	t2.full_name,
			         	t1.full_name),
			     '</a>') AS opponent,
			    IF(IF(t1.id=$teamid,score1,score2) > IF(t1.id=$teamid,score2,score1), 'Win',
				IF(IF(t1.id=$teamid,score1,score2) = IF(t1.id=$teamid,score2,score1), 'Draw', 'Loss')) AS result,
			    CONCAT(IF(t1.id=$teamid,score1,score2),' - ',IF(t1.id=$teamid,score2,score1)) AS final,
			    IF(t1.id=$teamid,
			    	{$mysql_prefix}_tut1.pow,
			    	{$mysql_prefix}_tut2.pow)
			    	AS t1pow,
			    IF(t1.id=$teamid,
			    	{$mysql_prefix}_tut1.tups,
			    	{$mysql_prefix}_tut2.tups)
			    	AS t1tups,
			    IF(t1.id=$teamid,
			    	{$mysql_prefix}_tut1.neg,
			    	{$mysql_prefix}_tut2.neg)
			    	AS t1negs,
			    FORMAT(IF(t1.id=$teamid,
			    	(score1-{$mysql_prefix}_tut1.tup)/({$mysql_prefix}_tut1.tuc - {$mysql_prefix}_rounds.ot_tossups1),
			    	(score2-{$mysql_prefix}_tut2.tup)/({$mysql_prefix}_tut2.tuc - {$mysql_prefix}_rounds.ot_tossups2)),2)
			    	AS t1conv,
			    IF(t2.id=$teamid,
			    	{$mysql_prefix}_tut1.pow,
			    	{$mysql_prefix}_tut2.pow)
			    	AS t2pow,
			    IF(t2.id=$teamid,
			    	{$mysql_prefix}_tut1.tups,
			    	{$mysql_prefix}_tut2.tups)
			    	AS t2tups,
			    IF(t2.id=$teamid,
			    	{$mysql_prefix}_tut1.neg,
			    	{$mysql_prefix}_tut2.neg)
			    	AS t2negs,
			    FORMAT(IF(t1.id=$teamid,
			    	(score2-{$mysql_prefix}_tut2.tup)/({$mysql_prefix}_tut2.tuc - {$mysql_prefix}_rounds.ot_tossups2),
			    	(score1-{$mysql_prefix}_tut1.tup)/({$mysql_prefix}_tut1.tuc - {$mysql_prefix}_rounds.ot_tossups1)),2)
			    	AS t2conv
			    $detail $edit_query
			FROM
                        (
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(tossups) as tups,
		    SUM(negs) AS neg,
		    team_id, game_id 
                    from {$mysql_prefix}_teams,{$mysql_prefix}_rounds_players where {$mysql_prefix}_teams.id={$mysql_prefix}_rounds_players.team_id group by game_id,team_id
                            ) AS {$mysql_prefix}_tut1, 
                        (
		    select SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
		    SUM(tossups) as tups,
		    SUM(negs) AS neg,
		    team_id, game_id 
                    from {$mysql_prefix}_teams,{$mysql_prefix}_rounds_players where {$mysql_prefix}_teams.id={$mysql_prefix}_rounds_players.team_id group by game_id,team_id
                            ) AS {$mysql_prefix}_tut2,
			    {$mysql_prefix}_rounds, 
			    {$mysql_prefix}_teams AS t1, {$mysql_prefix}_teams AS t2
			WHERE t1.id={$mysql_prefix}_rounds.team1
			    AND t2.id={$mysql_prefix}_rounds.team2
			    AND {$mysql_prefix}_tut1.game_id={$mysql_prefix}_rounds.game_id
			    AND {$mysql_prefix}_tut1.team_id=t1.id
			    AND {$mysql_prefix}_tut2.game_id={$mysql_prefix}_rounds.game_id
                            AND {$mysql_prefix}_tut2.team_id=t2.id
                            AND (t1.id=$teamid OR t2.id=$teamid)
			ORDER BY {$mysql_prefix}_rounds.id, {$mysql_prefix}_rounds.game_id ASC";
//	 print "<textarea>$query</textarea>";
	 $res1= query($query) or dberror("Error on team detail",$query);
     table($res1,array("Round","Opponent","Result","Score",
			"Pow","T/U","Neg","BConv",
			"OPow","O T/U","ONeg","OBConv",
			"Detail"),13,TRUE,FALSE,"stats",array("sort"=>""));
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

        sort($brackets);

        $table = new StatsTable(array("Team","W","L","D","Pct.","PPG","OPPG","PPTUH","OPPTUH","P/N","15","10","-5","TUH","BConv"),TRUE,TRUE,"stats");

        foreach($brackets as $bracket) {
            if($bracket != 0) {
                $table->interstitial("Bracket $bracket");
            }

            // Place headers after bracket name
            $table->names();
            
            $brk_q = " AND {$mysql_prefix}_teams.bracket = '$bracket' ";
			
            /* Average SOS
             * This should be expanded to use all the various statistics...
             */
            /*$query = "SELECT FORMAT(AVG(sos_oppth.unnorm / (wins+losses+draws)),3),
            				 FORMAT(AVG(sos_oppth_inc.unnorm / (wins+losses+draws)),3)
            			FROM 
                            (SELECT 
			    {$mysql_prefix}_teams.id as id,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND score1>score2) OR (team2={$mysql_prefix}_teams.id AND score2>score1),1,0)) AS wins,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND score1<score2) OR (team2={$mysql_prefix}_teams.id AND score2<score1),1,0)) AS losses,
			    SUM(IF((team1={$mysql_prefix}_teams.id OR team2={$mysql_prefix}_teams.id) AND score1=score2,1,0)) AS draws,
			    SUM(IF(team1={$mysql_prefix}_teams.id,score1,IF(team2={$mysql_prefix}_teams.id,score2,0))) AS pts,
			    SUM(IF(team1={$mysql_prefix}_teams.id,score2,IF(team2={$mysql_prefix}_teams.id,score1,0))) AS opts,
			    SUM(IF(team1={$mysql_prefix}_teams.id,ot_tossups1,IF(team2={$mysql_prefix}_teams.id,ot_tossups2,0))) AS ot_tups,
			    SUM(IF(team1={$mysql_prefix}_teams.id OR team2={$mysql_prefix}_teams.id,{$mysql_prefix}_rounds.tu_heard,0)) AS tuh,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND team2 = forfeit)
			    		OR (team2={$mysql_prefix}_teams.id AND team1 = forfeit),1,0)) AS forfeitwins,
			    SUM(IF({$mysql_prefix}_teams.id = forfeit,1,0)) AS forfeitlosses
				FROM {$mysql_prefix}_rounds,{$mysql_prefix}_teams
				GROUP BY {$mysql_prefix}_teams.id)
						AS {$mysql_prefix}_statt,
				{$mysql_prefix}_teams
                LEFT JOIN ((SELECT (rp.powers*15+rp.tossups*10-rp.negs*5) / r.tu_heard * matches.ct
                					AS unnorm,
                			t.id AS id
                		FROM {$mysql_prefix}_teams AS t,
                			{$mysql_prefix}_rounds AS r,
                			(SELECT t1.id AS tm1, t2.id AS tm2, COUNT(*) AS ct
                				FROM {$mysql_prefix}_rounds AS r,
                					{$mysql_prefix}_teams AS t1,
                					{$mysql_prefix}_teams AS t2
                				WHERE ((r.team1 = t1.id
                					AND r.team2 = t2.id) OR
                					(r.team1 = t2.id AND r.team2=t1.id))
                				GROUP BY t1.id, t2.id) AS matches,
                			(SELECT SUM(powers) AS powers,
                					SUM(tossups) AS tossups,
                					SUM(negs) AS negs,
                					team_id,
                					game_id
                				FROM {$mysql_prefix}_rounds_players
                			GROUP BY game_id) AS rp
                		WHERE r.game_id = rp.game_id
            				AND (r.team1 != t.id AND r.team2 != t.id)
            				AND matches.tm1 = t.id AND matches.tm2 = rp.team_id
                		GROUP BY t.id) AS sos_oppth,
                	(SELECT (rp.powers*15+rp.tossups*10-rp.negs*5) / r.tu_heard * matches.ct
                					AS unnorm,
                			t.id AS id
                		FROM {$mysql_prefix}_teams AS t,
                			{$mysql_prefix}_rounds AS r,
                			(SELECT t1.id AS tm1, t2.id AS tm2, COUNT(*) AS ct
                				FROM {$mysql_prefix}_rounds AS r,
                					{$mysql_prefix}_teams AS t1,
                					{$mysql_prefix}_teams AS t2
                				WHERE ((r.team1 = t1.id
                					AND r.team2 = t2.id) OR
                					(r.team1 = t2.id AND r.team2=t1.id))
                				GROUP BY t1.id, t2.id) AS matches,
                			(SELECT SUM(powers) AS powers,
                					SUM(tossups) AS tossups,
                					SUM(negs) AS negs,
                					team_id,
                					game_id
                				FROM {$mysql_prefix}_rounds_players
                			GROUP BY game_id) AS rp
                		WHERE r.game_id = rp.game_id
            				AND matches.tm1 = t.id AND matches.tm2 = rp.team_id
                		GROUP BY t.id) AS sos_oppth_inc)
                	ON (sos_oppth.id = {$mysql_prefix}_teams.id AND sos_oppth_inc.id = {$mysql_prefix}_teams.id)
                	WHERE {$mysql_prefix}_statt.id={$mysql_prefix}_teams.id";
            $res0 = query($query) or dberror("Error getting SOS avg", $query);
            list($avg_sos,$avg_sos_inc) = fetch_row($res0);
            */
            
            $query = "SELECT CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=', {$mysql_prefix}_statt.id, '\">', {$mysql_prefix}_teams.full_name,
                                '</a>'),
     			wins+forfeitwins,
            	losses+forfeitlosses,
            	draws,
			    FORMAT((wins+forfeitwins)/(wins+losses+draws+forfeitwins+forfeitlosses), 3) as pct,
			    FORMAT(pts/(wins+losses+draws),2) as ppg,
			    FORMAT(opts/(wins+losses+draws),2) as oppg,
			    FORMAT(pts/tuh,2) as pptuh,
			    FORMAT(opts/tuh,2) as opptuh,
			    FORMAT(pow/neg,2) AS pn,
			    pow,
			    tups,
			    neg,
                tuh,
                FORMAT((pts-tup)/(tuc-ot_tups),2) as bconv
                FROM 
                            (SELECT 
			    {$mysql_prefix}_teams.id as id,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND score1>score2) OR (team2={$mysql_prefix}_teams.id AND score2>score1),1,0)) AS wins,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND score1<score2) OR (team2={$mysql_prefix}_teams.id AND score2<score1),1,0)) AS losses,
			    SUM(IF((team1={$mysql_prefix}_teams.id OR team2={$mysql_prefix}_teams.id) AND score1=score2,1,0)) AS draws,
			    SUM(IF(team1={$mysql_prefix}_teams.id,score1,IF(team2={$mysql_prefix}_teams.id,score2,0))) AS pts,
			    SUM(IF(team1={$mysql_prefix}_teams.id,score2,IF(team2={$mysql_prefix}_teams.id,score1,0))) AS opts,
			    SUM(IF(team1={$mysql_prefix}_teams.id,ot_tossups1,IF(team2={$mysql_prefix}_teams.id,ot_tossups2,0))) AS ot_tups,
			    SUM(IF(team1={$mysql_prefix}_teams.id OR team2={$mysql_prefix}_teams.id,{$mysql_prefix}_rounds.tu_heard,0)) AS tuh,
			    SUM(IF((team1={$mysql_prefix}_teams.id AND team2 = forfeit)
			    		OR (team2={$mysql_prefix}_teams.id AND team1 = forfeit),1,0)) AS forfeitwins,
			    SUM(IF({$mysql_prefix}_teams.id = forfeit,1,0)) AS forfeitlosses
				FROM {$mysql_prefix}_rounds,{$mysql_prefix}_teams
				GROUP BY {$mysql_prefix}_teams.id) AS {$mysql_prefix}_statt,
                            (SELECT SUM(powers*15+tossups*10+negs*(-5)) as tup,
		    SUM(powers+tossups) as tuc,
		    SUM(powers) as pow,
                    SUM(negs) AS neg,
                    SUM(tossups) as tups,
		    team_id
                    FROM {$mysql_prefix}_teams, {$mysql_prefix}_rounds_players
                    WHERE {$mysql_prefix}_teams.id={$mysql_prefix}_rounds_players.team_id GROUP BY team_id) AS {$mysql_prefix}_tut,
                {$mysql_prefix}_teams
				WHERE {$mysql_prefix}_statt.id={$mysql_prefix}_tut.team_id
                                AND {$mysql_prefix}_statt.id={$mysql_prefix}_teams.id
                                $brk_q ORDER BY pct DESC, pptuh DESC";
            //echo "<textarea rows=50 cols=90>$query</textarea>";
            $res1 = query($query) or dberror("Error fetching team results.",$query);
            $table->body_res($res1);
            free_result($res1);
            
            // See how many teams are in the bracket
            $res = query("SELECT COUNT(*) FROM {$mysql_prefix}_teams WHERE bracket = '$bracket'");
            list($tm_ct) = fetch_row($res);

            $table->next_rank($table->next_rank + $tm_ct);

        }

        print $table->table();
        //print "<p>Average SOS: $avg_sos (Inclusive: $avg_sos_inc)</p>";
 }
 require "foot.php";			// finish off page
?>
