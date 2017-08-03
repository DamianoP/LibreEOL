/**
 * File: QT_ES.js
 * User: Masterplan
 * Date: 24/09/14
 * Time: 19:35
 * Desc: Javascript actions for Essay questions
 */

function initialize_ES(){

    createCKEditorInstance("qt"+mainLang);

};

/**
 *  @name   saveQuestionInfo_ES
 *  @descr  Binded function to save Essay question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_ES(close){
    saveQuestionInfo(close);                // Use normal save function
}

/**
 *  @name   createNewQuestion_ES
 *  @descr  Binded event to create a new Essay question
 */
function createNewQuestion_ES(){
    createNewQuestion(reopen = false);                                               // Use normal create function
}

function questionInfoTabChanged(event, ui){
    // Essay question doesn't have answers set
}

function getGivenAnswer_ES(questionDiv){
    return new Array($(questionDiv).find("textarea").val());
}