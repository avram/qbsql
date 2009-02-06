<?php
/** export.php
 * The beginnings of an export mechanism.
 *
 * As it currently stands, this will dump the SQL queries necessary
 * to recreate the tournaments on this host in a new QBSQL install.
 *
 * It could be easily tweaked to export something more akin to what
 * NAQT wants for its results database.
 */

// To export, comment out the following line:
die();

/* Don't change anything beneath this line */

/* init.php */
    require_once("functions.php");
$link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');

function export_table($name) {
    print "/* Table $name */\n";
    $res = query("SELECT * FROM $name");
    while($row = fetch_row($res)) {
        print 'INSERT INTO '.$name.' VALUES ("';
        print implode('","', $row);
        print "\");\n";
    }
}

function export_tournament($prefix) {
    print "/* Tournament $prefix */\n";
    print table_create_queries($prefix);
    export_table("{$prefix}_players");
    export_table("{$prefix}_rounds");
    export_table("{$prefix}_rounds_players");
    export_table("{$prefix}_teams");
}

function export_database() {
    print "/* Exporting Database */\n";
    $res = query("SELECT * FROM tournaments") or print(mysql_error());
    while($row = fetch_row($res)) {
        print 'INSERT INTO tournaments VALUES ("';
        print implode('","', $row);
        print "\");\n";
        export_tournament($row[2]);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-type" content='text/html; charset="UTF-8"' />
</head>
<body><pre>
<?php
export_database();
?>
</pre></body></html>
