<?php
/* migrations.php
 * An attempt to let database changes propagate by SVN.
 */

// Migrations are generally one-way, unlike in Rails
function migrate($new_rev) {
    // Revisions are introduced in rev 36

    $migrations = array(
        36 => array("CREATE TABLE qb_admin (rev int(11) NOT NULL)",
            "INSERT INTO qb_admin SET rev = 36;"),
        37 => array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN tiebreakers int(20) default NULL")
    );

    // The first step is to make sure that we have an admin database
    $query = "SHOW TABLES LIKE 'qb_admin'";
    $res = query($query);
    if(!fetch_row($res)) {
        // The admin table is not installed. Migrate there first.
        migration_apply($migrations[36], 36);
    }
    free_result($res);

    // Now we should be able to look up the revision number.
    $res = query("SELECT rev FROM qb_admin LIMIT 1");
    list($cur_rev) = fetch_row($res);
    free_result($res);

    // TODO somehow check that we succeeded
    foreach ($migrations as $rev_no => $migration) {
        if ($cur_rev < $rev_no && $new_rev >= $rev_no) {
            // Run each intermediate migration in order.
            migration_apply($migration, $rev_no);
        }
    } 
    query("UPDATE qb_admin SET rev = '$new_rev'");
}

function migration_apply($migration, $rev_no) {
    if(is_array($migration)) {
        foreach($migration as $key => $migration_part) {
            // If there is a prefix substitution, then apply it
            if($key = "PFX") {
                // First, get all the prefixes.
                $res = query("SELECT prefix FROM tournaments");
                // For each prefix, substitute and run the migration
                while(list($prefix) = fetch_row($res)) {
                    $part = str_replace("PFX", $prefix, $migration_part);
                    migration_apply($part, $rev_no);
                }
            } else {
                migration_apply($migration_part, $rev_no);
            }
        }
    } else {
        if ($debug)
            query($migration) or migration_error($rev_no [$migration], mysql_error(), mysql_errno());
        else 
            query($migration) or migration_error($rev_no, mysql_error(), mysql_errno());
    }
}

function migration_error($revno, $error, $errno) {
    if ($debug)
        warning("Database upgrade to revision #$revno failed. Check data integrity. [($errno) $error]");
    else
        warning("Database upgrade to revision #$revno failed. Check data integrity. [Error #$errno]");
}
?>
