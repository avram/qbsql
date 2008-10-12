<?php
// ============================================================================
// help.php: This page is used to display the various help texts for UCDB.
//            It expects to be called from the popupHelp function in init.php, which
//            is in turn called with the help topic parameter, topic.  It expects to be
//            a popup, standalone window, hence there are no includes.
//            
//            The help array contains the title and the text, based on keys.  It is 
//            declared first, for easy addition, and then we error check.
//            Next, if there were no errors, we get the title, and display it the help.
//
//            If no topic is specified, then we show a list of all topics.
//
// Parameters:
//   $topic - The help topic to display
//
// Sets:
//   None
//
//  To call from another script, insert the function popupHelp('help_topic')
//
//
// Author: James Michael-Hill
// Created: June 19, 2003
// Migrated and reworked for qbsql, February 2004, Avram Lyon

  //Note, this is a hashed array of arrays, arg 0 is the title, arg 1 is the text.
  $help['manage_roster']=array("Managing Rosters",
    "You should work it out. It can't be <em>that</em> hard.");


  //if we have the info for the help topic, set the title 
  if (in_array($topic,array_keys($help))){ 
    $title=$help[$topic][0];
  }
  else{ //otherwise, we need a generic title
    $title="Help";
  }
  


?>
<html>
<head>
<?php echo "<title>$title</title>"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {  background-color: #FFF; font-family: Verdana, Arial, Helvetica, sans-serif}
.box a:link {  text-decoration: none}
.box a:visited {  text-decoration: none}
.box a:hover {  text-decoration: underline; background-color: #F93}
a:link {  text-decoration: none}
a:visited {  text-decoration: none}
a:hover {  text-decoration: underline} 
h1 {  color: #000; background-color: #F93; text-align: center; border-style: solid; border-width: thin;}
h2 {  color: #000; background-color: #F93; border-color: #F93 #FC6 #FC6 #F93; border-style: solid; border-width: thin;}
-->
</style>
</head>

<body bgcolor="#FFFFFF">
<table width="100%" cellspacing="10" >
  <tr valign="top"> 
    <td width="90%">
      <?php echo "<h1>$title</h1>"; ?>
      <table width="100%" cellspacing="10">
        <tr valign="top">
          <td width="90%">
      <?php
      //if they didn't specify a topic, or picked a bad one, show the list of topics
      if ($title=='Help'){
        foreach (array_keys($help) as $topic){
	  echo "<a href=help.php?topic=$topic>".$help[$topic][0]."</a><br>\n";
	}
	echo "<br>\n";
	echo "<a href='#' onClick='javascript:window.close();return false;'>close</a>\n";
      }
      //otherwise, display the chosen topic
      else {
        echo $help[$topic][1]."<br>\n";
	echo "<a href='#' onClick='javascript:window.close();return false;'>close</a>\n";
      }
   ?>
       </td>
     </tr>
   </table>
   
   </td>
  </td>
</table>
</body></html>

