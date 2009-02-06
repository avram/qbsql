<?php
/* migrations.php
 * An attempt to let database changes propagate by SVN.
 */

// Migrations are generally one-way, unlike in Rails
function migrate($new_rev) {
    // Revisions are introduced in rev 36

    $migrations = array(
        36 => array("CREATE TABLE qb_admin (rev int(11) NOT NULL)",
                    "INSERT INTO qb_admin SET rev = 36;")
    );

    // The first step is to make sure that we have an admin database
    $query = "SHOW TABLES LIKE 'qb_admin'";
    $res = query($query);
    if(!fetch_row($res)) {
        // The admin table is not installed. Migrate there first.
        if (is_array($migrations[36])) {
            foreach ($migrations[36] as $migration) {
                query($migration) or migration_error(36, mysql_error(), mysql_errno());
            }
        } else {
            query($migrations[36]) or migration_error(36, mysql_error(), mysql_errno());
        }
    }
    free_result($res);

    // Now we should be able to look up the revision number.
    $res = query("SELECT rev FROM qb_admin LIMIT 1");
    list($cur_rev) = fetch_row($res);
    free_result($res);

    foreach ($migrations as $rev_no => $migration) {
        if ($cur_rev < $rev_no && $new_rev >= $rev_no) {
            // Run each intermediate migration in order.
            if (is_array($migration)) {
                foreach ($migration as $migration_part) {
                    query($migration_part) or migration_error($rev_no, mysql_error(), mysql_errno());
                }
            } else {
                query($migration) or migration_error($rev_no, mysql_error(), mysql_errno());
            }
        }
    } 
}

function migration_error($revno, $error, $errno) {
    die("Database upgrade to revision #$revno failed. Check data integrity. [($errno) $error]");
}
?>
