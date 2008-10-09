<?php
/* tournaments.php
 *
 * Author: Avram Lyon 
 */
    require_once("functions.php");
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');

$title = "Tournament List";
require "head.php";			// Generate header as appropriate

print "<ul>\n";
$res = query("SELECT name, prefix, locked, description FROM tournaments ORDER BY id DESC");
while (list($name, $prefix, $lock, $desc)=fetch_row($res)) {
    print "<li><a href='index.php?t=$prefix'>$name</a> &mdash; $desc</li>\n";
}
print "</ul>";
?>
<p><a href="tournament_modify.php">[admin]</a></p>
<p>Tournament stats powered by <a href="http://code.google.com/p/qbsql/"><tt>qbsql</tt></a>.</p>
</body>
</html>
