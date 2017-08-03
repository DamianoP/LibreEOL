/**
 * File: QT_PL.js
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
//subque table
var ztci = {
    score : 0,
    text : 1,
    subID : 2
};
var subQuestionClick = false;
var selectedRow;
function initialize_PL(){

    createCKEditorInstance("qt"+mainLang);
    initializeSubquestionsTable_PL();
    initializeAnswersTable_PL();
    /**
     *  @descr  Binded event to create new Multiple Response answer
     */

    $("#newAnswer_PL").on("click", function(event){
        if(subQuestionClick){
            newEmptyAnswer_PL();
        }else{
            showErrorMessage(ttSelectSubQuestion);
        }

    });
    $( "#deleteSubQuestion" ).on("click", function(event){
        if(subQuestionClick){
            deleteSubQuestion();
        }else{
            showErrorMessage(ttSelectSubQuestion);
        }
    });
    $("#newSubquestion_PL").on("click", function(event){
        newEmptySubquestion_PL();
    });

};

/**
 *  @name   initializeAnswersTable_PL
 *  @descr  Function to initialize Multiple Response answers table
 */
function initializeAnswersTable_PL(){
        answersTable = $("#answersTable").DataTable({
            scrollY: 100,
            scrollCollapse: false,
            jQueryUI: true,
            paging: false,
            bSort: false,
            loading : true,
            columns: [
                {
                    className: "aScore", width: "10px"
                },
                {
                    className: "aText", width: "740px", mRender: function (data) {
                    return truncate(data, '740px')
                    }
                },
                {
                    className:"aAnswerID",visible:false
                }

            ],
            language: {
                info: ttDTAnswerInfo,
                infoFiltered: ttDTAnswerFiltered,
                infoEmpty: ttDTAnswerEmpty
            }
        }).on("dblclick", "td", function () {
            showAnswerInfo_PL(new Array($(this).parent(), answerEditing));
        });


        $("#answersTable_filter").css("margin-right", "50px")
            .after($("#newAnswer_PL").parent())
            .before($("#answersTable_info"))
            .hide();

        $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));


}
function initializeSubquestionsTable_PL(){
    subquestionsTable = $("#subquestionsTable").DataTable({
        scrollY:        100,
        scrollCollapse: false,
        jQueryUI:       true,
        paging:         false,
        bSort : false,
        columns :  [

            { className: "zScore", width : "10px",visible : false},
            { className: "zText", width : "740px", mRender: function(data){return truncate(data, '740px')}, visible :  true },
            { className: "zSubID", visible :  false}
        ],

        language : {
           // info: ttDTsubquestionInfo,
            info: ttDTsubquestionInfo,
            infoFiltered: ttDTAnswerFiltered,
            //infoEmpty: ttDTSubquestionEmpty
            infoEmpty: ttDTsubquestionInfo
        }

    }).on("click", "td", function(){

            oTable = $('#subquestionsTable').dataTable();
            var aPos = oTable.fnGetPosition(this);
            var aData = oTable.fnGetData(aPos[0]);
            adNumber = aData[2];
            subQuestionClick = true;
            selectedRow = subquestionsTable.row( $(this).parents('tr') );
            var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
            $.ajax({
                url     : "index.php?page=question/Getanswersubinfo",
                type    : "post",
                data    : {
                    idQuestion      :   idQuestion,
                    idSubQuestion        :   adNumber,
                    mainLang        :   mainLang
                },
                success : function (data) {
                    data = data.trim().split(ajaxSeparator);
                    if(data[0] == "ACK"){
                        var answer = JSON.parse(data[1]);
                        answersTable.clear().draw();
                        for(var i = 0;i < answer.length; i++){
                            answersTable.row.add([
                                answer[i]["score"],
                                answer[i]["translation"],
                                answer[i]["idAnswer"]
                            ]).draw();
                        }
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });


        });

    $("#subquestionsTable_filter").css("margin-right", "50px")
        .after($("#newSubquestion_PL").parent())
        .before($("#subquestionsTable_info"))
        .hide();

    $("#subquestionsTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));

}

function deleteSubQuestion() {
    subQuestionClick = false;
    subquestionsTable.row(selectedRow).remove().draw();
    answersTable.clear().draw();
    $.ajax({
        url     : "index.php?page=question/Deletesubquestion",
        type    : "post",
        data    : {
            idSubQuestion        :   adNumber
        },
        success : function (data) {
            data = data.trim().split(ajaxSeparator);
            if(data[0] != "ACK"){
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}
/**
 *  @name   saveQuestionInfo_PL
 *  @descr  Binded function to save Multiple Response question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_PL(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_PL
 *  @descr  Binded event to create a new Multiple Response question
 */
function createNewQuestion_PL(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}
function createNewSubQuestion_PL(){
    createNewSubQuestion(reopen = true);                                               // Use normal create function
}
/**
 *  @name   showAnswerInfo_PL
 *  @descr  Get and display informations and translations for requested Multiple Response answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_PL(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("PL");
    showAnswerInfo(selectedAnswerAndConfirm);
}
function showSubquestionsInfo_PL(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("PL");
    showSubquestionsInfo(selectedAnswerAndConfirm);
}


/**
 *  @name   newEmptyAnswer_PL
 *  @descr  Ajax request for show empty interface for define a new Multiple Response answer
 */
function newEmptyAnswer_PL() {
    newEmptyAnswer("PL");
}
function newEmptySubquestion_PL() {
    newEmptySubquestion("PL");
}
//when tab change
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

function getGivenAnswer_PL(questionDiv){
    var answer = [];
    $(questionDiv).find("option:selected").each(function(index, input){
        answer.push($(input).attr("value"));
    });

    return answer;

}


