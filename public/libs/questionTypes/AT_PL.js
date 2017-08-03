/**
 * File: AT_PL.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for answer in Multiple Response questions
 */

$(function(){

    createCKEditorInstance("at"+mainLang);

    /**
     *  @descr  Function to enable answer tab's dropdownInfo menu effects
     */
    $("#answerInfo .dropdownInfo dt.writable").on("click", function() {
        $(this).children("span").toggleClass("clicked");
        $(this).next().children("ol").slideToggle(200);
    });

    /**
     *  @descr  Function to change infos
     */
    $("#answerInfo .dropdownInfo dd ol li").on("click", function() {
        answerEditing = true;
        updateDropdown($(this));
    });

});

/**
 *  @name   createNewAnswer_PL
 *  @descr  Binded event to create a new Multiple Response answer
 */
function createNewAnswer_PL(){
    var score = $("#answerScore").find("dt span span").text();


    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    //var adNumber = 5;
    console.log("adNumber inviato al db: "+ adNumber);
    var translationsA = new Array();

    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val();
    });
    
    $.ajax({
        url     : "index.php?page=question/newanswersub",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            subQuestion     :   adNumber,
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "PL",
            mainLang        :   mainLang
        },
        success : function (data) {

            //     alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                var idNewQuestion = data[1];
                if(parseInt(idQuestion) != parseInt(idNewQuestion)){        // Question duplicated, reload answers table
                    reloadAnswersTable(idNewQuestion, data[2]);
                }else{
                    answersTable.row.add(JSON.parse(data[2])).draw();
                }
                showSuccessMessage(ttMNewAnswer);
                setTimeout(function(){
                    if(close){
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeAnswerInfo(false);
                        setTimeout(function(){ answersTable.columns.adjust(); }, 500);
                    }
                }, 1000);
            }else{
                alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

function createNewSubQuestion_PL(){
    var score = $("#answerScore").find("dt span span").text();
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    var translationsA = new Array();
    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val();
    });
    console.log(translationsA);
    $.ajax({
        url     : "index.php?page=question/newsubquestion",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "PL",
            mainLang        :   mainLang
        },
        success : function (data) {

// alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                var idNewQuestion = data[1];
                //alert(data[2]);
                if(parseInt(idQuestion) != parseInt(idNewQuestion)){        // Question duplicated, reload answers table
                    reloadSubquestionsTable(idNewQuestion, data[2]);

                }else{
                    subquestionsTable.row.add(JSON.parse(data[2])).draw();
                }
                showSuccessMessage(ttMNewQuestion);
                setTimeout(function(){
                    if(close){
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeAnswerInfo(false);
                        setTimeout(function(){ answersTable.columns.adjust(); }, 500);
                    }
                }, 1000);
            }else{

                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
/**
 *  @name   saveAnswerInfo_PL
 *  @descr  Binded function to save Multiple Response answer info
 *  @param  close       Boolean                     Close panel if true
 */
function saveAnswerInfo_PL(close){
    var score = $("#answerScore").find("dt span span").text();
    var translationsA = new Array();
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
  //var subquestion = subquestionsTable.row(questionRowSelected).data()[ztci.subID];
    //alert(subquestion);
    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val()
    });
    $.ajax({
        url     : "index.php?page=question/Updateanswerinfo",
        type    : "post",
        data    : {

            idQuestion      :   idQuestion,
            idAnswer        :   answersTable.row(answerRowSelected).data()[atci.answerID],
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "PL",
            mainLang        :   mainLang
        },
        success : function (data) {
//            alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                var idNewQuestion = data[1];
                if(parseInt(idQuestion) != parseInt(idNewQuestion)){        // Question duplicated, reload answers table
                    reloadAnswersTable(idNewQuestion, data[2]);
                }else{
                    answersTable.row(answerRowSelected).data(JSON.parse(data[2])).draw();
                    scrollToRow(answersTable, answerRowSelected);
                }
                showSuccessMessage(ttMEdit);
                setTimeout(function(){
                    if(close){
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeAnswerInfo(false);
                        setTimeout(function(){ answersTable.columns.adjust(); }, 500);
                    }
                }, 1000);
            }else{
//                alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}function saveSubInfo_PL(close){
    var score = $("#answerScore").find("dt span span").text();
    var translationsA = new Array();
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val();
    });
    $.ajax({
        url     : "index.php?page=question/updatesubinfo",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            idAnswer        :   answersTable.row(answerRowSelected).data()[atci.answerID],
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "PL",
            mainLang        :   mainLang
        },
        success : function (data) {
//            alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                var idNewQuestion = data[1];
                if(parseInt(idQuestion) != parseInt(idNewQuestion)){        // Question duplicated, reload answers table
                    reloadAnswersTable(idNewQuestion, data[2]);
                }else{
                    answersTable.row(answerRowSelected).data(JSON.parse(data[2])).draw();
                    scrollToRow(answersTable, answerRowSelected);
                }
                showSuccessMessage(ttMEdit);
                setTimeout(function(){
                    if(close){
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeAnswerInfo(false);
                        setTimeout(function(){ answersTable.columns.adjust(); }, 500);
                    }
                }, 1000);
            }else{
//                alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   deleteAnswer_PL
 *  @descr  Binded function to delete a Multiple Response answer
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function deleteAnswer_PL(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteAnswer, deleteAnswer_PL, false)))){
        var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
        var idAnswer = answersTable.row(answerRowSelected).data()[atci.answerID];
        $.ajax({
            url     : "index.php?page=question/deleteanswer",
            type    : "post",
            data    : {
                idQuestion  :   idQuestion,
                idAnswer    :   idAnswer,
                type        :   "PL"
            },
            success : function (data) {
                data = data.split(ajaxSeparator);
                if(data[0] == "ACK"){
                    var idNewQuestion = data[1];
                    if(parseInt(idQuestion) != parseInt(idNewQuestion)){        // Question duplicated, reload answers table
                        reloadAnswersTable(idNewQuestion, data[2]);
                    }else{
                        answersTable.row(answerRowSelected).remove().draw();
                    }
                    showSuccessMessage(ttMEdit);
                    setTimeout(function(){
                        if(close){
                            showQuestionLanguageAndPreview(questionRowSelected);
                            closeAnswerInfo(false);
                            setTimeout(function(){ answersTable.columns.adjust(); }, 500);
                        }
                    }, 1000);
                }else{
//                alert(data);
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   reloadAnswersTable
 *  @descr  Function to reload answer's table
 *  @param  idNewQuestion       String          New question's ID
 *  @param  tableData           Array           Answer's table data
 */
function reloadAnswersTable(idNewQuestion, tableData){
    alert("tableAnswer");
    questionsTable.cell(questionsTable.row(questionRowSelected).index(), qtci.questionID).data(idNewQuestion);
    $("body").append($("#newAnswer_PL").parent());          // Move new answer button to save it
    answersTable.destroy(true);
    $("#answersTableContainer").html(tableData);
    initializeAnswersTable_PL();
}
function reloadSubquestionsTable(idNewQuestion, tableData){
    questionsTable.cell(questionsTable.row(questionRowSelected).index(), qtci.questionID).data(idNewQuestion);
    $("body").append($("#newSubquestion_PL").parent());          // Move new answer button to save it
    subquestionsTable.destroy(true);
    $("#subquestionsTableContainer").html(tableData);
    initializeSubquestionsTable_PL();
}