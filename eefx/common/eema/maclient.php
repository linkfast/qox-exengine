<?php
/**
 * QOX ExEngine Message Agent (formerly Debugger)
 *
 * Programa: Giancarlo Chiappe Aguilar
 * Fecha/Hora: 25/04/14 04:53 PM
 * (C) 2014 Todos los derechos reservados.
 */

/* register in eema */
$ma = new eema("maclient-frontend",'EE Message Agent client frontend.');

$apps = array();
if (count(eema::getApps()) > 0) {
	$apps = eema::getApps();
	$ma->t('Apps found and loaded.');
} else {
	$ma->w('Not apps registered in message agent.');
}
//$ma->e("error test");

?>
<style>
	.divider_line {
		line-height: 11px;
	}
	@-moz-document url-prefix() {
		.divider_line {
			line-height: 13px !important;
		}
	}
</style>
<div class="row">
	<div class="panel panel-default col-md-3" id="apps-panel">
		<div class="panel-body">
			<div class="row" style ="text-align: center; margin-bottom: 15px;"><span class="label
            label-default">Active Applications List</span>&nbsp;
            <span class="label label-default bsTooltip" onclick="eema_refreshApps()" style="cursor: pointer;"
				  title="Force active applications
            refresh" data-toggle="tooltip"
				  data-placement="right"><i id="aapps-spinner"  class="fa fa-refresh"></i></span>&nbsp;
            <span class="label label-default bsTooltip" style="cursor: pointer;" onclick="eema_clearApps()" title="Clear all applications" data-toggle="tooltip"
				  data-placement="right"><i class="fa
            fa-trash-o"></i></span>
			</div>
			<div class="row list-group" id="aapps-list">
				<?php
				$errorCount = 0;
				foreach ($apps as $eema_app) {
					$messages = array();
					$messages = eema::getMessages($eema_app['appKey']);
					foreach ($messages as $me) {
						if ($me['level'] == 'error' || $me['level'] == 'fatal')
							$errorCount++;
					}
					?>
					<a id="eema_<?php print $eema_app['appKey'] ; ?>" style="cursor: pointer;" onclick="return eema_loadApp('<?php print $eema_app['appKey'] ; ?>','<?php print $eema_app['appShortName'] ?>');" class="list-group-item">
						<span id="eema_badge_<?php print $eema_app['appKey'] ; ?>" class="badge <?php if ($errorCount > 0) { ?>alert-danger<?php } ?>"><?php print count(eema::getMessages($eema_app['appKey'])); ?></span>
						<h4 class="list-group-item-heading"><?php print $eema_app['appShortName'] ?></h4>
						<p class="list-group-item-text"><?php print $eema_app['appLongName'] ?></p>
					</a>
				<?php $errorCount=0; } ?>
			</div>
		</div>
	</div>

	<div class="panel panel-default col-md-9" id="message-panel">
		<div class="panel-body">
			<div class="row" style ="text-align: center; margin-bottom: 15px;">
				<span class="label label-default">Application Messages</span>&nbsp;
				<span class="label label-default" onclick="if (!eema_autoRefreshEnabled) { eema_getNewMessages(); }" style="cursor: pointer;"><a id="messages-tooltip" class="bsTooltip white-href" data-toggle="tooltip" data-placement="right" title="Ready. (click to force update)"><i id="messages-spinner" class="fa fa-refresh"></i></a></span>
				&nbsp;
            <span class="label label-default bsTooltip" style="cursor: pointer;" onclick="eema_clearAppMessages()" title="Clear selected app messages" data-toggle="tooltip"
				  data-placement="right"><i class="fa
            fa-trash-o"></i></span>
			</div>
			<nav class="navbar navbar-default" role="navigation">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" id="messages-apptitle">N/A</a>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<!-- <p class="navbar-text navbar-right">Autorefresh</p> -->
						<ul class="nav navbar-nav navbar-right">
							<li><a href="" onclick="return eema_toggleAutoRefresh();">Autoupdate <span class="badge" id="autorefresh-badge">OFF</span></a></li>
						</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>

			<div class="row" id="messages-in-panel">
				<div class="panel-body">
					<div id="messages-data">
						QOX CORPORATION
					</div>
					<div id="messages-help-select">
						<i style="font-size: 40px" class="fa fa-arrow-left"></i>
						<h4>Please select an application on the left panel to show messages.</h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var lapHeight = null;
var legacySize = null;
var module_init = function() {
	console.log('module_init','maclient:running in 10ms');
	setTimeout(function() {
		console.log('module_init','maclient:running now.');

		/* resizing */
		lapHeight = $("#apps-panel").css('height').replace('px','');
		var theSize = viewportHeight;
		theSize -= 70;

		//console.log('theSize',theSize);

		if (theSize < 500) {
			theSize = 500;
		}

		$("#message-panel").css('height',theSize.toString()+'px');
		$("#messages-in-panel").css('height',(theSize-160).toString()+'px');

		legacySize = theSize;

		/* info message */
		$("#messages-data").hide();

		/* bootstrap init js */
		$(".bsTooltip").tooltip();

		console.log('module_init','maclient:complete.');
	}, 10);
}

var eema_busy = function(statusMessage) {
	//$("#messages-tooltip").attr('title',statusMessage);
	$("#messages-tooltip").tooltip().attr('data-original-title', statusMessage).tooltip('fixTitle');
	$("#messages-tooltip").tooltip('show');
	$("#messages-spinner").addClass('fa-spin');
};

var eema_notBusy = function() {
	$("#messages-spinner").removeClass('fa-spin');
	$("#messages-tooltip")
		.attr('data-original-title','Ready. (click to force update)')
		.tooltip('fixTitle');
	$("#messages-tooltip").tooltip('show');
	setTimeout(function(){
		$("#messages-tooltip").tooltip('hide');
	},2000);
};

var eema_toggleAutoRefresh = function() {
	if (eema_autoRefreshEnabled) {
		eema_autoRefreshEnabled = false;
		$('#autorefresh-badge').html('OFF');
		clearInterval(eema_autoRefreshIval);
	} else {
		eema_autoRefreshEnabled = true;
		$('#autorefresh-badge').html('ON');
		eema_autoRefreshIval = setInterval(eema_getNewMessages,5000);
	}
	return false;
};

var eema_selApp = null;
var eema_selAppTitle = null;
var eema_autoRefreshIval = null;
var eema_appOldData = null;
var eema_autoRefreshEnabled = false;
var eema_getNewMessages = function() {
	if (eema_selApp != null) {
		eema_busy('Getting new messages...');
		$.ajax({
			type: 'POST',
			url: eema_server,
			data: { 'cmd' : 'getMessages', 'appKey' : eema_selApp },
			dataType: 'json'
		}).fail(function() {
			alert("error! - cannot get new messages");
			eema_notBusy();
		}).
			done(function(data) {
				var mess = data;
				if (mess !== 'No Messages for ' + eema_selApp + '.') {
					/* hashing */
					var oldMessHash = [];
					if (eema_appOldData != null) {
						eema_appOldData.forEach(function(msg) {
							if (typeof msg !== 'undefined') {
								var foreColor = '#FFF';
								var levelName = 'TRACE';
								var bgColor = '255,255,255';
								switch (msg.level) {
									case 'trace':
										foreColor = '#FFF';
										bgColor = '255,255,255';
										levelName = 'TRACE';
										break;
									case 'debug':
										foreColor = '#B2B2B2';
										bgColor = '178, 178, 178';
										levelName = 'DEBUG';
										break;
									case 'info':
										foreColor = '#00CC66';
										bgColor = '0, 204, 102';
										levelName = 'INFO';
										break;
									case 'warning':
										foreColor = '#FF9966';
										bgColor = '255, 153, 102';
										levelName = 'WARNING';
										break;
									case 'error':
										foreColor = '#CC3300';
										bgColor = '204, 51, 0, 1';
										levelName = 'ERROR';
										eemaApp_errorCount++;
										break;
									case 'fatal':
										foreColor = '#FF0000';
										bgColor = '255, 0, 0';
										levelName = 'FATAL';
										eemaApp_errorCount++;
										break;
								}
								var addData = '';
								if (typeof msg.additionalData !== 'undefined') {
									var AData_Dump = msg.additionalData.replace(/'/g, "");
									AData_Dump = AData_Dump.replace(/"/g, "");
									AData_Dump = AData_Dump.replace(/<br >/g, "");
									AData_Dump = AData_Dump.replace(/<br \/>/g, "");
									AData_Dump = AData_Dump.replace(/<\/a>/g, "");
									addData = '<small>(<a style="cursor: pointer;" onclick="alert(\''+AData_Dump+'\');">additional data present</a>)</small>';
								}

								var appendData =
									'<div>' +
										'<div class="divider_line" style="text-align: center; background-color: rgba('+bgColor+',0.15); padding: 0; font-size: 12px;height: 13px;">' +
										'<b style="color: '+foreColor+'; ">' + levelName + '</b>&nbsp;&nbsp;' + msg.date +
										'</div>' +
										'<div>' +
										'<span style="color: '+foreColor+';">' +
										msg.message +
										"</span>" +
										addData +
										"</div>" +
										"</div>";

								oldMessHash.push(appendData);
							}
						});
					}
					var newMessHash = [];
					mess.forEach(function(msg) {
						if (typeof msg !== 'undefined') {
							var foreColor = '#FFF';
							var levelName = 'TRACE';
							var bgColor = '255,255,255';
							switch (msg.level) {
								case 'trace':
									foreColor = '#FFF';
									bgColor = '255,255,255';
									levelName = 'TRACE';
									break;
								case 'debug':
									foreColor = '#B2B2B2';
									bgColor = '178, 178, 178';
									levelName = 'DEBUG';
									break;
								case 'info':
									foreColor = '#00CC66';
									bgColor = '0, 204, 102';
									levelName = 'INFO';
									break;
								case 'warning':
									foreColor = '#FF9966';
									bgColor = '255, 153, 102';
									levelName = 'WARNING';
									break;
								case 'error':
									foreColor = '#CC3300';
									bgColor = '204, 51, 0, 1';
									levelName = 'ERROR';
									eemaApp_errorCount++;
									break;
								case 'fatal':
									foreColor = '#FF0000';
									bgColor = '255, 0, 0';
									levelName = 'FATAL';
									eemaApp_errorCount++;
									break;
							}
							var addData = '';
							if (typeof msg.additionalData !== 'undefined') {
								var AData_Dump = msg.additionalData.replace(/'/g, "");
								AData_Dump = AData_Dump.replace(/"/g, "");
								AData_Dump = AData_Dump.replace(/<br >/g, "");
								AData_Dump = AData_Dump.replace(/<br \/>/g, "");
								AData_Dump = AData_Dump.replace(/<\/a>/g, "");
								addData = '<small>(<a style="cursor: pointer;" onclick="alert(\''+AData_Dump+'\');">additional data present</a>)</small>';
							}

							var appendData =
								'<div>' +
									'<div class="divider_line" style="text-align: center; background-color: rgba('+bgColor+',0.15); padding: 0; font-size: 12px;height: 13px;">' +
									'<b style="color: '+foreColor+'; ">' + levelName + '</b>&nbsp;&nbsp;' + msg.date +
									'</div>' +
									'<div>' +
									'<span style="color: '+foreColor+';">' +
									msg.message +
									"</span>" +
									addData +
									"</div>" +
									"</div>";

							newMessHash.push(appendData);
						}
					});

					$("#eema_badge_" + eema_selApp).html(newMessHash.length.toString());

					console.log('oldMessHash',oldMessHash);
					console.log('newMessHash',newMessHash);

					/* compare */
					var messx = _.difference(newMessHash,oldMessHash);

					console.log('_difference',messx);

					eema_appOldData = mess;

					//console.log('messx',messx.length);

					if (messx.length > 0) {
						for (var i=0;i<messx.length;i++) {
							(function(i,msg) {
								if (i == (messx.length-1)) {
									setTimeout(function() {
										if (eemaApp_errorCount>0) {
											$("#eema_badge_" + eema_selApp).addClass('alert-danger');
										} else {
											$("#eema_badge_" + eema_selApp).removeClass('alert-danger');
										}
										eemaApp_errorCount=0;
										eema_notBusy();
									},20);
								}
								setTimeout(function() {
									$("#messages-data").prepend(msg);
								},10);
							})(i,messx[i]);
						}
					}else {
						eema_notBusy();
					}
				} else {
					eema_notBusy();
				}
			}).always(function() {
				//eema_notBusy();
			});
	}
	return false;
};

var eema_clearAppMessages = function() {
	if (eema_selApp != null) {
		$.ajax({
			type: 'POST',
			url: eema_server,
			data: { 'cmd' : 'cleanMessages' , 'appKey' : eema_selApp },
			dataType: 'json'
		}).fail(function() {
			alert("error!");
			eema_notBusy();
		}).done(function(data) {
			eema_notBusy();
			eema_appOldData=null;
			eema_loadApp(eema_selApp,eema_selAppTitle);
		});
	}
};

var eemaApp_errorCount = 0 ;
var eema_loadApp = function(eemaApp,appTitle) {
	$('#messages-data').css('height', (legacySize-180).toString() + 'px');
	if ($('#messages-data').css('display')!=='none') {
		$('#messages-data').fadeOut(100);
	}
	$('#messages-apptitle').html('N/A');
	clearInterval(eema_autoRefreshIval);
	eema_selApp = null;
	eema_busy('Getting messages...');
	$.ajax({
		type: 'POST',
		url: eema_server,
		data: { 'cmd' : 'getMessages', 'appKey' : eemaApp },
		dataType: 'json'
	}).fail(function() {
		alert("error!");
		eema_notBusy();
	}).
		done(function(data) {
			$('#messages-data').html('');
			var mess = data;
			if (mess.length > 0) {
				eema_appOldData = mess;
				$("#eema_badge_" + eemaApp).html(mess.length.toString());
				//console.log('mess.length',mess.length);
				for (var i=0;i<mess.length;i++) {
					(function(i,msg) {
						if (i == (mess.length-1)) {
							setTimeout(function() {
								$('#messages-apptitle').html(appTitle);
								eema_selApp = eemaApp;
								eema_selAppTitle = appTitle;
								$('#messages-help-select').fadeOut(100,function() { $('#messages-data').fadeIn(100); });

								if (eemaApp_errorCount>0) {
									$("#eema_badge_" + eemaApp).addClass('alert-danger');
								} else {
									$("#eema_badge_" + eemaApp).removeClass('alert-danger');
								}
								eemaApp_errorCount=0;

								if (eema_autoRefreshEnabled)
									eema_autoRefreshIval = setInterval(eema_getNewMessages,5000);
								eema_notBusy();
							},20);
						}
						setTimeout(function() {
							var foreColor = '#FFF';
							var levelName = 'TRACE';
							var bgColor = '255,255,255';
							switch (msg.level) {
								case 'trace':
									foreColor = '#FFF';
									bgColor = '255,255,255';
									levelName = 'TRACE';
									break;
								case 'debug':
									foreColor = '#B2B2B2';
									bgColor = '178, 178, 178';
									levelName = 'DEBUG';
									break;
								case 'info':
									foreColor = '#00CC66';
									bgColor = '0, 204, 102';
									levelName = 'INFO';
									break;
								case 'warning':
									foreColor = '#FF9966';
									bgColor = '255, 153, 102';
									levelName = 'WARNING';
									break;
								case 'error':
									foreColor = '#CC3300';
									bgColor = '204, 51, 0, 1';
									levelName = 'ERROR';
									eemaApp_errorCount++;
									break;
								case 'fatal':
									foreColor = '#FF0000';
									bgColor = '255, 0, 0';
									levelName = 'FATAL';
									eemaApp_errorCount++;
									break;
							}
							var addData = '';
							if (typeof msg.additionalData !== 'undefined') {
								var AData_Dump = msg.additionalData.replace(/'/g, "");
								AData_Dump = AData_Dump.replace(/"/g, "");
								AData_Dump = AData_Dump.replace(/<br >/g, "");
								AData_Dump = AData_Dump.replace(/<br \/>/g, "");
								AData_Dump = AData_Dump.replace(/<\/a>/g, "");
								addData = '<small>(<a style="cursor: pointer;" onclick="alert(\''+AData_Dump+'\');">additional data present</a>)</small>';
							}

							var appendData =
								'<div>' +
									'<div class="divider_line" style="text-align: center; background-color: rgba('+bgColor+',0.15); padding: 0; font-size: 12px;height: 13px;">' +
										'<b style="color: '+foreColor+'; ">' + levelName + '</b>&nbsp;&nbsp;' + msg.date +
									'</div>' +
									'<div>' +
										'<span style="color: '+foreColor+';">' +
											msg.message +
										"</span>" +
										addData +
									"</div>" +
								"</div>";

							$("#messages-data").prepend(appendData);

						},10);
					})(i,mess[i]);
				}
			} else {
				$('#messages-apptitle').html(appTitle);
				eema_selApp = eemaApp;
				$('#messages-help-select').fadeOut(100,function() { $('#messages-data').fadeIn(100); });
				if (eema_autoRefreshEnabled)
					eema_autoRefreshIval = setInterval(eema_getNewMessages,5000);
				eema_notBusy();
			}
		}).always(function() {

		});
	return false;
};

var eema_clearApps = function() {
	eema_busy('Clearing apps & messages...');
	$('#messages-apptitle').html('N/A');
	if ($('#messages-data').css('display')!=='none') {
		$('#messages-data').fadeOut(100,function() {
			$('#messages-data').html('');
			$('#messages-help-select').fadeIn(100); });
	}
	$.ajax({
		type: 'POST',
		url: eema_server,
		data: { 'cmd' : 'clearApps' },
		dataType: 'json'
	}).fail(function() {
		alert("error!");
		eema_notBusy();
	}).done(function(data) {
		eema_notBusy();
		eema_refreshApps();
	});
};

var eema_refreshBadges = function() {
	//eema_busy('Refreshing message count...');
	$('#aapps-spinner').addClass('fa-spin');
	$.ajax({
		type: 'POST',
		url: eema_server,
		data: { 'cmd' : 'getApps' },
		dataType: 'json'
	}).fail(function() {
		alert("error! - Cannot refresh badges.");
		//eema_notBusy();
	}).done(function(data) {
		var appsR = data;
		if (appsR.length > 0) {
		for (var i=0;i<appsR.length;i++) {
			(function(i,appName) {
				if (i == (appsR.length-1)) {
					setTimeout(function() {
						$('#aapps-spinner').removeClass('fa-spin');
						//eema_notBusy();
					},20);
				}
				setTimeout(function() {
					if (eema_autoRefreshEnabled && (appName.appKey !== eema_selApp)) {
						if (appName.errorCount > 0) {
							$("#eema_badge_" + appName.appKey).addClass('alert-danger');
						} else {
							$("#eema_badge_" + appName.appKey).removeClass('alert-danger');
						}
						$("#eema_badge_" + appName.appKey).html(appName.msgCount.toString());
					} else {
						if (!eema_autoRefreshEnabled) {
							if (appName.errorCount > 0) {
								$("#eema_badge_" + appName.appKey).addClass('alert-danger');
							} else {
								$("#eema_badge_" + appName.appKey).removeClass('alert-danger');
							}
							$("#eema_badge_" + appName.appKey).html(appName.msgCount.toString());
						}
					}
				},10);
			})(i,appsR[i]);
		}
		} else {
			//eema_notBusy();
			$('#aapps-spinner').removeClass('fa-spin');
			eema_refreshApps();
		}
	});
}

var eema_badgesInterval = setInterval('eema_refreshBadges();', 5000);

var eema_refreshApps = function() {
	//eema_busy('Getting active applications list...');
	$('#aapps-spinner').addClass('fa-spin');
	$('#messages-apptitle').html('N/A');
	eema_selApp = null;
	eema_selAppTitle = null;
	if ($('#messages-data').css('display')!=='none') {
		$('#messages-data').fadeOut(100,function() {
			$('#messages-data').html('');
			$('#messages-help-select').fadeIn(100); });
	}
	$('#aapps-list').fadeOut(100);
	$('#aapps-list').html('');
	$.ajax({
		type: 'POST',
		url: eema_server,
		data: { 'cmd' : 'getApps' },
		dataType: 'json'
	}).fail(function() {
		alert("error!");
		//eema_notBusy();
	}).done(function(data) {
		var appsR = data;
		if (appsR.length > 0) {
			for (var i=0;i<appsR.length;i++) {
				(function(i,appName) {
					if (i == (appsR.length-1)) {
						setTimeout(function() {
							$('#aapps-list').fadeIn(100);
							$('#aapps-spinner').removeClass('fa-spin');
							//eema_notBusy();
						},20);
					}
					setTimeout(function() {
						var eC = '';
						if (appName.errorCount > 0) {
							eC = ' alert-danger';
						}
						var myAData =
						'<a id="eema_'+appName.appKey+'" style="cursor: pointer;" onclick="return eema_loadApp(\''+appName.appKey+'\',\''+appName.appShortName+'\');" class="list-group-item">\
						<span id="eema_badge_'+appName.appKey+'" class="badge'+eC+'">'+appName.msgCount+'</span>\
						<h4 class="list-group-item-heading">'+appName.appShortName+'</h4>\
						<p class="list-group-item-text">'+appName.appLongName+'</p>\
						</a>';
						$('#aapps-list').append(myAData);
					},10);
				})(i,appsR[i]);
			}
		} else {
			$('#aapps-spinner').removeClass('fa-spin');
			//eema_notBusy();
		}
		//eema_notBusy();
	});
}
</script>