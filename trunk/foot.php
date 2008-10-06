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
