/**
 * File:
 * User: Masterplan
 * Date: 23/10/14
 * Time: 10:56
 * Desc:
 */

$(function(){

    $('#contents, #main').height($(window).height() - 100);             // Contents and main height = window - top
    $('#contentsList, #help').height($("#contents").height() - 33);     // ContentsList and help height = contents - header
    $(window).resize(function(){
        $('#contents, #main').height($(window).height() - 100);
        $('#contentsList, #help').height($("#contents").height() - 33);
    });

    $.ajax({
        url     : "contents.html",
        cache: false,
        beforeSend: function() {
            $('#contentsList').html('<div class="loading"><img src="../loading.gif"/></div>');
        },
        success : function (data) {
            $("#contentsList").html(data).find("link, title, meta").remove();
            $("#contentsList a.showHelp").on("click", function(event){showHelp($(this), event)});
            $("#contentsList li span.submenuIcon").html("&nbsp;&nbsp;&nbsp;&nbsp;")
                                                  .on("click", function(){showHideContents($(this))});
        },
        error : function (){ print404(); }
    });

    $.ajax({
        url     : "eol.html",
        cache: false,
        beforeSend: function() {
            $("#help").html('<div class="loading"><img src="../loading.gif"/></div>');
        },
        success : function (data) {
            $("#help").html($(data)).find("link, title, meta").remove();
            $("#help a.showHelp").on("click", function(event){showHelp($(this), event)});
        },
        error : function (){ print404(); }
    });

});

function showHelp(content, event){
    event.preventDefault();
    var helpPage = content.attr("href");
    $.ajax({
        url     : helpPage,
        cache: false,
        beforeSend: function() {
            $("#help").html('<div class="loading"><img src="../loading.gif"/></div>');
        },
        success : function (data) {
            $("#help").html($(data)).find("link, title, meta").remove();
            $("#help a.showHelp").on("click", function(event){showHelp($(this), event)});
        },
        error : function (){ print404(); }
    });
}

function showHideContents(content){
    var li = content.parent();
    if(li.hasClass("closed")){
        li.removeClass("closed");
        li.next().removeClass("closed");
    }else{
        li.addClass("closed");
        li.next().addClass("closed");
    }
}

function contentsToggle(tool){
    var contents = $("#contents");
    var main = $("#main");
    if(contents.hasClass("closed")){        // Open contents panel and reduce main panel
        contents.removeClass("closed");
        main.removeClass("extended");
        tool.removeClass("right").addClass("left");
    }else{                                  // Close contents panel and extend main panel
        contents.addClass("closed");
        main.addClass("extended");
        tool.removeClass("left").addClass("right");
    }
}

function print404(){
    $.ajax({
        url     : "404.html",
        cache: false,
        beforeSend: function() {
            $("#help").html('<div class="loading"><img src="../loading.gif"/></div>');
        },
        success : function (data) {
            $("#help").html($(data)).find("link, title, meta").remove();
            $("#help a.showHelp").on("click", function(event){showHelp($(this), event)});
        },
        error : function (request, status, error) {
            $("#help").html("<h3>jQuery Ajax request error</h3>");
        }
    });
}