<?php
/* 
 * head.php
 *
 * Takes:
 * $title -- the page title
 *
 * Author: Avram Lyon
 * Created: 21 February 2004
 */
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
    echo "\n"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 <meta http-equiv="Content-type" content='text/html; charset="UTF-8"' />
 <title><?php echo $title ?></title>
 <link rel="stylesheet" type="text/css" href="qbsql.css" />
 <link rel="stylesheet" type="text/css" href="local.css" />
</head>
<body>
 <h4 class="headline"><strong><?=$tourney_name?></strong> | <a href="tournaments.php">Other Tournaments</a></h4>
 <h1><?php echo $title ?></h1>
