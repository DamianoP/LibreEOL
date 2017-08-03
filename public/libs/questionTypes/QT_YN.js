/**
 * File: QT_YN.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for Yes/No questions
 */

// Answer Table Column Index
var atci = {
    score : 0,
    text : 1,
    answerID : 2
};

function initialize_YN(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Yes/No answers DataTables initialization
     */
    initializeAnswersTable_YN();

}

/**
 *  @name   initializeAnswersTable_YN
 *  @descr  Function to initialize Yes/No answers table
 */
function initializeAnswersTable_YN(){
    answersTable = $("#answersTable").DataTable({
        scrollY:        60,
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
            showAnswerInfo_YN(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").before($("#answersTable_info")).hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_YN
 *  @descr  Binded function to save Yes/No question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_YN(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_YN
 *  @descr  Binded event to create a new Yes/No question
 */
function createNewQuestion_YN(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_YN
 *  @descr  Get and display informations and translations for requested Yes/No answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_YN(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("YN");
    showAnswerInfo(selectedAnswerAndConfirm);
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

function getGivenAnswer_YN(questionDiv){
    var answer = $(questionDiv).find("input:checked");
    return (answer.length > 0) ? new Array(answer.attr("value")) : new Array();
}