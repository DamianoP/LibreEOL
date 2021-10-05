/**
 * File: AT_OC.js
 * User: Anis
 * Date: 02/03/2021
 * Desc: Javascript actions for answer in On Click questions
 * */

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
 *  @name   createNewAnswer_OC
 *  @descr  Binded event to create a new On Click answer
 */
function createNewAnswer_OC(){
    var score = $("#answerScore").find("dt span span").text();
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    var translationsA = new Array();
    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val();
    });
    $.ajax({
        url     : "index.php?page=question/newanswer",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "OC",
            mainLang        :   mainLang
        },
        success : function (data) {
//            alert(data);

            data = data.trim().split(ajaxSeparator);
            console.log(data);
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
 *  @name   saveAnswerInfo_OC
 *  @descr  Binded function to save On Click answer info
 *  @param  close       Boolean                     Close panel if true
 */
function saveAnswerInfo_OC(close){
    var score = $("#answerScore").find("dt span span").text();
    var translationsA = new Array();
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    $("textarea[id^=at]").each(function(){
        translationsA[$(this).attr("id").split("at")[1]] = $(this).val();
    });
    $.ajax({
        url     : "index.php?page=question/updateanswerinfo",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            idAnswer        :   answersTable.row(answerRowSelected).data()[atci.answerID],
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "OC",
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
 *  @name   deleteAnswer_OC
 *  @descr  Binded function to delete a On Click answer
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function deleteAnswer_OC(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteAnswer, deleteAnswer_OC, false)))){
        var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
        var idAnswer = answersTable.row(answerRowSelected).data()[atci.answerID];
        $.ajax({
            url     : "index.php?page=question/deleteanswer",
            type    : "post",
            data    : {
                idQuestion  :   idQuestion,
                idAnswer    :   idAnswer,
                type        :   "OC"
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
    questionsTable.cell(questionsTable.row(questionRowSelected).index(), qtci.questionID).data(idNewQuestion);
    $("body").append($("#newAnswer_OC").parent());          // Move new answer button to save it
    answersTable.destroy(true);
    $("#answersTableContainer").html(tableData);
    initializeAnswersTable_OC();
}