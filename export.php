<?php
/** export.php
 * An export mechanism.
 *
 * To dump the SQL queries necessary to recreate the tournaments
 * on this host in a new QBSQL install, replace the line:
 *   print sqbs_export_tourney($mysql_prefix);
 * with
 *   export_database();
 * Note, however, that that will reveal all of the tournament passwords.
 *
 * The default behavior is to output something like the SQBS format, which is
 * sufficient for the NAQT results database. It probably isn't enough to be
 * imported by SQBS itself.
 */

/* init.php */
require_once("init.php");
require("require_admin.php");
$link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');

if(!headers_sent())
    header("Content-Type: text/plain; charset=UTF-8");

// To export the entire database, change the next line as directed above.
// Note the warning above.
print sqbs_export_tourney($mysql_prefix);
?>
