/**
 * File: QT_NM.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for Numeric questions
 */

/** Classe copiata MR */
// Answer Table Column Index
var atci;
atci = {
    score: 0,
    text: 1,
    answerID: 2
};

function initialize_NM(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  Numeric answers DataTables initialization
     */
    initializeAnswersTable_NM();

    /**
     *  @descr  Binded event to create new Numeric answer
     */
    $("#newAnswer_NM").on("click", function(event){
        newEmptyAnswer_NM();
    });

};

/**
 *  @name   initializeAnswersTable_NM
 *  @descr  Function to initialize Numeric answers table
 */
function initializeAnswersTable_NM(){
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
            showAnswerInfo_NM(new Array($(this).parent(), answerEditing));
        });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_NM").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_NM
 *  @descr  Binded function to save Numeric question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_NM(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_NM
 *  @descr  Binded event to create a new Numeric question
 */
function createNewQuestion_NM(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_NM
 *  @descr  Get and display informations and translations for requested Numeric answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_NM(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("NM");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_NM
 *  @descr  Ajax request for show empty interface for define a new Numeric answer
 */
function newEmptyAnswer_NM() {
    newEmptyAnswer("NM");
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


function getGivenAnswer_NM(questionDiv){
    var val = $(questionDiv).find("#inputNumber").val();
    return new Array($(questionDiv).find("#inputNumber").val());
}


function controlNum(){
    var valore = document.getElementById("inputNumber").value;
    num = $.isNumeric(valore);
    if ((num == false) && (valore !="")) {
            showErrorMessage (ttNumberError);
        }
}


