<?php
/* tournament_modify.php
 * 
 * Handle the creation and maintenance of tournaments.
 *
 * Author: Avram Lyon 
 */
 require "functions.php";
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');
 $title="Manage tournaments";
 require "head.php";			// Generate header as appropriate

 if (isset($_GET["action"]) && $_GET["action"] == "add") {
     $master = ($_POST["master_un"] == $master_username) && 
         ($_POST["master_pw"] == $master_password);
     $confirm = ($_POST["pw"] == $_POST["pw2"]);
     $prefix_ok = !preg_match("/[^a-zA-Z0-9_]/", $_POST["prefix"]);
     
     if($prefix_ok) {
         $res = query("SELECT * FROM tournaments WHERE prefix = '$_POST[prefix]'");
         if(fetch_row($res))
             $prefix_ok = false;
     }
     
     if ($master && $confirm && $prefix_ok && is_numeric($_POST["len"])) {
         // We've checked all the input.
         query("INSERT INTO tournaments SET name = '$_POST[name]',
                    prefix = '$_POST[prefix]', username = '$_POST[un]',
                    password = '$_POST[pw]', game_length = '$_post[len]',
                    description = '$_POST[desc]'") or die(mysql_error());
         $prefix = $_POST["prefix"];
         $query = <<<CREATE
CREATE TABLE {$prefix}_players (
  last_name varchar(40) default NULL,
  first_name varchar(40) default NULL,
  short_name varchar(40) default NULL,
  team int(20) NOT NULL default '0',
  id int(20) NOT NULL auto_increment,
  KEY id (id)
) ENGINE=MyISAM;
CREATE;
         query($query) or die(mysql_error());
         
         $query = <<<CREATE
CREATE TABLE {$prefix}_rounds (
  name varchar(40) default NULL,
  team1 int(20) NOT NULL default '0',
  team2 int(20) NOT NULL default '0',
  score1 int(20) default NULL,
  score2 int(20) default NULL,
  tu_heard int(20) default NULL,
  id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (game_id),
  KEY id (id)
) ENGINE=MyISAM;
CREATE;
         query($query) or die(mysql_error());
         
         $query = <<<CREATE
CREATE TABLE {$prefix}_rounds_players (
  player_id int(20) NOT NULL default '0',
  team_id int(20) NOT NULL default '0',
  powers int(20) default NULL,
  tossups int(20) default NULL,
  negs int(20) default NULL,
  tu_heard int(20) default NULL,
  round_id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL default '0',
  KEY round_id (round_id),
  KEY game_id (game_id)
) ENGINE=MyISAM;
CREATE;
         query($query) or die(mysql_error());

         $query = <<<CREATE
CREATE TABLE {$prefix}_teams (
  full_name varchar(30) default NULL,
  short_name varchar(30) default NULL,
  id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
CREATE;
         query($query) or die(mysql_error());
         
            // redirect to tournament list
            print <<<RED
<html><head>
  <meta http-equiv="Refresh" content="1; url=tournaments.php">
</head><body>
  <p>Tournament probably created. Returning to <a href="tournaments.php">Tournament List</a></p>
</body></html>
RED;
     }
     else
         print "Double-check and try again.";
 }


?>
     <form action="?action=add" method="POST">
     <p class="form">
      Name: <input type="text" size="30" name="name" /><br />
      Prefix: <input type="text" size="30" name="prefix" /> (letters and numbers only)<br />
      Description: <textarea rows="4" cols="30" name="desc"></textarea><br />
      Default Game Length: <input type="text" size="4" name="len" value="20" /> (tossups per round)<br />
      Tournament username: <input type="text" size="30" name="un" /><br />
      Tournament password: <input type="password" size="30" name="pw" /><br />
      Confirm password: <input type="password" size="30" name="pw2" /><br />
      Adding tournaments requires the master username and password: <br />
      Master username: <input type="text" size="30" name="master_un" /><br />
      Master password: <input type="password" size="30" name="master_pw" /><br />
      <input type="submit" value="Add tournament" />
     </p>
<?php
?>
</body>
</html>
