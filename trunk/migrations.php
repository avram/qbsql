<?php
/* migrations.php
 * An attempt to let database changes propagate by SVN.
 */

// Migrations are generally one-way, unlike in Rails
function migrate($new_rev) {
    // Revisions are introduced in rev 36

    /* This is an array of migrations to apply. The key is the SVN revision that
     * requires the change, and the value is an array of changes to make.
     * 
     * Since the migration function is recursive, a migration can be an entire
     * array of migrations. If a migration is specified as the key-value pairs:
     * "PFX => migration
     * then every instance of PFX in the migration will be replaced by each of the
     * existing prefixes in the database to run the migration on the specified table
     * of each tournament.
     */
    $migrations = array(
        36 => array("CREATE TABLE qb_admin (rev int(11) NOT NULL)",
            "INSERT INTO qb_admin SET rev = 36;"),
        37 => array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN tiebreakers int(20) default NULL"),
        39 => array("PFX" => "ALTER TABLE PFX_teams ADD COLUMN bracket int(20) default NULL"),
        41 => array("PFX" => "ALTER TABLE PFX_teams MODIFY bracket int(20) default 0 NOT NULL"),
        58 => array("PFX" => "ALTER TABLE PFX_players ADD COLUMN naqtid varchar(30) default NULL"),
		65 => array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN forfeit int(20) default NULL"),
		79 => array(array("PFX" => "ALTER TABLE PFX_rounds DROP COLUMN tiebreakers"),
					array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN ot int(20) default 0 NOT NULL"),
					array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN ot_tossups1 int(20) default 0 NOT NULL"),
					array("PFX" => "ALTER TABLE PFX_rounds ADD COLUMN ot_tossups2 int(20) default 0 NOT NULL"))
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
            //print_r($migration);
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
                    //print $part;
                    migration_apply($part, $rev_no);
                }
            } else {
            	//print $migration_part;
                migration_apply($migration_part, $rev_no);
            }
        }
    } else {
        query($migration) or migration_error($rev_no, mysql_error() . "[$migration]", mysql_errno());
    }
}

function migration_error($revno, $error, $errno) {
	$debug = TRUE;
    if ($debug)
        warning("Database upgrade to revision #$revno failed. Check data integrity. [($errno) $error]");
    else
        warning("Database upgrade to revision #$revno failed. Check data integrity. [Error #$errno]");
}
?>
