/**
 * File: QT_MC.js
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

function initialize_MC(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Multiple Choice answers DataTables initialization
     */
    initializeAnswersTable_MC();

    /**
     *  @descr  Binded event to create new Multiple Choice answer
     */
    $("#newAnswer_MC").on("click", function(event){
        newEmptyAnswer_MC();
    });

}

/**
 *  @name   initializeAnswersTable_MC
 *  @descr  Function to initialize Multiple Choice answers table
 */
function initializeAnswersTable_MC(){
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
            showAnswerInfo_MC(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_MC").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_MC
 *  @descr  Binded function to save Multiple Choice question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_MC(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_MC
 *  @descr  Binded event to create a new Multiple Choice question
 */
function createNewQuestion_MC(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_MC
 *  @descr  Get and display informations and translations for requested Multiple Choice answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_MC(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("MC");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_MC
 *  @descr  Ajax request for show empty interface for define a new Multiple Choice answer
 */
function newEmptyAnswer_MC() {
    newEmptyAnswer("MC");
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

function getGivenAnswer_MC(questionDiv){
    var answer = $(questionDiv).find("input:checked");
    return (answer.length > 0) ? new Array(answer.attr("value")) : new Array();
           //new Array($(questionDiv).find("input:checked").attr("value"));
}