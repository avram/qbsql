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
 <script type="text/javascript" src="inc/jquery.js"></script>
 <script type="text/javascript" src="inc/wymeditor/wymeditor/jquery.wymeditor.pack.js"></script>
 <script type="text/javascript">
// Custom layout from (MIT & GPL dual licensed):
// 		Jean-Francois Hovinne (jf.hovinne a-t wymeditor dotorg)
// http://files.wymeditor.org/wymeditor/trunk/src/examples/12-custom-layout.html
jQuery(function() {

    jQuery('.wymeditor').wymeditor({
        
      //classes panel
      classesItems: [
        {'name': 'date', 'title': 'PARA: Date', 'expr': 'p'},
        {'name': 'hidden-note', 'title': 'PARA: Hidden note',
         'expr': 'p[@class!="important"]'},
        {'name': 'important', 'title': 'PARA: Important',
         'expr': 'p[@class!="hidden-note"]'},
        {'name': 'border', 'title': 'IMG: Border', 'expr': 'img'},
        {'name': 'special', 'title': 'LIST: Special', 'expr': 'ul, ol'}
      ],
      
      //we customize the XHTML structure of WYMeditor by overwriting 
      //the value of boxHtml. In this example, "CONTAINERS" and 
      //"CLASSES" have been moved from "wym_area_right" to "wym_area_top":
      boxHtml:   "<div class='wym_box'>"
              + "<div class='wym_area_top'>"
              + WYMeditor.TOOLS
              + WYMeditor.CONTAINERS
              + WYMeditor.CLASSES
              + "</div>"
              + "<div class='wym_area_left'></div>"
              + "<div class='wym_area_right'>"
              + "</div>"
              + "<div class='wym_area_main'>"
              + WYMeditor.HTML
              + WYMeditor.IFRAME
              + WYMeditor.STATUS
              + "</div>"
              + "<div class='wym_area_bottom'>"
              + "</div>"
              + "</div>",

      //postInit is a function called when WYMeditor instance is ready
      //wym is the WYMeditor instance
      postInit: function(wym) {

        //we make all sections in area_top render as dropdown menus:
        jQuery(wym._box)
            //first we have to select them:
            .find(".wym_area_top .wym_section")
            //then we remove the existing class which make some of them render as a panels:
            .removeClass("wym_panel")
            //then we add the class which will make them render as a dropdown menu:
            .addClass("wym_dropdown")
            //finally we add some css to make the dropdown menus look better:
            .css("width", "160px")
            .css("float", "left")
            .css("margin-right", "5px")
            .find("ul")
            .css("width", "140px");

        //add a ">" character to the title of the new dropdown menus (visual cue)
        jQuery(this._box).find(".wym_tools, .wym_classes ")
            .find(WYMeditor.H2)
            .append("<span>&nbsp;&gt;</span>");
        }
    });
});
 </script>
</head>
<body>
 <h4 class="headline"><strong><?=$tourney_name?></strong> | <a href="tournaments.php">Other Tournaments</a></h4>
 <h1><?php echo $title ?></h1>
