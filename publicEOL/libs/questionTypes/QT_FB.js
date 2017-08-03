/**
 * Created by alberto on 20/03/2015.
 */


// Answer Table Column Index
var atci = {
    score : 0,
    text : 1,
    answerID : 2
};

function initialize_FB(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Fill in blanks
     */
    initializeAnswersTable_FB();

    /**
     *  @descr  Binded event to create new Fill in blanks answer
     */
    $("#newAnswer_FB").on("click", function(event){
        newEmptyAnswer_FB();
    });

}

/**
 *  @name   initializeAnswersTable_FB
 *  @descr  Function to initialize Multiple Choice answers table
 */
function initializeAnswersTable_FB(){
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
        showAnswerInfo_FB(new Array($(this).parent(), answerEditing));
    });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_FB").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_FB
 *  @descr  Binded function to save fill in blanks question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_FB(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_FB
 *  @descr  Binded event to create a new fill in blanks question
 */
function createNewQuestion_FB(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_FB
 *  @descr  Get and display informations and translations for requested Multiple Choice answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_FB(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("FB");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_FB
 *  @descr  Ajax request for show empty interface for define a new Multiple Choice answer
 */
function newEmptyAnswer_FB() {
    newEmptyAnswer("FB");
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

function getGivenAnswer_FB(questionDiv){

    var answer = [];
    $(questionDiv).find("input:text").each(function(index, input){
        answer.push($(input).val());

    });
    return answer;

}