// FG-MENU EXENGINE PLUGIN GZipped
var allUIMenus=[];$.fn.menu=function(b){var a=this;b=b;var d=new Menu(a,b);allUIMenus.push(d);$(this).mousedown(function(){d.menuOpen||d.showLoading()}).click(function(){d.menuOpen==false?d.showMenu():d.kill();return false})}; function Menu(b,a){var d=this;b=$(b);var c=$('<div class="fg-menu-container ui-widget ui-widget-content ui-corner-all">'+a.content+"</div>");this.menuExists=this.menuOpen=false;a=jQuery.extend({content:null,width:180,maxHeight:180,positionOpts:{posX:"left",posY:"bottom",offsetX:0,offsetY:0,directionH:"right",directionV:"down",detectH:true,detectV:true,linkToFront:false},showSpeed:200,callerOnState:"ui-state-active",loadingState:"ui-state-loading",linkHover:"ui-state-hover",linkHoverSecondary:"li-hover", crossSpeed:200,crumbDefaultText:"Choose an option:",backLink:true,backLinkText:"Back",flyOut:false,flyOutOnState:"ui-state-default",nextMenuLink:"ui-icon-triangle-1-e",topLinkText:"All",nextCrumbLink:"ui-icon-carat-1-e"},a);var f=function(){$.each(allUIMenus,function(e){allUIMenus[e].menuOpen&&allUIMenus[e].kill()})};this.kill=function(){b.removeClass(a.loadingState).removeClass("fg-menu-open").removeClass(a.callerOnState);c.find("li").removeClass(a.linkHoverSecondary).find("a").removeClass(a.linkHover); a.flyOutOnState&&c.find("li a").removeClass(a.flyOutOnState);a.callerOnState&&b.removeClass(a.callerOnState);c.is(".fg-menu-ipod")&&d.resetDrilldownMenu();c.is(".fg-menu-flyout")&&d.resetFlyoutMenu();c.parent().hide();d.menuOpen=false;$(document).unbind("click",f);$(document).unbind("keydown")};this.showLoading=function(){b.addClass(a.loadingState)};this.showMenu=function(){f();d.menuExists||d.create();b.addClass("fg-menu-open").addClass(a.callerOnState);c.parent().show().click(function(){d.kill(); return false});c.hide().slideDown(a.showSpeed).find(".fg-menu:eq(0)");d.menuOpen=true;b.removeClass(a.loadingState);$(document).click(f);$(document).keydown(function(e){var g;if(e.which!="")g=e.which;else if(e.charCode!="")g=e.charCode;else if(e.keyCode!="")g=e.keyCode;var h=$(e.target).parents("div").is(".fg-menu-flyout")?"flyout":"ipod";switch(g){case 37:if(h=="flyout"){$(e.target).trigger("mouseout");$("."+a.flyOutOnState).size()>0&&$("."+a.flyOutOnState).trigger("mouseover")}if(h=="ipod"){$(e.target).trigger("mouseout"); $(".fg-menu-footer").find("a").size()>0&&$(".fg-menu-footer").find("a").trigger("click");$(".fg-menu-header").find("a").size()>0&&$(".fg-menu-current-crumb").prev().find("a").trigger("click");$(".fg-menu-current").prev().is(".fg-menu-indicator")&&$(".fg-menu-current").prev().trigger("mouseover")}return false;case 38:if($(e.target).is("."+a.linkHover)){g=$(e.target).parent().prev().find("a:eq(0)");if(g.size()>0){$(e.target).trigger("mouseout");g.trigger("mouseover")}}else c.find("a:eq(0)").trigger("mouseover"); return false;case 39:if($(e.target).is(".fg-menu-indicator"))if(h=="flyout")$(e.target).next().find("a:eq(0)").trigger("mouseover");else if(h=="ipod"){$(e.target).trigger("click");setTimeout(function(){$(e.target).next().find("a:eq(0)").trigger("mouseover")},a.crossSpeed)}return false;case 40:if($(e.target).is("."+a.linkHover)){g=$(e.target).parent().next().find("a:eq(0)");if(g.size()>0){$(e.target).trigger("mouseout");g.trigger("mouseover")}}else c.find("a:eq(0)").trigger("mouseover");return false; case 27:f();break;case 13:if($(e.target).is(".fg-menu-indicator")&&h=="ipod"){$(e.target).trigger("click");setTimeout(function(){$(e.target).next().find("a:eq(0)").trigger("mouseover")},a.crossSpeed)}break}})};this.create=function(){c.css({width:a.width}).appendTo("body").find("ul:first").not(".fg-menu-breadcrumb").addClass("fg-menu");c.find("ul, li a").addClass("ui-corner-all");c.find("ul").attr("role","menu").eq(0).attr("aria-activedescendant","active-menuitem").attr("aria-labelledby",b.attr("id")); c.find("li").attr("role","menuitem");c.find("li:has(ul)").attr("aria-haspopup","true").find("ul").attr("aria-expanded","false");c.find("a").attr("tabindex","-1");if(c.find("ul").size()>1)a.flyOut?d.flyout(c,a):d.drilldown(c,a);else c.find("a").click(function(){d.chooseItem(this);return false});a.linkHover&&c.find(".fg-menu li a").hover(function(){$(this);$("."+a.linkHover).removeClass(a.linkHover).blur().parent().removeAttr("id");$(this).addClass(a.linkHover).focus().parent().attr("id","active-menuitem")}, function(){$(this).removeClass(a.linkHover).blur().parent().removeAttr("id")});a.linkHoverSecondary&&c.find(".fg-menu li").hover(function(){$(this).siblings("li").removeClass(a.linkHoverSecondary);a.flyOutOnState&&$(this).siblings("li").find("a").removeClass(a.flyOutOnState);$(this).addClass(a.linkHoverSecondary)},function(){$(this).removeClass(a.linkHoverSecondary)});d.setPosition(c,b,a);d.menuExists=true};this.chooseItem=function(e){d.kill();location.href=$(e).attr("href")}} Menu.prototype.flyout=function(b,a){var d=this;this.resetFlyoutMenu=function(){b.find("ul ul").removeClass("ui-widget-content").hide()};b.addClass("fg-menu-flyout").find("li:has(ul)").each(function(){var c=b.width(),f,e,g=$(this).find("ul");g.css({left:c,width:c}).hide();$(this).find("a:eq(0)").addClass("fg-menu-indicator").html("<span>"+$(this).find("a:eq(0)").text()+'</span><span class="ui-icon '+a.nextMenuLink+'"></span>').hover(function(){clearTimeout(e);var h=$(this).next();fitVertical(h,$(this).offset().top)|| h.css({top:"auto",bottom:0});fitHorizontal(h,$(this).offset().left+100)||h.css({left:"auto",right:c,"z-index":999});f=setTimeout(function(){h.addClass("ui-widget-content").show(a.showSpeed).attr("aria-expanded","true")},300)},function(){clearTimeout(f);var h=$(this).next();e=setTimeout(function(){h.removeClass("ui-widget-content").hide(a.showSpeed).attr("aria-expanded","false")},400)});$(this).find("ul a").hover(function(){clearTimeout(e);$(this).parents("ul").prev().is("a.fg-menu-indicator")&&$(this).parents("ul").prev().addClass(a.flyOutOnState)}, function(){e=setTimeout(function(){g.hide(a.showSpeed);b.find(a.flyOutOnState).removeClass(a.flyOutOnState)},500)})});b.find("a").click(function(){d.chooseItem(this);return false})}; Menu.prototype.drilldown=function(b,a){var d=this,c=b.find(".fg-menu"),f=$('<ul class="fg-menu-breadcrumb ui-widget-header ui-corner-all ui-helper-clearfix"></ul>'),e=$('<li class="fg-menu-breadcrumb-text">'+a.crumbDefaultText+"</li>"),g=$('<li class="'+(a.backLink?"fg-menu-prev-list":"fg-menu-all-lists")+'"><a href="#" class="'+(a.backLink?"ui-state-default ui-corner-all":"")+'">'+(a.backLink?'<span class="ui-icon ui-icon-triangle-1-w"></span>':"")+(a.backLink?a.backLinkText:a.topLinkText)+"</a></li>"); b.addClass("fg-menu-ipod");a.backLink?f.addClass("fg-menu-footer").appendTo(b).hide():f.addClass("fg-menu-header").prependTo(b);f.append(e);var h=function(i){i.height()>a.maxHeight&&i.addClass("fg-menu-scroll");i.css({height:a.maxHeight})},m=function(i){i.removeClass("fg-menu-scroll").removeClass("fg-menu-current").height("auto")};this.resetDrilldownMenu=function(){$(".fg-menu-current").removeClass("fg-menu-current");c.animate({left:0},a.crossSpeed,function(){$(this).find("ul").each(function(){$(this).hide(); m($(this))});c.addClass("fg-menu-current")});$(".fg-menu-all-lists").find("span").remove();f.empty().append(e);$(".fg-menu-footer").empty().hide();h(c)};c.addClass("fg-menu-content fg-menu-current ui-widget-content ui-helper-clearfix").css({width:b.width()}).find("ul").css({width:b.width(),left:b.width()}).addClass("ui-widget-content").hide();h(c);c.find("a").each(function(){$(this).next().is("ul")?$(this).addClass("fg-menu-indicator").each(function(){$(this).html("<span>"+$(this).text()+'</span><span class="ui-icon '+ a.nextMenuLink+'"></span>')}).click(function(){var i=$(this).next(),p=$(this).parents("ul:eq(0)"),n=p.is(".fg-menu-content")?0:parseFloat(c.css("left"));n=Math.round(n-parseFloat(b.width()));var l=$(".fg-menu-footer");m(p);h(i);c.animate({left:n},a.crossSpeed);i.show().addClass("fg-menu-current").attr("aria-expanded","true");var q=function(j){var k=$(".fg-menu-current"),o=k.parents("ul:eq(0)");k.hide().attr("aria-expanded","false");m(k);h(o);o.addClass("fg-menu-current").attr("aria-expanded","true"); if(o.hasClass("fg-menu-content")){j.remove();l.hide()}};if(a.backLink){if(l.find("a").size()==0){l.show();$('<a href="#"><span class="ui-icon ui-icon-triangle-1-w"></span> <span>Back</span></a>').appendTo(l).click(function(){var j=$(this),k=parseFloat(c.css("left"))+b.width();c.animate({left:k},a.crossSpeed,function(){q(j)});return false})}}else{if(f.find("li").size()==1){f.empty().append(g);g.find("a").click(function(){d.resetDrilldownMenu();return false})}$(".fg-menu-current-crumb").removeClass("fg-menu-current-crumb"); i=$(this).find("span:eq(0)").text();i=$('<li class="fg-menu-current-crumb"><a href="javascript://" class="fg-menu-crumb">'+i+"</a></li>");i.appendTo(f).find("a").click(function(){if($(this).parent().is(".fg-menu-current-crumb"))d.chooseItem(this);else{var j=-($(".fg-menu-current").parents("ul").size()-1)*180;c.animate({left:j},a.crossSpeed,function(){q()});$(this).parent().addClass("fg-menu-current-crumb").find("span").remove();$(this).parent().nextAll().remove()}return false});i.prev().append(' <span class="ui-icon '+ a.nextCrumbLink+'"></span>')}return false}):$(this).click(function(){d.chooseItem(this);return false})})}; Menu.prototype.setPosition=function(b,a,d){var c={refX:a.offset().left,refY:a.offset().top,refW:a.getTotalWidth(),refH:a.getTotalHeight()};d=d;var f,e,g=$('<div class="positionHelper"></div>');g.css({position:"absolute",left:c.refX,top:c.refY,width:c.refW,height:c.refH});b.wrap(g);switch(d.positionOpts.posX){case "left":f=0;break;case "center":f=c.refW/2;break;case "right":f=c.refW;break}switch(d.positionOpts.posY){case "top":e=0;break;case "center":e=c.refH/2;break;case "bottom":e=c.refH;break}f+= d.positionOpts.offsetX;e+=d.positionOpts.offsetY;if(d.positionOpts.directionV=="up"){b.css({top:"auto",bottom:e});d.positionOpts.detectV&&!fitVertical(b)&&b.css({bottom:"auto",top:e})}else{b.css({bottom:"auto",top:e});d.positionOpts.detectV&&!fitVertical(b)&&b.css({top:"auto",bottom:e})}if(d.positionOpts.directionH=="left"){b.css({left:"auto",right:f});d.positionOpts.detectH&&!fitHorizontal(b)&&b.css({right:"auto",left:f})}else{b.css({right:"auto",left:f});d.positionOpts.detectH&&!fitHorizontal(b)&& b.css({left:"auto",right:f})}d.positionOpts.linkToFront&&a.clone().addClass("linkClone").css({position:"absolute",top:0,right:"auto",bottom:"auto",left:0,width:a.width(),height:a.height()}).insertAfter(b)};function sortBigToSmall(b,a){return a-b}jQuery.fn.getTotalWidth=function(){return $(this).width()+parseInt($(this).css("paddingRight"))+parseInt($(this).css("paddingLeft"))+parseInt($(this).css("borderRightWidth"))+parseInt($(this).css("borderLeftWidth"))}; jQuery.fn.getTotalHeight=function(){return $(this).height()+parseInt($(this).css("paddingTop"))+parseInt($(this).css("paddingBottom"))+parseInt($(this).css("borderTopWidth"))+parseInt($(this).css("borderBottomWidth"))};function getScrollTop(){return self.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop}function getScrollLeft(){return self.pageXOffset||document.documentElement.scrollLeft||document.body.scrollLeft} function getWindowHeight(){var b=document.documentElement;return self.innerHeight||b&&b.clientHeight||document.body.clientHeight}function getWindowWidth(){var b=document.documentElement;return self.innerWidth||b&&b.clientWidth||document.body.clientWidth}function fitHorizontal(b,a){a=parseInt(a)||$(b).offset().left;return a+$(b).width()<=getWindowWidth()+getScrollLeft()&&a-getScrollLeft()>=0} function fitVertical(b,a){a=parseInt(a)||$(b).offset().top;return a+$(b).height()<=getWindowHeight()+getScrollTop()&&a-getScrollTop()>=0} Number.prototype.pxToEm=String.prototype.pxToEm=function(b){b=jQuery.extend({scope:"body",reverse:false},b);var a=this==""?0:parseFloat(this),d,c=function(){var f=document.documentElement;return self.innerWidth||f&&f.clientWidth||document.body.clientWidth};d=b.scope=="body"&&$.browser.msie&&(parseFloat($("body").css("font-size"))/c()).toFixed(1)>0?function(){return(parseFloat($("body").css("font-size"))/c()).toFixed(3)*16}():parseFloat(jQuery(b.scope).css("font-size"));return b.reverse==true?(a*d).toFixed(2)+ "px":(a/d).toFixed(2)+"em"};