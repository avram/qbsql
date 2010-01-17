// Error checking

function errorMsg(msg) {
	alert(msg);
}

function checkTUH(arr, teamTUH, teamName) {
	var tot = 0;
	for (var i = 0; i < arr.length; i++) {
		if (arr[i].value != "") {
			if (parseInt(arr[i].value) > teamTUH) {
				errorMsg ("Player " + (i + 1) + " on team " + teamName + " heard more tossups than were read.");
				return false;
			}
			tot += parseInt(arr[i].value);
		}
	}
	
	if (tot <= 0) {
		errorMsg ("No tossups heard for " + teamName + ".");
		return false;
	} else if (tot > teamTUH * 4) {
		errorMsg ("Too many tossups heard for " + teamName + ".");
		return false;
	}
	return true;
}

function checkTotalAnswered(t1pows, t1tus, t2pows, t2tus, tusHeard) {
	var tot = 0;
	for (var i = 0; i < t1pows.length; i++) {
		if (t1pows[i].value != "") {
			tot += parseInt(t1pows[i].value);
		}
	}
	for (var i = 0; i < t1tus.length; i++) {
		if (t1tus[i].value != "") {
			tot += parseInt(t1tus[i].value);
		}
	}
	for (var i = 0; i < t2pows.length; i++) {
		if (t2pows[i].value != "") {
			tot += parseInt(t2pows[i].value);
		}
	}
	for (var i = 0; i < t2tus.length; i++) {
		if (t2tus[i].value != "") {
			tot += parseInt(t2tus[i].value);
		}
	}
	
	if (tot > tusHeard) {
		errorMsg ("More tossups were answered than read.");
		return false;
	}
	return true;
}

function checkTotalNegs(t1negs, t2negs, tusHeard) {
	var tot = 0;
	for (var i = 0; i < t1negs.length; i++) {
		if (t1negs[i].value != "") {
			tot += parseInt(t1negs[i].value);
		}
	}
	for (var i = 0; i < t2negs.length; i++) {
		if (t2negs[i].value != "") {
			tot += parseInt(t2negs[i].value);
		}
	}

	if (tot > tusHeard) {
		errorMsg ("More negs than tossups read.");
		return false;
	}
	return true;
}

function checkScoreDivisor(score, team) {
	if (score % 5 != 0) {
		errorMsg("Score for team " + team + " is not a multiple of 5.");
		return false;
	}
	return true;
}

/*
Total score % 5 == 0

[ Total individual TUs heard > 0; <= 4 * TUs heard team
Individual TUs heard <= TUS heard team ]

[ Total TUs answered <= TUs heard team ]

[ Total negs <= TUs heard team ]

Bonus conversion <= 30, >= 0

Individual Buzzes <= individual TUs heard

Team buzzes <= Team TUs heard -- necessary?
*/

function checkInput(team1, team2) {
	var t1score = parseInt(document.getElementsByName("team1_score")[0].value);
	if (!checkScoreDivisor(t1score, team1)) return false;
	var t2score = parseInt(document.getElementsByName("team2_score")[0].value);
	if (!checkScoreDivisor(t2score, team2)) return false;		
	
	var tusHeard = parseInt(document.getElementsByName("total_tuh")[0].value);
	var t1tuh = document.getElementsByName("team1_tuh[]");
	var t2tuh = document.getElementsByName("team2_tuh[]");
	if (!checkTUH(t1tuh, tusHeard, team1)) return false;
	if (!checkTUH(t2tuh, tusHeard, team2)) return false;
	
	var t1pows = document.getElementsByName("team1_pow[]");
	var t1tus = document.getElementsByName("team1_tu[]");
	var t2pows = document.getElementsByName("team2_pow[]");
	var t2tus = document.getElementsByName("team2_tu[]");
	if (!checkTotalAnswered(t1pows, t1tus, t2pows, t2tus, tusHeard)) return false;
	
	var t1negs = document.getElementsByName("team1_neg[]");
	var t2negs = document.getElementsByName("team2_neg[]");
	if (!checkTotalNegs(t1negs, t2negs, tusHeard)) return false;
	
	return true;
}


// sorter
$(document).ready(function() 
    {
        $(".sort").tablesorter({widgets: ['zebra']});
        $("#overtime-content").hide();
        $("fieldset#overtime legend").click(function() {
        	$("#overtime-content").toggle("fast");
        })
    } 
);

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
