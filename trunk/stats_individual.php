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


 // If they've asked for a player detail, give it to them
 if (isset($_GET["player"]) && is_numeric($_GET["player"])) {
     $playerid=$_GET["player"];
     $res_players = query("SELECT CONCAT({$mysql_prefix}_players.first_name, ' ', {$mysql_prefix}_players.last_name),
                {$mysql_prefix}_teams.full_name, {$mysql_prefix}_teams.id
                FROM {$mysql_prefix}_players, {$mysql_prefix}_teams
                WHERE {$mysql_prefix}_players.id='$playerid'
                    AND {$mysql_prefix}_players.team = {$mysql_prefix}_teams.id") or die("could not get player info:".mysql_error());
     list($playername, $teamname, $teamid) = fetch_row($res_players);
     print "<h2>$playername (<a href='stats_team.php?t={$mysql_prefix}&team=$teamid'>$teamname</a>) <a class='edit-player' href='roster_modify.php?edit=$playerid&t={$mysql_prefix}'>Edit</a></h2>";
     free_result($res_players);

 $edit_query = ($auth) ? ", concat(\"<a href='add_game.php?edit=\",r.game_id,\"&t=$mysql_prefix'>Edit</a>\")" : "";
 $detail = ", CONCAT(\"<a href='game_detail.php?game=\",r.game_id,\"&t=$mysql_prefix'>Detail</a>\")";

 // Now print round-by-round stats
     // Round, Opponent, W/L/D, Pow, Tup, Neg, Pts, P/N, TUH
     $res1 = query("SELECT r.id, CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=', t2.id, '\">', t2.full_name, '</a>'),
         IF(IF(t2.id = r.team1, r.score1-r.score2, r.score2-r.score1) > 0, 'L', IF(IF(t2.id = r.team1, r.score1-r.score2, r.score2-r.score1) < 0, 'W', 'D')),
         rp.powers,
         rp.tossups,
         rp.negs,
         rp.powers*15 + rp.tossups*10 - rp.negs*5,
         FORMAT(rp.powers / rp.negs, 2),
         rp.tu_heard $detail $edit_query
     FROM {$mysql_prefix}_teams AS t1,
        {$mysql_prefix}_teams AS t2,
        {$mysql_prefix}_rounds AS r,
        {$mysql_prefix}_rounds_players AS rp
    WHERE ((t1.id = r.team1 AND t2.id = r.team2)
        OR (t1.id = r.team2 AND t2.id = r.team1))
        AND t1.id = '$teamid'
        AND rp.player_id = '$playerid'
        AND rp.game_id = r.game_id
        ORDER BY r.id ASC") or die(mysql_error());
     table($res1,array("Round","Opponent","Result","15","10","-5","Pts.","P/N", "TUH","Detail"),9,TRUE,FALSE,"stats",array());
     free_result($res1);
 }

 // If they haven't asked for anything, or they asked wrong, show the list
 if (!isset($_GET["player"]) || !is_numeric($_GET["player"])) {
     $res1 = query("SELECT CONCAT('<a href=\"stats_individual.php?t={$mysql_prefix}&player=',
                                    {$mysql_prefix}_players.id, '\">',
                                    {$mysql_prefix}_players.first_name,' ',{$mysql_prefix}_players.last_name,
                                    '</a>'),
                            CONCAT('<a href=\"stats_team.php?t={$mysql_prefix}&team=', {$mysql_prefix}_teams.id, '\">',
                            {$mysql_prefix}_teams.short_name, '</a>'),
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
 }
 
 require "foot.php";			// finish off page
?>
