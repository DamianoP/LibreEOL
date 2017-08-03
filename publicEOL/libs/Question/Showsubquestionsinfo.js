/**
 * File: Showanswerinfo.js
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:34
 * Desc: Show answer's info with all translations and CKEditors
 */

$(function(){

    $("#aLangsTabs a.tab").on("click", function(){changeCKEditorAnswerLanguage(this)});

});

/**
 *  @name   closeAnswerInfo
 *  @descr  Close answer's informations after confirm dialog preventing to loose changes
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function closeAnswerInfo(askConfirmation){
    if((!askConfirmation) || ((!answerEditing) || (confirmDialog(ttWarning, ttCDiscardEdits, closeAnswerInfo, false)))){
        answerEditing = false;
        answerRowSelected = false;
        destroyAllCKEditorInstances();
        closeLightbox($('#answerInfo'));
        $("#questionInfo").slideDown();
    }
}

/**
 *  @name   cancelNewAnswer
 *  @descr  Binded function to cancel new answer process
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function cancelNewAnswer(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNewAnswer, false)))){
        answerEditing = false;
        destroyAllCKEditorInstances();
        closeLightbox($("#answerInfo"));
        $("#questionInfo").slideDown();
    }
}

function changeCKEditorAnswerLanguage(tab){
    var idLanguage = $(tab).attr("value");
    createCKEditorInstance("at"+idLanguage);
    $("#aLangsTabs a.tab").removeClass("active");
    $(tab).addClass("active");
}