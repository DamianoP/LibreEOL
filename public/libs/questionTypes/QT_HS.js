/**
 * File: QT_HS.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for Multiple Choice questions
 */

// Answer Table Column Index
var atci = {
    score : 0,
    text : 1,
    answerID : 2
};
//
//$( "#contentContainer" ).click( function( event ) {
//    $x = event.pageX;
//    $y = event.pageY;
//    cosole.log("PROVA");
//    cosole.log("x="+ $x + ", y=" + $y);
//    //alert(event.pageX);
//    //$( "#log" ).text( "pageX: " + event.pageX + ", pageY: " + event.pageY );
//});
//
//function prova(event) {
//    alert("PROVA");
//    //cosole.log("PROVA");
//    var x = event.pageX;
//    var y = event.pageY;
//    alert("x="+ x + ", y=" + y);
//    //alert(event.pageX);
//    //$( "#log" ).text( "pageX: " + event.pageX + ", pageY: " + event.pageY );
//};
//
//$(function () {
////$("#contentContainer").click(function () {
//    alert("ciaoooo");
//    var container = $( "#contentContainer" );
//    console.log(container);
//    var soccerball = $( "#soccerball" );
//    console.log(soccerball);
//    var theThing = $("#thing");
//    console.log(theThing);
//});

// Sposta il puntatore in durante il test
function riposiziona(elemento,top,left){
    setTimeout(function() { 
        var immagine= $(elemento);
        immagine.css("left",top+"px");
        immagine.css("top",left+"px");   
    }, 2000);

}
function getClickPosition(container,event) {
    var container= $(container);
    container.css('position','relative');
    var theThing = container.find("#thing");
    theThing.css('position','absolute');
    var parentOffset = container.parent().offset();
    var x = (event.pageX - parentOffset.left - 20 - 13);
    var y = (event.pageY - parentOffset.top - 20 - 13);
    var xPositionPX = x + "px";
    var yPositionPX = y + "px";
    theThing.css("left",xPositionPX);
    theThing.css("top",yPositionPX);    
}

function initialize_HS(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Hotspot answers DataTables initialization
     */
    initializeAnswersTable_HS();

    /**
     *  @descr  Binded event to create new Multiple Choice answer
     */
    $("#newAnswer_HS").on("click", function(event){
        newEmptyAnswer_HS();
    });

}

/**
 *  @name   initializeAnswersTable_HS
 *  @descr  Function to initialize Multiple Choice answers table
 */
function initializeAnswersTable_HS(){
    answersTable = $("#answersTable").DataTable({
        scrollY:        100,
        scrollCollapse: false,
        jQueryUI:       true,
        paging:         false,
        bSort : false,
        columns : [
            { className: "aScore", width : "10px" },
            { className: "aText", width : "740px", mRender: function(data){return truncate(data, '740px')} },
            { className: "aAnswerID", visible : false }
        ],
        language : {
            info: ttDTAnswerInfo,
            infoFiltered: ttDTAnswerFiltered,
            infoEmpty: ttDTAnswerEmpty
        }
    }).on("dblclick", "td", function(){
            showAnswerInfo_HS(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_HS").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_HS
 *  @descr  Binded function to save Multiple Choice question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_HS(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_HS
 *  @descr  Binded event to create a new Multiple Choice question
 */
function createNewQuestion_HS(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_HS
 *  @descr  Get and display informations and translations for requested Multiple Choice answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_HS(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("HS");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_HS
 *  @descr  Ajax request for show empty interface for define a new Multiple Choice answer
 */
function newEmptyAnswer_HS() {
    newEmptyAnswer("HS");
}

function questionInfoTabChanged(event, ui){
    if(ui.newTab.index() == 0){             // Question tab selected
        var lang = $("#qLangsTabs a.tab.active").attr("value");
//        destroyAllCKEditorInstances();        Unnecessary
        if(!(CKEDITOR.instances["qt"+lang]))
            createCKEditorInstance("qt"+lang);
    }else if(ui.newTab.index() == 1){       // Answer tab selected
        answersTable.columns.adjust();
    }
}

function getGivenAnswer_HS(questionDiv){

    var container = $(questionDiv).find(".contentContainer");

    var theThing = $(questionDiv).find(".hscursor");

    //var posContainer = container.position();

    var positionn = theThing.position();

    //alert(positionn.left+" "+positionn.top);
    /*
    var posx = positionn.left - 20;
    var posy = positionn.top - posContainer.top - 20;
    */

    var posx = positionn.left;
    var posy = positionn.top;


    //var mod = posx + "," + posy;
    //var ris = new Array(mod);

    var ris = new Array(posx , posy);

    //alert (ris);
    //var riss = JSON.stringify(ris);
    //alert("riss=" + riss);

    return ris;
}
