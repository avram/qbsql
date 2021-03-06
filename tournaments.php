<?php
/* tournaments.php
 *
 * Author: Avram Lyon 
 */
    require_once("functions.php");
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');

$title = "Tournament List";
$tourney_name = $title;
require "head.php";			// Generate header as appropriate

print "<ul>\n";
$res = query("SELECT name, prefix, locked, description FROM tournaments ORDER BY id DESC");
while (list($name, $prefix, $lock, $desc)=fetch_row($res)) {
    print "<li class='tourney-list'><a href='index.php?t=$prefix'>$name</a><div>$desc</div></li>\n";
}
print "</ul>";
?>
<p><a href="tournament_modify.php">[New Tournament]</a></p>
<?php powered_by(); ?>
</body>
</html>
