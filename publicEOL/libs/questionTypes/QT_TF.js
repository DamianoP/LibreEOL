/**
 * File: QT_TF.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for True/False questions
 */

// Answer Table Column Index
var atci = {
    score : 0,
    text : 1,
    answerID : 2
};

function initialize_TF(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  True/False answers DataTables initialization
     */
    initializeAnswersTable_TF();

}

/**
 *  @name   initializeAnswersTable_TF
 *  @descr  Function to initialize True/False answers table
 */
function initializeAnswersTable_TF(){
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
            showAnswerInfo_TF(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").before($("#answersTable_info")).hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_TF
 *  @descr  Binded function to save True/False question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_TF(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_TF
 *  @descr  Binded event to create a new True/False question
 */
function createNewQuestion_TF(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_TF
 *  @descr  Get and display informations and translations for requested True/False answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_TF(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("TF");
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

function getGivenAnswer_TF(questionDiv){
    var answer = $(questionDiv).find("input:checked");
    return (answer.length > 0) ? new Array(answer.attr("value")) : new Array();
}