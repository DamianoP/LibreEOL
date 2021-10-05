/**
 * File: QT_OC.js
 * User: Anis
 * Date: 02/03/2021
 * Desc: Javascript actions for On Click questions
 */

// Answer Table Column Index

var atci = {
    score : 0,
    text : 1,
    answerID : 2
};

function initialize_OC(){

    createCKEditorInstance("qt"+mainLang);

    /**
     *  @descr  On Click answers DataTables initialization
     */
    initializeAnswersTable_OC();

    /**
     *  @descr  Binded event to create new On Click answer
     */
    $("#newAnswer_OC").on("click", function(event){
        newEmptyAnswer_OC();
    });

};

/**
 *  @name   initializeAnswersTable_OC
 *  @descr  Function to initialize On Click answers table
 */
function initializeAnswersTable_OC(){
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
        showAnswerInfo_OC(new Array($(this).parent(), answerEditing));
    });

    $("#answersTable_filter").css("margin-right", "50px")
        .after($("#newAnswer_OC").parent())
        .before($("#answersTable_info"))
        .hide();

    $("#answersTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHAnswPanel));
}

/**
 *  @name   saveQuestionInfo_OC
 *  @descr  Binded function to save On Click question's info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_OC(close){
    saveQuestionInfo(close)
}

/**
 *  @name   createNewQuestion_OC
 *  @descr  Binded event to create a new On Click question
 */
function createNewQuestion_OC(){
    createNewQuestion(reopen = true);                                               // Use normal create function
}

/**
 *  @name   showAnswerInfo_OC
 *  @descr  Get and display informations and translations for requested On Click answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_OC(selectedAnswerAndConfirm){
    selectedAnswerAndConfirm.push("OC");
    showAnswerInfo(selectedAnswerAndConfirm);
}

/**
 *  @name   newEmptyAnswer_OC
 *  @descr  Ajax request for show empty interface for define a new On Click answer
 */
function newEmptyAnswer_OC() {
    newEmptyAnswer("OC");
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


function main_OC() {
    //initializing variables
    let textInput = ""; //text stored when clicking over an answerOC
    let textInputDrop = ""; //text stored when clicking over a droptarget
    let idDropping = ""; //id of the question from which the clicked item comes from
    let idAnswer = ""; //id of the clicked answer
    let ansArr = document.getElementsByClassName("answerOC"); //array of all the answerOC spans
    let dropsArr = document.getElementsByClassName("droptarget"); //array of all the droptarget spans
    //console.log(ansArr);

    function cleanOC(){
        //reintialize all the variables and remove CSS class activeOC from all the spans
        textInputDrop = textInput = idDropping = idAnswer = "";
        $("span").removeClass("activeOC");
        //loop between answers to hide those who have the same text of any droptarget and make visible the other ones
        for (let j = 0, n = ansArr.length; j < n; ++j) {
            for (let i = 0, n = dropsArr.length; i < n; ++i) {
                if (ansArr[j].getAttribute("value") === dropsArr[i].getAttribute("idAns")) {
                    ansArr[j].style.visibility = "hidden";
                    break;
                } else {
                    ansArr[j].style.visibility = "initial";
                }
            }
        }
    }

    //unbind events on span to avoid errors due to duplicated listeners
    $('span.answerOC').unbind();
    $('span.droptarget').unbind();
    $('img.infoButtonOC').unbind();
    //click listener for answerOC span
    $('span.answerOC').click(function() {
        //check if there is some value stored from another answerOC
        if(textInput == ""){
            //check if there is some value stored from a droptarget that has the same questionID
            if(textInputDrop!=="" && $(this).attr("idQ")==idDropping){
                //swap the text between the answerOC and the droptarget
                textInput = $(this).text();
                let temp = $(this).attr("value");
                for (let i = 0, n = dropsArr.length; i < n; ++i) {
                    if (dropsArr[i].getAttribute("idAns") === idAnswer) {
                        dropsArr[i].innerHTML = textInput;
                        dropsArr[i].setAttribute("idAns", temp);
                    }
                }
                cleanOC();
            }
            else{
                cleanOC();
                //select this answer by storing its text and questionId
                textInput = $(this).text();
                idDropping = $(this).attr("idQ");
                idAnswer = $(this).attr("value");
                //add the CSS class activeOC to this span
                $(this).addClass("activeOC");
            };
        }
        else if(idAnswer == $(this).attr("value")){
            //if this answer is already selected remove the selection
            cleanOC();
        }
        else if(textInput != "" || idDropping != $(this).attr("idQ")){
            //if the user is trying to select this answerOC but he has another one selected
            cleanOC();
            //select this answer by storing its text and questionId
            textInput = $(this).text();
            idDropping = $(this).attr("idQ");
            idAnswer = $(this).attr("value");
            //add the CSS class activeOC to this span
            $(this).addClass("activeOC");
        };
    });

    $('span.answerOC').contextmenu(function() {
        return false;
    });

    $('span.droptarget').click(function() {
        //check if the stored value has the same questionID
        if($(this).attr("value")===(idDropping)) {
            //check if there is a value stored from an answerOC
            if(textInput!=""){
                this.innerHTML = textInput;
                this.setAttribute("idAns",idAnswer);
                cleanOC();
            }
            //check if there is a value stored from a droptarget
            else if(textInputDrop!=""){
                //check if this droptarget is empty
                if($(this).text()==""){
                    //move the text from one droptarget to the other one
                    for (let i = 0, n = dropsArr.length; i < n; ++i) {
                        if (dropsArr[i].getAttribute("idAns") === idAnswer) {
                            dropsArr[i].textContent = "";
                            dropsArr[i].setAttribute("idAns","");
                        }
                    }
                    this.innerHTML = textInputDrop;
                    this.setAttribute("idAns", idAnswer);
                }
                else{
                    //swap the text between two droptargets
                    textInput = $(this).text();
                    let tempId = $(this).attr("idAns");
                    for (let i = 0, n = dropsArr.length; i < n; ++i) {
                        if (dropsArr[i].getAttribute("idAns") === idAnswer){
                            dropsArr[i].textContent = textInput;
                            dropsArr[i].setAttribute("idAns", tempId);
                        }
                    }
                    this.setAttribute("idAns", idAnswer);
                    this.textContent = textInputDrop;
                }
                //reinitialize variables
                cleanOC();
            }
        }
        //user is clicking on a droptarget but has values stored from another question or haven't stored any values
        else{
            //reinitialize variables (in case there are any previous stored values)
            cleanOC();
            //check if the droptarget has text
            if($(this).text() !== ""){
                //store this droptarget value, questionId and add the CSS class activeOC to it
                textInputDrop = $(this).text();
                idDropping = $(this).attr("value");
                idAnswer = $(this).attr("idAns");
                $(this).addClass("activeOC");;
            }
        }
    });

    $('span.droptarget').contextmenu(function() {
        //double click listener to remove the text from the selected droptarget
        this.innerHTML = "";
        this.setAttribute("idAns", "");
        cleanOC();
        return false;
    });
    $('img.infoButtonOC').click(function() {
        var text = ttOCInstructions;
        text = text.replaceAll("<br/>", "\n");
        alert(text);
    });

};

function getGivenAnswer_OC(questionDiv){
    var answer = [];
    $(questionDiv).find("span.droptarget").each(function(index, element){
        answer.push( $(this).attr("idans"));
    });
    return answer;
}