<?php
/* tournament_modify.php
 * 
 * Handle the creation and maintenance of tournaments.
 *
 * Author: Avram Lyon 
 */
 require "functions.php";
    $link = connect($mysql_host,$mysql_username,$mysql_pass,$mysql_db) or die('Failed to connect to DB server.');
 $title="New tournament";
 $tourney_name = "New Tournament";
 $js_includes = True;
 require "head.php";			// Generate header as appropriate

 if (isset($_GET["action"]) && $_GET["action"] == "add") {
     $master = ($_POST["master_un"] == $master_username) && 
         ($_POST["master_pw"] == $master_password);
     $confirm = ($_POST["pw"] == $_POST["pw2"]);
     $prefix_ok = !preg_match("/[^a-zA-Z0-9_]/", $_POST["prefix"]);
     $prefix_ok = $prefix_ok && !strstr("PFX", $_POST["prefix"]); // prevent migration scripts from breaking
     
     if($prefix_ok) {
         $res = query("SELECT * FROM tournaments WHERE prefix = '$_POST[prefix]'");
         if(fetch_row($res))
             $prefix_ok = false;
     }
     
     if ($master && $confirm && $prefix_ok && is_numeric($_POST["len"]) && !$name_bad) {
         // We've checked all the input.
         $query = "INSERT INTO tournaments SET name = '$_POST[name]',
                    prefix = '$_POST[prefix]', username = '$_POST[un]',
                    password = '$_POST[pw]', game_length = '$_POST[len]',
                    description = '$_POST[desc]'";
         query($query) or
         	dbwarning("A database error occurred while adding the tournament.",
         			  $query);
         $prefix = $_POST["prefix"];

         $queries = table_create_queries($prefix);
         foreach ($queries as $query) {
             query($query) or
             	dbwarning("A database error occurred while adding the tournament.",
             			  $query);
         }
         
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
         warning("An input validation error occurred. Double-check your input and try again, perhaps with a different prefix.");
 }


?>
     <form id="newtourney" action="?action=add" method="POST">
     <fieldset id="basic">
     <legend>Tournament Settings</legend>
     <ol>
      <li><label for="name">Name: </label>
      <input type="text" size="30" name="name" tabindex="1" id="name" /></li>
      <li><label for="prefix">Prefix: </label>
      <input type="text" size="30" name="prefix" tabindex="2" id="name" />
      <p>letters and numbers only</p></li>
      <li><label for="desc">Description: </label>
      <textarea rows="6" cols="20" name="desc" tabindex="3" id="desc" class="wymeditor"></textarea></li>
      <li><label for="len">Default game length: </label>
      <input type="text" size="4" name="len" value="20" tabindex="4" id="len" />
      <p>tossups per round</p></li>
      <li><label for="un">Tournament username: </label>
      <input type="text" size="30" name="un" tabindex="5" id="un" /></li>
      <li><label for="pw">Tournament password: </label>
      <input type="password" size="30" name="pw" tabindex="6" id="pw" /></li>
      <li><label for="pw2">Confirm password: </label>
      <input type="password" size="30" name="pw2" tabindex="7" id="pw2" /></li>
      </ol>
      </fieldset>
      <fieldset id="authentication">
      <legend>Authentication</legend>
      <ol>
      <li><label for="master_un">Master username: </label>
      <input type="text" size="30" name="master_un" id="master_un" /></li>
      <li><label for="master_pw">Master password: </label>
      <input type="password" size="30" name="master_pw" id="master_pw" /></li>
      </ol>
      </fieldset>
      <p><input type="submit" class="wymupdate" value="Add tournament" /></p>
     </form>
<?php
?>
</body>
</html>
