/**
 * File: QT_MR.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for Multiple Response questions
 */

// Answer Table Column Index
var atci = {
    score : 0,
    text : 1,
    answerID : 2
};

function initialize_MR(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Multiple Response answers DataTables initialization
     */
    initializeAnswersTable_MR();

    /**
     *  @descr  Binded event to create new Multiple Response answer
     */
    $("#newAnswer_MR").on("click", function(event){
        newEmptyAnswer_MR();
    });

};

/**
 *  @name   initializeAnswersTable_MR
 *  @descr  Function to initialize Multiple Response answers table
 */
function initializeAnswersTable_MR(){
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
            showAnswerInfo_MR(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_MR").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_MR
 *  @descr  Binded function to save Multiple Response question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_MR(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_MR
 *  @descr  Binded event to create a new Multiple Response question
 */
function createNewQuestion_MR(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_MR
 *  @descr  Get and display informations and translations for requested Multiple Response answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_MR(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("MR");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_MR
 *  @descr  Ajax request for show empty interface for define a new Multiple Response answer
 */
function newEmptyAnswer_MR() {
    newEmptyAnswer("MR");
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

function getGivenAnswer_MR(questionDiv){
    var answer = [];
    $(questionDiv).find("input:checked").each(function(index, input){
                       answer.push($(input).attr("value"));
                   });
    return answer;
}