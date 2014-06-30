// ExEngine / Debugger Javascript 1.0.2
/*
	This file is part of ExEngine7.

    ExEngine7 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ExEngine7 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ExEngine7.  If not, see <http://www.gnu.org/licenses/>.
*/

var lG = loc + "debug_loading.gif";
var iA = loc + "debug/icon.accept.png";
var selectedApp;
var autoRefreshTime = 5000;

$(function() {			
			refreshApps();
});

function busy(mss) {
	$("#status-bar").html('<img src="'+lG+'" border="0" align="absmiddle" /> '+mss);	
}

function ready() {
	$("#status-bar").html('<img src="'+iA+'" border="0" align="absmiddle" /> Ready.');
}

function cleanDebug() {
	busy("Working...");
	$.post(self+"?aserver=true",
		   {cmd:"cleanAll"},
		   function(data) {
			   if (data.result == true) {
					refreshApps();
					$("#messages-app").html('<p align="center">Please Select an application to see messages.</p>');
					selectedApp = null;
			   } else {
				   alert("Something gone wrong.\nError Code: 001");
			   }
		   },"json");
}

function selApp(app) {
	selectedApp = app;
	getMessages();
}

function getMessages() {
	if (selectedApp!=null) {
		busy("Getting Messages for "+selectedApp+"...");
		$.post(self+"?aserver=true",
			   {cmd:"getMessages",gApp:selectedApp},
			   function(data){
				   mess = data.result;
				   $("#messages-app").html("");
					for (i=0;i<mess.length;i++) {
						pre = $("#messages-app").html();
						$("#messages-app").html(pre  + mess[i].date + "&nbsp;&nbsp;&nbsp;&nbsp;<b>" + mess[i].msg + "</b><br/>");
						
					}
					ready();
			   },"json");
	} else {
		ready();
		alert("No application selected.");
	}
}

function cleanSelApp() {
	if (selectedApp!=null) {
		busy("Cleaning Messages for "+selectedApp+"...");
		$.post(self+"?aserver=true",
			   {cmd:"cleanMessages",gApp:selectedApp},
			   function(data){
				   if (data.result == true) {
					ready();
					getMessages();				
			   } else {
				   alert("Something gone wrong.\nError Code: 002");
			   }
			   },"json");
	} else {
		ready();
		alert("No application selected.");
	}
}

function refreshApps() {
	busy("Working...");
	$("#available-apps").html('<p align="center"><img src="debug_loading.gif" border="0" align="absmiddle" /></p>');
	$.post(self+"?aserver=true",
		   { cmd : "getApps" },
		   function(data) {
			   if (data.result == "Empty.") {
					$("#available-apps").html("No apps are connected now with Debugger.");
			   } else {
					apps = data.result.split("(*)");
					$("#available-apps").html("");
					for (i=0;i<apps.length;i++) {
						pre = $("#available-apps").html();
						$("#available-apps").html(pre + '<a href="javascript:selApp'+"('"+apps[i]+"')"+';">' + apps[i]+"</a><br/>");
					}
			   }
			   ready();
		   },"json");

}


//http://www.vijayjoshi.org/2009/04/02/changing-font-size-on-a-page-with-javascript-for-better-user-experience/
function changeFontSize(element,step)
		{
			step = parseInt(step,10);
			var el = document.getElementById(element);
			var curFont = parseInt(el.style.fontSize,10);
			el.style.fontSize = (curFont+step) + 'px';
			return;
		}
