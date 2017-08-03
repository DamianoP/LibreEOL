/**
 * File: AT_HS.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for answer in Multiple Choice questions
 */

$(function(){

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



$(function(){
    var x1 = $("#x1").val();
    var y1 = $("#y1").val();
    var h = $("#h").val();
    var w = $("#w").val();
    $("#myCanvas").css("top", y1 + "px")
        .css("left", x1 + "px")
        .css("width", w + "px")
        .css("height", h + "px");

});



/**
 *  @name   createNewAnswer_HS
 *  @descr  Binded event to create a new Multiple Choice answer
 */
function createNewAnswer_HS(){
    //var score = $("#answerScore").find("dt span span").text();
    var score = 1;
    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
    var translationsA = new Array();
    var x1 = $("#x1").val();
    var y1 = $("#y1").val();
    var x2 = $("#x2").val();
    var y2 = $("#y2").val();
    var risp = x1 + "," + y1 + "," + x2 + "," + y2 ;
    //var risp = [x1, y1, x2, y2];
    //JSON.stringify(risp);
    //alert(idQuestion);
    translationsA = [null, risp , risp];
    $.ajax({
        url     : "index.php?page=question/newanswer",
        type    : "post",
        data    : {
            idQuestion      :   idQuestion,
            translationsA   :   JSON.stringify(translationsA),
            score           :   score,
            type            :   "HS",
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
 *  @name   saveAnswerInfo_HS
 *  @descr  Binded function to save multiple choice answer info
 *  @param  close       Boolean                     Close panel if true
 */
function saveAnswerInfo_HS(close){
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
            type            :   "HS",
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
 *  @name   deleteAnswer_HS
 *  @descr  Binded function to delete a Multiple Choice answer
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function deleteAnswer_HS(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteAnswer, deleteAnswer_HS, false)))){
        var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
        var idAnswer = answersTable.row(answerRowSelected).data()[atci.answerID];
        $.ajax({
            url     : "index.php?page=question/deleteanswer",
            type    : "post",
            data    : {
                idQuestion  :   idQuestion,
                idAnswer    :   idAnswer,
                type        :   "HS"
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
    $("body").append($("#newAnswer_HS").parent());          // Move new answer button to save it
    answersTable.destroy(true);
    $("#answersTableContainer").html(tableData);
    initializeAnswersTable_HS();
}



$(function(){
    var jcrop_api;
    $("#contentContainerCrop").Jcrop({
        onChange:   showCoords,
        onSelect:   showCoords,
        onRelease:  clearCoords
    },function(){
        jcrop_api = this;
    });

    $('#coords').on('change','input',function(e){
        var x1 = $('#x1').val(),
            x2 = $('#x2').val(),
            y1 = $('#y1').val(),
            y2 = $('#y2').val();
        jcrop_api.setSelect([x1,y1,x2,y2]);
    });

});

// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoords(c)
{
    $('#x1').val(c.x);
    $('#y1').val(c.y);
    $('#x2').val(c.x2);
    $('#y2').val(c.y2);
    $('#w').val(c.w);
    $('#h').val(c.h);
};

function clearCoords()
{
    $('#coords input').val('');
};