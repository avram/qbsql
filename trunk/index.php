<?php
/* index.php
 *
 * Central command for qbsql 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
require "init.php";			// set up (connect to DB, etc)

$title = $tourney_name;
require "head.php";			// Generate header as appropriate
?>
    <p id="description"><?=$tourney_desc?></p>
<?php
 require "foot.php";			// finish off page
 ?>