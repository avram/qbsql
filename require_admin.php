<?php
    if (!$auth || $tourney_lock) {
		header("HTTP/1.0 401 Unauthorized");
		
		$title = "Access Denied";
		
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
</head>
<body>
<?php
    }
	if(!$auth) {
		warning("You must log in to use this system.");
	}
	if($tourney_lock) {
		warning("Tournament locked.");
    }
?>
</body>
</html>
<?php
	die();
?>