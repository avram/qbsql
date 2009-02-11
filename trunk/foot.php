<?php
/*
 * foot.php
 *
 * Finishes off the dirty work. This should close MySQL connections, close
 * open tags, add navigation links... Maybe even some banners/insignia.
 * 
 * Uses $link, from init.php
 * 
 * Author: Avram Lyon
 * Created: 21 February 2004
 */
    close($link);
?>
<ul class="nav">
 <li><a href="index.php?t=<?=$mysql_prefix?>">Tournament Home</a></li>
 <li><a href="rosters.php?t=<?=$mysql_prefix?>">Team rosters</a></li>
 <li><a href="stats_round.php?t=<?=$mysql_prefix?>">Round summaries</a></li>
 <li><a href="stats_individual.php?t=<?=$mysql_prefix?>">Individual statistics</a></li>
 <li><a href="stats_team.php?t=<?=$mysql_prefix?>">Team statistics</a></li>
</ul>
<?php if ($auth) { ?>
<ul class="nav">
 <li><a href="roster_modify.php?t=<?=$mysql_prefix?>">Add players</a></li>
 <li><a href="team_modify.php?t=<?=$mysql_prefix?>">Add teams</a></li>
 <li><a href="add_game.php?t=<?=$mysql_prefix?>">Add a game</a></li>
 <li><a href="tournament_admin.php?t=<?=$mysql_prefix?>">Tournament Admin</a></li>
</ul>
<?php }
powered_by();
?>
</body>
</html>
