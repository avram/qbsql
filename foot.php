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
 <li><a href="index.php">Stats Home</a></li>
 <li><a href="rosters.php">Team rosters</a></li>
 <li><a href="stats_round.php">Round summaries</a></li>
 <li><a href="stats_individual.php">Individual statistics</a></li>
 <li><a href="stats_team.php">Team statistics</a></li>
</ul>
<ul class="nav">
 <li><a href="roster_modify.php">Add players</a></li>
 <li><a href="team_modify.php">Add teams</a></li>
 <li><a href="add_game.php">Add a game</a></li>
</ul>
</body>
</html>
