<?php
/**
 * QOX ExEngine Message Agent (formerly Debugger)
 *
 * Programa: Giancarlo Chiappe Aguilar
 * Fecha/Hora: 25/04/14 04:53 PM
 * (C) 2014 Todos los derechos reservados.
 */

if (isset($_SESSION["exengine-debugger-apps"])) {
    $apps = $_SESSION["exengine-debugger-apps"];
    //$apps = implode("(*)",$apps);
    //$res = array("result"=>$apps);
    $ee->debugThis("Debug App Server","All debugging applications names served.");
} else {
    //$res = array("result"=>"Empty.");
    $ee->debugThis("Debug App Server","Debugger Applications list empty.");
}

?>
<div class="row">
    <div class="panel panel-default col-md-3" id="legacy-apps-panel">
        <div class="panel-body">
            <div class="row" style ="text-align: center; margin-bottom: 15px;"><span class="label
            label-default">Active Applications List</span>&nbsp;
            <span class="label label-default bsTooltip" onclick="eemaLegacy_refreshApps()" style="cursor: pointer;"
                  title="Force active applications
            refresh" data-toggle="tooltip"
                  data-placement="right"><i id="legacy-aapps-spinner"  class="fa fa-refresh"></i></span>&nbsp;
            <span class="label label-default bsTooltip" style="cursor: pointer;" onclick="eemaLegacy_clearApps()" title="Clear all applications" data-toggle="tooltip"
                                         data-placement="right"><i class="fa
            fa-trash-o"></i></span>
            </div>
            <div class="row list-group" id="legacy-aapps-list">
                <?php
                    if (!is_array($apps))
                        $apps = array();
                    foreach ($apps as $eema_app) {
                ?>
                <a style="cursor: pointer;" onclick="return eemaLegacy_loadApp('<?php print $eema_app ; ?>');"
                   class="list-group-item"><?php print $eema_app ?></a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="panel panel-default col-md-9" id="legacy-message-panel">
        <div class="panel-body">
            <div class="row" style ="text-align: center; margin-bottom: 15px;">
                <span class="label label-default">Application Messages</span>&nbsp;
                <span class="label label-default" onclick="if (!eemaLegacy_autoRefreshEnabled) { eemaLegacy_getNewMessages(); }" style="cursor: pointer;"><a id="legacy-messages-tooltip" class="bsTooltip white-href" data-toggle="tooltip" data-placement="right" title="Ready. (click to force update)"><i id="legacy-messages-spinner" class="fa fa-refresh"></i></a></span>
                &nbsp;
            <span class="label label-default bsTooltip" style="cursor: pointer;" onclick="eemaLegacy_clearAppMessages()" title="Clear selected app messages" data-toggle="tooltip"
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
                        <a class="navbar-brand" id="legacy-messages-apptitle">N/A</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <!-- <p class="navbar-text navbar-right">Autorefresh</p> -->
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="" onclick="return eemaLegacy_toggleAutoRefresh();">Autoupdate <span class="badge" id="legacy-autorefresh-badge">OFF</span></a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>

            <div class="row" id="legacy-messages-in-panel">
                <div class="panel-body">
                    <div id="legacy-messages-data">
                        QOX CORPORATION
                    </div>
                    <div id="legacy-messages-help-select">
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
        console.log('module_init','legacy:running in 10ms');
        setTimeout(function() {
            console.log('module_init','legacy:running now.');

            /* resizing */
            lapHeight = $("#legacy-apps-panel").css('height').replace('px','');
            var theSize = viewportHeight;
            theSize -= 70;

            //console.log('theSize',theSize);

            if (theSize < 500) {
                theSize = 500;
            }

            $("#legacy-message-panel").css('height',theSize.toString()+'px');
            $("#legacy-messages-in-panel").css('height',(theSize-160).toString()+'px');

            legacySize = theSize;

            /* info message */
            $("#legacy-messages-data").hide();

            /* bootstrap init js */
            $(".bsTooltip").tooltip();

            console.log('module_init','legacy:complete.');
        }, 10);
    }

    var eemaLegacy_busy = function(statusMessage) {
        //$("#legacy-messages-tooltip").attr('title',statusMessage);
        $("#legacy-messages-tooltip").tooltip().attr('data-original-title', statusMessage).tooltip('fixTitle');
        $("#legacy-messages-tooltip").tooltip('show');
        $("#legacy-messages-spinner").addClass('fa-spin');
    };

    var eemaLegacy_notBusy = function() {
        $("#legacy-messages-spinner").removeClass('fa-spin');
        $("#legacy-messages-tooltip")
            .attr('data-original-title','Ready. (click to force update)')
            .tooltip('fixTitle');
        $("#legacy-messages-tooltip").tooltip('show');
        setTimeout(function(){
            $("#legacy-messages-tooltip").tooltip('hide');
        },2000);
    };

    var eemaLegacy_toggleAutoRefresh = function() {
        if (eemaLegacy_autoRefreshEnabled) {
            eemaLegacy_autoRefreshEnabled = false;
            $('#legacy-autorefresh-badge').html('OFF');
            clearInterval(eemaLegacy_autoRefreshIval);
        } else {
            eemaLegacy_autoRefreshEnabled = true;
            $('#legacy-autorefresh-badge').html('ON');
            eemaLegacy_autoRefreshIval = setInterval(eemaLegacy_getNewMessages,5000);
        }
        return false;
    };

    var eemaLegacy_selApp = null;
    var eemaLegacy_autoRefreshIval = null;
    var eemaLegacy_appOldData = null;
    var eemaLegacy_autoRefreshEnabled = false;
    var eemaLegacy_getNewMessages = function() {
        if (eemaLegacy_selApp != null) {
            eemaLegacy_busy('Getting new messages...');
            $.ajax({
                type: 'POST',
                url: eema_server,
                data: { 'cmd' : 'legacyGetMessages', 'gApp' : eemaLegacy_selApp },
                dataType: 'json'
            }).fail(function() {
                alert("error!");
                eemaLegacy_notBusy();
            }).
                done(function(data) {
                    var mess = data.result;
                    if (mess !== 'No Messages for ' + eemaLegacy_selApp + '.') {
                        /* hashing */
                        var oldMessHash = [];
                        eemaLegacy_appOldData.forEach(function(obj) {
                            if (typeof obj !== 'undefined')
                                oldMessHash.push(obj.date+ "&nbsp;&nbsp;&nbsp;" +obj.msg);
                        });
                        var newMessHash = [];
                        mess.forEach(function(obj) {
                            if (typeof obj !== 'undefined')
                                newMessHash.push(obj.date+ "&nbsp;&nbsp;&nbsp;" +obj.msg);
                        });

                        /* compare */
                        var messx = _.difference(newMessHash,oldMessHash);

                        eemaLegacy_appOldData = mess;

                        //console.log('messx',messx.length);

                        if (messx.length > 0) {
                            for (var i=0;i<messx.length;i++) {
                                (function(i,msg) {
                                    if (i == (messx.length-1)) {
                                        setTimeout(function() {
                                            eemaLegacy_notBusy();
                                        },20);
                                    }
                                    setTimeout(function() {
                                        $("#legacy-messages-data").prepend(msg + "<br>");
                                    },10);
                                })(i,messx[i]);
                            }
                        }else {
                            eemaLegacy_notBusy();
                        }
                    } else {
                        eemaLegacy_notBusy();
                    }
                }).always(function() {
                    //eemaLegacy_notBusy();
                });
        }
        return false;
    };

    var eemaLegacy_clearAppMessages = function() {
        if (eemaLegacy_selApp != null) {
            $.ajax({
                type: 'POST',
                url: eema_server,
                data: { 'cmd' : 'legacyCleanMessages' , 'gApp' : eemaLegacy_selApp },
                dataType: 'json'
            }).fail(function() {
                alert("error!");
                eemaLegacy_notBusy();
            }).done(function(data) {
                eemaLegacy_notBusy();
                eemaLegacy_loadApp(eemaLegacy_selApp);
            });
        }
    };

    var eemaLegacy_loadApp = function(legacyApp) {
        $('#legacy-messages-data').css('height', (legacySize-180).toString() + 'px');
        if ($('#legacy-messages-data').css('display')!=='none') {
            $('#legacy-messages-data').fadeOut(100);
        }
        $('#legacy-messages-apptitle').html('N/A');
        clearInterval(eemaLegacy_autoRefreshIval);
        eemaLegacy_selApp = null;
        eemaLegacy_busy('Getting messages...');
        $.ajax({
            type: 'POST',
            url: eema_server,
            data: { 'cmd' : 'legacyGetMessages', 'gApp' : legacyApp },
            dataType: 'json'
        }).fail(function() {
            alert("error!");
            eemaLegacy_notBusy();
        }).
        done(function(data) {
            $('#legacy-messages-data').html('');
            var mess = data.result;
            if (mess !== 'No Messages for ' + legacyApp + '.') {
                eemaLegacy_appOldData = mess;
                //console.log('mess.length',mess.length);
                for (var i=0;i<mess.length;i++) {
                    (function(i,msg) {
                        if (i == (mess.length-1)) {
                            setTimeout(function() {
                                $('#legacy-messages-apptitle').html(legacyApp);
                                eemaLegacy_selApp = legacyApp;
                                $('#legacy-messages-help-select').fadeOut(100,function() { $('#legacy-messages-data').fadeIn(100); });
                                if (eemaLegacy_autoRefreshEnabled)
                                    eemaLegacy_autoRefreshIval = setInterval(eemaLegacy_getNewMessages,5000);
                                eemaLegacy_notBusy();
                            },20);
                        }
                        setTimeout(function() {
                            $("#legacy-messages-data").append(msg.date + "&nbsp;&nbsp;&nbsp;" + msg.msg + "<br>");
                        },10);
                    })(i,mess[i]);
                }
            } else {
                $('#legacy-messages-apptitle').html(legacyApp);
                eemaLegacy_selApp = legacyApp;
                $('#legacy-messages-help-select').fadeOut(100,function() { $('#legacy-messages-data').fadeIn(100); });
                if (eemaLegacy_autoRefreshEnabled)
                    eemaLegacy_autoRefreshIval = setInterval(eemaLegacy_getNewMessages,5000);
                eemaLegacy_notBusy();
            }
        }).always(function() {

        });
        return false;
    };

    var eemaLegacy_clearApps = function() {
        eemaLegacy_busy('Clearing apps & messages...');
        $('#legacy-messages-apptitle').html('N/A');
        if ($('#legacy-messages-data').css('display')!=='none') {
            $('#legacy-messages-data').fadeOut(100,function() {
                $('#legacy-messages-data').html('');
                $('#legacy-messages-help-select').fadeIn(100); });
        }
        $.ajax({
            type: 'POST',
            url: eema_server,
            data: { 'cmd' : 'legacyCleanAll' },
            dataType: 'json'
        }).fail(function() {
            alert("error!");
            eemaLegacy_notBusy();
        }).done(function(data) {
            eemaLegacy_notBusy();
            eemaLegacy_refreshApps();
        });
    };

    var eemaLegacy_refreshApps = function() {
        eemaLegacy_busy('Getting active applications list...');
        $('#legacy-aapps-spinner').addClass('fa-spin');
        $('#legacy-messages-apptitle').html('N/A');
        eemaLegacy_selApp = null;
        if ($('#legacy-messages-data').css('display')!=='none') {
            $('#legacy-messages-data').fadeOut(100,function() {
                $('#legacy-messages-data').html('');
                $('#legacy-messages-help-select').fadeIn(100); });
        }
        $('#legacy-aapps-list').fadeOut(100);
        $('#legacy-aapps-list').html('');
        $.ajax({
            type: 'POST',
            url: eema_server,
            data: { 'cmd' : 'legacyGetApps' },
            dataType: 'json'
        }).fail(function() {
            alert("error!");
            eemaLegacy_notBusy();
        }).done(function(data) {
            var appsR = data.result;
            for (var i=0;i<appsR.length;i++) {
                (function(i,appName) {
                    if (i == (appsR.length-1)) {
                        setTimeout(function() {
                            $('#legacy-aapps-list').fadeIn(100);
                            $('#legacy-aapps-spinner').removeClass('fa-spin');
                            eemaLegacy_notBusy();
                        },20);
                    }
                    setTimeout(function() {
                        $('#legacy-aapps-list').append('<a style="cursor: pointer;" onclick="return ' +
                            'eemaLegacy_loadApp(\''+appName+'\');" class="list-group-item">'+appName+'</a>');
                    },10);
                })(i,appsR[i]);
            }
            eemaLegacy_notBusy();
        });
    }
</script>