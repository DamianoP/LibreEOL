/**
 * File: Showquestioninfo.js
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:34
 * Desc: Show question's info with all translations
 */

var maximizeFixForAnswer = false;

$(function(){

    /**
     *  @descr  Function to enable QuestionInfo tabs
     */
    //oldIdQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID]

    $("#questionInfoTabs").tabs({activate: function(e, ui) {
                                    questionInfoTabChanged(e, ui);
                                }}).css("border", "none")
                                   .find("ul").css("background", "none")
                                            .css("border", "none");

    $("#questionInfoTabs > div").css("border", "1px solid #686868")
                                .css("border-radius", "5px")
                                .css("margin-top", "7px");

    $("#qLangsTabs a.tab").on("click", function(){changeCKEditorQuestionLanguage(this)});

    $("#question-tab").prepend($("#questionExtra"));

    /**
    *  @descr  Function to enable question tab's dropdownInfo menu effects
    */
    $("#question-tab .dropdownInfo dt.writable").on("click", function() {
                                                        $(this).children("span").toggleClass("clicked");
                                                        $(this).next().children("ol").slideToggle(200);
                                                    });

    /**
     *  @descr  Function to change infos
     */
    $("#question-tab .dropdownInfo dd ol li").on("click", function() {
                                                              questionEditing = true;
                                                              updateDropdown($(this));
                                                          });

    $("#qDescription, input[name=extra]").on("change", function(){questionEditing = true});

    // Close all dropdowns when click out of it
    // Maybe too heavy for system... IMPROVE
    $(document).on('click', function(e) {
                                var $clicked = $(e.target);
                                if (!($clicked.parents().hasClass("dropdownInfo"))){
                                    $(".dropdownInfo dd ol").slideUp(200);
                                    $(".dropdownInfo dt span").removeClass("clicked");
                                }
                            });
});

/**
 *  @name   createNewQuestion
 *  @descr  Binded event to create a new question
 *  @param  reopen          Boolean             If true reopen question info panel
 */
function createNewQuestion(reopen){
    var idTopic = $("#questionTopic dt span.value").text();
    var difficulty = $("#questionDifficulty dt span.value").text();
    var type = $("#questionType").val();
    var translationsQ = new Array();
    $("textarea[id^=qt]").each(function(){
        translationsQ[$(this).attr("id").split("qt")[1]] = $(this).val();
    });
    var description = ($("#qDescription").val().trim() == "")? $("<a>"+$("#qt"+mainLang).val()+"</a>").text() : $("#qDescription").val();
    var extras = $("input[name=extra]").serialize().replace(/extra=/g, "").replace(/&/g, "");

    $.ajax({
        url     : "index.php?page=question/newquestion",
        type    : "post",
        data    : {
            idTopic         :   idTopic,
            difficulty      :   difficulty,
            type            :   type,
            translationsQ   :   JSON.stringify(translationsQ),
            shortText       :   description,
            extras          :   extras,
            mainLang        :   mainLang
        },
        success : function (data) {
            data = data.trim().split(ajaxSeparator);
            if(data.length > 1){
                var questionInfo = JSON.parse(data[1]);
                questionsTable.row.add(questionInfo).draw();
                var newQuestionIndex = questionsTable.rows().eq(0).filter(function(rowIndex){
                    return questionsTable.cell(rowIndex, qtci.questionID).data() == questionInfo[qtci.questionID];
                });
                questionRowSelected = questionsTable.row(newQuestionIndex[0]).node();
                showSuccessMessage(ttMNewQuestion);
                questionEditing = false;
                closeQuestionInfo(false);
                scrollToRow(questionsTable, questionRowSelected);
                if(reopen)
                    setTimeout(function(){ showQuestionInfo(questionRowSelected) }, 500);       // Auto reopen to create answer set
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
 *  @name   saveQuestionInfo
 *  @descr  Binded function to save question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo(close){
    var idTopic = $("#questionTopic").find("dt span span").text();
    var difficulty = $("#questionDifficulty").find("dt span span").text();
    var type = $("#questionType").val();
    var translationsQ = new Array();
    $("textarea[id^=qt]").each(function(){
        translationsQ[$(this).attr("id").split("qt")[1]] = $(this).val();
    });
    var description = ($("#qDescription").val().trim() == "")? $("<a>"+$("#qt"+mainLang).val()+"</a>").text() : $("#qDescription").val();
    var extras = $("input[name=extra]").serialize().replace(/extra=/g, "").replace(/&/g, "");
    $.ajax({
        url     : "index.php?page=question/updatequestioninfo",
        type    : "post",
        data    : {
            idQuestion      :   questionsTable.row(questionRowSelected).data()[qtci.questionID],
            idTopic         :   idTopic,
            type            :   type,
            difficulty      :   difficulty,
            translationsQ   :   JSON.stringify(translationsQ),
            shortText       :   description,
            extras          :   extras,
            mainLang        :   mainLang
        },
        success : function (data) {
            data = data.trim().split(ajaxSeparator);
            if(data.length > 1){
                questionsTable.row(questionRowSelected).data(JSON.parse(data[1]));
                questionsTable.draw();
                showSuccessMessage(ttMEdit);
                questionEditing = false;
                setTimeout(function(){
                    if(close){
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeQuestionInfo(false);
                    }
                }, 1000);
                scrollToRow(questionsTable, questionRowSelected);
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
 *  @name   deleteQuestion
 *  @descr  Binded function to delete question
 *  @param  askConfirmation     Boolean     If true ask confirmation
 */
function deleteQuestion(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteQuestion, deleteQuestion, false))){
        var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
        $.ajax({
            url     : "index.php?page=question/deletequestion",
            type    : "post",
            data    : {
                idQuestion      :   idQuestion
            },
            success : function (data) {
                if(data == "ACK"){
                    questionsTable.row(questionRowSelected).remove().draw();
                    questionRowSelected = null;
                    showSuccessMessage(ttMQuestionDeleted);
                    setTimeout(function(){
                        if(close){
                            closeQuestionLanguagePanel();
                            closeQuestionPreviewPanel();
                            closeQuestionInfo(false);
                        }
                    }, 1000);
                }else{
//                    alert(data);
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
 *  @name   newEmptyAnswer
 *  @descr  Ajax request for show empty interface for define a new answer
 *  @param  type        String      Question's type
 */
function newEmptyAnswer(type) {
    $.ajax({
        url     : "index.php?page=question/showanswerinfo",
        type    : "post",
        data    : {
            action      :   "new",
            idQuestion  :   questionsTable.row(questionRowSelected).data()[qtci.questionID],
            type        :   type,
            idAnswer    :   "none",
            mainLang    :   mainLang
        },
        success : function (data) {
//            alert(data);
            if($(data)){
                $("body").append(data);
                $("#questionInfo").slideUp();
                newLightbox($("#answerInfo"), {});
                maximizeFixForAnswer = true;
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
 *  @name   showAnswerInfo
 *  @descr  Get and display informations and translations for requested answer
 *  @param  selectedAnswerConfirmAndType        Array       [Selected answer <tr>, Confirmation, Question's Type]
 */
function showAnswerInfo(selectedAnswerConfirmAndType){
    clearTimeout(timer);
    var selectedAnswer = selectedAnswerConfirmAndType[0];
    var askConfirmation = selectedAnswerConfirmAndType[1];
    var type = selectedAnswerConfirmAndType[2];
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, showAnswerInfo, new Array(selectedAnswer, false, type)))){
        answerRowSelected = $(selectedAnswer);
        $.ajax({
            url     : "index.php?page=question/showanswerinfo",
            type    : "post",
            data    : {
                action      :   "show",
                idQuestion  :   questionsTable.row(questionRowSelected).data()[qtci.questionID],
                type        :   type,
                idAnswer    :   answersTable.row(answerRowSelected).data()[atci.answerID],
                mainLang    :   mainLang
            },
            success : function (data) {
//                alert(data);
                $("body").append(data);
                $("#questionInfo").slideUp();
                newLightbox($("#answerInfo"), {});
                maximizeFixForAnswer = true;
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   closeQuestionInfo
 *  @descr  Close question's informations after confirm dialog preventing to loose changes
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function closeQuestionInfo(askConfirmation){
    if((!askConfirmation) || ((!questionEditing) || (confirmDialog(ttWarning, ttCDiscardEdits, closeQuestionInfo, false)))){
        questionEditing = false;
        destroyAllCKEditorInstances();
        closeLightbox($('#questionInfo'));
        closeQuestionTypeSelect();

        // action when question's window go down
        newIdQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
        //window.alert(newIdQuestion);

    }
}
function newEmptySubquestion(type) {


    $.ajax({
        url : "index.php?page=question/showsubquestionsinfo",
        type : "post",
        data : {
            action : "new",
            idQuestion : questionsTable.row(questionRowSelected).data()[qtci.questionID],
            type : type,
            sub_questions : "none",
            mainLang : mainLang
        },

        success : function (data) {
            //  alert(data);
//alert("ciao");
//alert(data);
            if($(data)){

                $("body").append(data);
                $("#subquestionInfo").slideUp();
                newLightbox($("#answerInfo"), {});
            }else{
// alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
function showSubquestionsInfo(selectedAnswerConfirmAndType){
    clearTimeout(timer);
    var selectedAnswer = selectedAnswerConfirmAndType[0];
    var askConfirmation = selectedAnswerConfirmAndType[1];
    var type = selectedAnswerConfirmAndType[2];
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, showAnswerInfo, new Array(selectedAnswer, false, type)))){
        answerRowSelected = $(selectedAnswer);
        $.ajax({
            url     : "index.php?page=question/showsubquestionsinfo",
            type    : "post",
            data    : {
                action      :   "show",
                idQuestion  :   questionsTable.row(questionRowSelected).data()[qtci.questionID],
                type        :   type,
                //sub_questions    :   answersTable.row(answerRowSelected).data()[atci.answerID],
                sub_questions    :   subquestionsTable.row(answerRowSelected).data()[ztci.subID],
                mainLang    :   mainLang
            },
            success : function (data) {
//                alert(data);
                $("body").append(data);
                $("#questionInfo").slideUp();
                newLightbox($("#answerInfo"), {});
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
        console.log(selectedAnswer);
        console.log(askConfirmation);
        console.log(type);
        console.log(questionsTable.row(questionRowSelected).data()[qtci.questionID]);
        console.log(subquestionsTable.row(answerRowSelected).data()[ztci.subID]);



    }
}


/**
 *  @name   cancelNewQuestion
 *  @descr  Closes new question's information after confirm dialog
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function cancelNewQuestion(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNewQuestion, false)))){
        questionEditing = false;
        closeQuestionInfo(false);
        closeQuestionTypeSelect();
    }
}

function changeCKEditorQuestionLanguage(tab){
    var idLanguage = $(tab).attr("value");
    createCKEditorInstance("qt"+idLanguage);
    $("#qLangsTabs a.tab").removeClass("active");
    $(tab).addClass("active");
}

function fixMaximize(){
    if(maximizeFixForAnswer){           // Answer editing   =>  Fix for div#answerInfo
        $("#answerInfo").addClass("maximize_fix");
    }else{                              // Question editing   =>  Fix for div#questionInfo
        $("#questionInfo").addClass("maximize_fix");
    }
}
