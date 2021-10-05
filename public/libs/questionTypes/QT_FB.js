/**
 * Created by Francesco Giancristofaro
 */

/*************************************
 *            Utilities              *
 *************************************/

/**
 *  @name   htmlDecode
 *  @descr  function to decode input string with html entities in simple text
 *  @param  input       String         The string to decode
 */
function htmlDecode(input) {
    let e = document.createElement('textarea');
    e.innerHTML = input;
    // handle case of empty input
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

/**
 *  @name   htmlEntities
 *  @descr  function to encode input string in html entities
 *  @param  str       String         The string to encode
 */
function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/,/g, '&#44;');
}

/**
 *  @name   helpjs
 *  @descr  function to show help panel for answer FB interface
 */
function helpjs() {

    $("#dialogError p").html("<span>"+ttHelpPanelFB+"</span><hr><span>"+ttHelpPanelFB1+"</span>");
    $("#dialogError").dialog("option", "title", ttHelpDefault)
        .dialog("open");
    $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}

/***********************************************************************************************************************************************************************************************************/

/**
 *  @name   initialize_FB
 *  @descr  function called when double-click on FB question row for edit it
 */
function initialize_FB() {

    createCKEditorInstance_FB("qt" + mainLang);

    initInterface_FB();

}


/*************************************
 *          Interface functions       *
 *************************************/


/**
 *  @name   initInterface_FB
 *  @descr  initialize behaviour of every element in FB interface
 */
function initInterface_FB() {
    let mainLang = $("#qLangsTabs > :first-child").attr("value");

    //show help panel
    $(".help_FB").click(function () {
        helpjs();
    });

    //behaviour of language tabs
    $("#qLangsTabs > a").click(function () {

            $(this).css("pointer-events", "none");

            //hide other content table
            let value = "#tbodyAnswer" + $(this).attr("value");
            $(".tbl-contentFB").attr("style", "display:none");

            //show this language content table
            $(value).attr("style", "display:block");
            $("#qLangsTabs > a").removeClass("disabled").css("pointer-events", "auto");


            //if there isn't a content table answer for a language display proper message
            if (!($("#tbodyAnswer" + $(this).attr("value")).length) && ($(".infoAnswerFB").length<1)) {
                let text = "<div  data-lang='" + $(this).attr("value") + "' class='infoAnswerFB '><br><br><br><span>"+ttEmptyLanguageFB+"</span></div>"
                $("#headerTable").after(text);
            }
            // otherwise remove the message
            else if ($("#tbodyAnswer" + $(this).attr("value")).length) {
                $(".infoAnswerFB").remove();
            }
        });

    //when the select menu for answers change(an answer is added or removed) make the update button clickable or not
    $("select").each(function () {
        $(this).on("change", function () {
            let id = "input[data-id=" + $(this).attr("data-id") + "]";
            if ($(this).val() !== "hide") {
                $(id).prop('disabled', false);
                $(id).css("pointer-events", "auto");
            } else {
                $(id).prop('disabled', true);
            }
        });
    });

    //init same behaviour for update button when the page load
    $(".listAnswerFB").each(function () {
        if ($(this).find("option:selected").attr("value") !== "hide")
            $(this).next().prop("disabled", false).css("pointer-events", "auto");
    });


    //OPEN NEW ANSWER BOX
    $(".newAnswer_FB").click(function () {
        let id = $(this).attr("data-id");
        let lang = $(this).attr("data-lang");
        openBoxFB(id,"",lang,"Nuova risposta","addNewAnswer");
    });

    //OPEN UPDATE ANSWER BOX
    $(".updateAnswerFB").click(function () {
        let id = $(this).attr("data-id");
        let lang = $(this).attr("data-lang");

        let contentAnswer = $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").val();
        openBoxFB(id, contentAnswer, lang,"Modifica risposta","updateAnswer");
    });

    //ADD SAVE BUTTON LIKE THAT IN QUESTIONS' INTERFACE IN THE ANSWERS' INTERFACE
    $('<a class="button blue right lSpace tSpace" onclick="saveQuestionInfo_FB(close = true);">'+ttSave+'</a>').insertAfter($('#answers').find(".button,.normal,.left,.rSpace,.tSpace"));

}


// Handle click on tabs Question/Answer
function questionInfoTabChanged(event, ui) {
    if (ui.newTab.index() === 0) {             // Question tab selected
        let lang = $("#qLangsTabs a.tab.active").attr("value");
        $(this).css("pointer-events", "none");
        $("#answersTab").css("pointer-events", "auto");
        $("#qLangsTabs").find("a[value=" + lang + "]").addClass("active").addClass("disabled").click();

        if (!(CKEDITOR.instances["qt" + lang]))
            createCKEditorInstance("qt" + lang);
    } else if (ui.newTab.index() === 1) {
        let lang = $("#qLangsTabs a.tab.active").attr("value");
        $(this).css("pointer-events", "none");
        $("#questionTab").css("pointer-events", "auto");
        $("#answersTablecountinginerFB").find("#qLangsTabs").find("a[value=" + lang + "]").addClass("active").addClass("disabled").click();
    }
}




/**
 *  @name   showAnswerInfo_FB
 *  @descr  Get and display informations and translations for requested FB answer
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <tr>, Confirmation]
 */
function showAnswerInfo_FB(selectedAnswerAndConfirm) {
    selectedAnswerAndConfirm.push("FB");
    showAnswerInfo(selectedAnswerAndConfirm);
}

// Function called when a question is submitted from a test. The output is an object with a pair id fb item/student's answer
// and it is required to getScoreForGivenAnswer() in QT_FB.php to know student's given answers.
function getGivenAnswer_FB(questionDiv) {
    let answer = [];
    $(questionDiv).find("input:text").each(function (index, input) {
        let id;
        if ($(input).attr("data-id") != undefined)
             id = parseInt($(input).attr("data-id").slice(9, -9), 10);
        else
             id=null;
        answer.push({"id":id,"value":$(input).val()});
    });
    return answer;
}

        /*************************************
         *               Box                 *
         *************************************/



function openBoxFB(id, content="", lang,title="",type="") {
    //function for exit from this box
    let exit = function () {
        $("#boxFB" + id).slideUp();
        $("#questionInfo").css("pointer-events", "auto");
        $(".newAnswer_FB").css("pointer-events", "auto");
        $(".updateAnswerFB").css("pointer-events", "auto");
        setTimeout(function () {
            $('#boxFB' + id).next('div').remove();
            $("#boxFB" + id).remove();
        }, 400);
    }
    //create box
    let box = "<div class='boxCenterS' style='width: 500px; margin-top: 125px;cursor: move' id='boxFB" + id + "'><div class='box'>" +
        "<div class='boxTopCenter' style='background-color: #F49D26'>" + title + "<span><img class='update_FB' src='themes/default/images/back-arrow.png' data-id='" + id + "' alt='close'></span></div>" +
        "<div class='smallButtons'></div>" +
        "</div>" +
        "<div class='boxContent' style='text-align: center'><br><br><textarea style='font-size: 18px;text-align: center;resize: none' name='boxContentFB" + id + "' id='boxContentFB" + id + "'>" + htmlEntities(content) + "</textarea><br><br>" +
        "</div>" +
        "<div class='boxBottom'>" +
        " <div class='boxBottomCenter'>";
        if(type==="addNewAnswer"){
            box+="<button class='saveBoxAnswerFB' data-id='" + id + "'>" + ttAdd + "</button>";
        }else if (type==="updateAnswer"){
            box+="<button class='deleteAnswer' data-id='" + id + "'>" + ttDelete + "</button>" +
                "<button style='float: right' class='saveBoxAnswerFB' data-id='" + id + "'>" + ttEdit + "</button>";
        }
        box+=   "</div>" +"</div>" +"</div>";

    $("body").append(box);
    $("#questionInfo").css("pointer-events", "none");
    $(".newAnswer_FB").css("pointer-events", "none");
    $(".updateAnswerFB").css("pointer-events", "none");
    newLightbox($("#boxFB" + id), {});
    maximizeFixForAnswer = true;

    //set focus
    setTimeout(function () {
        if (type==="addNewAnswer")
        $("#boxContentFB" + id).focus();
        $("#boxFB" + id).draggable()
    }, 300);

        //save button
        $(".saveBoxAnswerFB").click(function () {
            //clean the string
            let contentAnswer = $('#boxContentFB' + id).val();
            contentAnswer = htmlDecode(contentAnswer).replace(/[\n\r]/g, ' ').replace(/\s\s+/g, ' ').trim();

            //check consistency, save empty answer or that already exists returns an error
            if (contentAnswer === "") {
                showErrorMessage(ttErrorEmptyAnswer);
            } else if (contentAnswer === $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").val()) {
                exit();
            } else if (contentAnswer.length > 200) {
                showErrorMessage(ttErrorMaxLength);
            } else {
                let alreadyExist = false;
                $("select[data-id=" + id + "][data-lang=" + lang + "] option").each(function () {
                    if ($(this).attr("value") !== "hide" && $(this).val() === contentAnswer)
                        alreadyExist = true;
                });
                if (alreadyExist) {
                    showErrorMessage(ttErrorAlreadyExists);
                } else {
                    if (type==="updateAnswer"){
                    //update answer
                    questionEditing = true;
                    $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").attr("value", contentAnswer.trim());
                    if (contentAnswer.length <= 40) {
                        $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").text(contentAnswer.trim());
                    } else {
                        $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").text(contentAnswer.trim().substring(0, 26) + "..");
                    }}
                    else if (type==="addNewAnswer"){
                        if (htmlDecode(contentAnswer).trim().length <= 32) {
                            $('select[data-id=' + id + '][data-lang=' + lang + ']')
                                .append($("<option></option>")
                                    .attr("value", htmlDecode(contentAnswer).trim())
                                    .prop("selected", true)
                                    .text(htmlDecode(contentAnswer).trim()));
                        } else {
                            $('select[data-id=' + id + '][data-lang=' + lang + ']')
                                .append($("<option></option>")
                                    .attr("value", htmlDecode(contentAnswer).trim())
                                    .prop("selected", true)
                                    .text(htmlDecode(contentAnswer).trim().substring(0, 29) + ".."));
                        }
                        if ($("select[data-id=" + id + "][data-lang=" + lang + "] option").length > 1) {
                            $(".updateAnswerFB[data-id=" + id + "][data-lang=" + lang + "]").prop("disabled", false);
                        }
                    }
                    exit();
                }
            }

        });

    //back button
    $(".update_FB").click(function () {
        let contentAnswer = $('#boxContentFB' + id).val();
        contentAnswer = contentAnswer.replace(/[\n\r]/g, ' ');
        if (contentAnswer === "" || contentAnswer.trim() === htmlDecode($("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").val()).trim()) {
            exit();
        } else {
            confirmDialog(ttConfirm, ttCDiscardEdits, function () {
                exit();
            }, false);
        }
    });

    //if delete answer button is present handle its behaviour
    if ($(".deleteAnswer").length){
        $(".deleteAnswer").click(function () {
            confirmDialog(ttDelete, ttDeleteAnswerFB, function () {
                $("select[data-id=" + id + "][data-lang=" + lang + "] option:selected").remove();
                if ($("select[data-id=" + id + "][data-lang=" + lang + "] option").length === 1 && $("select[data-id=" + id + "][data-lang=" + lang + "] option").attr("value") === "hide") {
                    $("select[data-id=" + id + "][data-lang=" + lang + "] option").prop("selected", true);
                    $(".updateAnswerFB[data-id=" + id + "][data-lang=" + lang + "]").prop("disabled", true);
                }
                exit();
            }, false);
        });
    }
}


/*************************************
 *           Save functions          *
 *************************************/
/**
 *  @name   createNewQuestion_FB
 *  @descr  Binded event to create a new fill in blanks question and a set of answers' containers in database
 */
function createNewQuestion_FB() {

    //get question's data from DOM
    let checkTextareas = new Array();
    let idTopic = $("#questionTopic dt span.value").text();
    let difficulty = $("#questionDifficulty dt span.value").text();
    let type = $("#questionType").val();
    let description = ($("#qDescription").val().trim() === "") ? $("<a>" + $("#qt" + mainLang).val() + "</a>").text() : $("#qDescription").val();
    let extras = $("input[name=extra]").serialize().replace(/extra=/g, "").replace(/&/g, "");
    let translationsQ = new Array();
    $("textarea[id^=qt]").each(function () {
        checkTextareas.push($(this).val());
    });

    //CHECK CONSISTENCY OF TRANSLATIONS
    let reg = /<input data-id=.@==@=@==@/g;
    let checklength = checkTextareas.length;

    //if main lang doesn't contain fill in the blank items
    if (checkTextareas[0].match(reg) == null) {
        showErrorMessage(ttAlertFB);
        return;
    }
    //check that all the translations not empty has the same number of FB items
    for (let i = 1; i < checklength; i++) {
        if (checkTextareas[i] !== "") {
            if (checkTextareas[i].match(reg) == null) {
                showErrorMessage(ttAlert1FB);
                return;
            } else {
                if (checkTextareas[i].match(reg).length !== checkTextareas[i - 1].match(reg).length) {
                    showErrorMessage(ttAlert1FB);
                    return;
                }
            }
        }
    }
    //...if all goes right

    /*CKeditor FB plugin put FB item with different id's SO I HAVE TO COPY THE VALUES OF ID'S IN MAIN LANGUAGE TO ALL OTHER TRANSLATIONS because a row in answer table database
    have field "score"=(iditem * scoreright) and all traslations must have same score.
    */
    let matching = checkTextareas[0].match(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g);
    $("textarea[id^=qt]").each(function () {
        let text = $(this).val();
        let i = 0;
        text = text.replace(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g, function ($0) {
            if (i === matching.length) i = 0;
            return matching[i++];
        });
        $(this).val(text);
    });

    //modify real content of textarea because i have changed id's of translations
    $("textarea[id^=qt]").each(function () {
        translationsQ[$(this).attr("id").split("qt")[1]] = $(this).val();
    });

    $.ajax({
        url: "index.php?page=question/newquestion",
        type: "post",
        data: {
            idTopic: idTopic,
            difficulty: difficulty,
            type: type,
            translationsQ: JSON.stringify(translationsQ),
            shortText: description,
            extras: extras,
            mainLang: mainLang
        },
        success: function (data) {
            data = data.trim().split(ajaxSeparator);
            if (data.length > 1) {
                let questionInfo = JSON.parse(data[1]);
                questionsTable.row.add(questionInfo).draw();
                let newQuestionIndex = questionsTable.rows().eq(0).filter(function (rowIndex) {
                    return questionsTable.cell(rowIndex, qtci.questionID).data() === questionInfo[qtci.questionID];
                });
                questionRowSelected = questionsTable.row(newQuestionIndex[0]).node();
                showSuccessMessage(ttMNewQuestion);
                questionEditing = false;
                closeQuestionInfo(false);
                scrollToRow(questionsTable, questionRowSelected);
                setTimeout(function () {
                    showQuestionInfo(questionRowSelected)
                }, 500);

                //trigger this function only for question type FB, set default translations for empty answers' container and create them
                if (matching !== undefined) {
                    let translations = new Array();
                    translationsQ.forEach(function (value, index, array) {
                        if (array[index] !== "" && array[index] != null && array[index] !== undefined) {
                            translations[index] = "=@a=@b=empty=@a=@b=";
                        }
                    });
                    newEmptyAnswerFB(matching, questionInfo[qtci.questionID], translations, (1 / matching.length).toFixed(2));
                }
            } else {
                showErrorMessage(data);
            }
        },
        error: function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}


/**
 *  @name   saveQuestionInfo_FB
 *  @descr  Binded function to CHECK CONSISTENCY and get correct data for init methods for save fill in blanks info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo_FB(close) {
    $("#questionInfoForm").css("pointer-events", "none");

    //Take data from DOM
    let idTopic = $("#questionTopic").find("dt span span").text();
    let difficulty = $("#questionDifficulty").find("dt span span").text();
    let type = $("#questionType").val();
    let description = ($("#qDescription").val().trim() === "") ? $("<a>" + $("#qt" + mainLang).val() + "</a>").text() : $("#qDescription").val();
    let extras = $("input[name=extra]").serialize().replace(/extra=/g, "").replace(/&/g, "");
    let translationsQ = new Array();
    let checkTextareas = new Array();

    //Fill arrays with content of textareas which represent questions with their translations
    $("textarea[id^=qt]").each(function () {
        checkTextareas.push($(this).val());
        translationsQ[$(this).attr("id").split("qt")[1]] = $(this).val();
    });

    //Check consistency of questions with their traslations
    let reg = /<input data-id=.@==@=@==@/g;
    let checklength = checkTextareas.length;
    //if null
    if (checkTextareas[0].match(reg) == null) {
        showErrorMessage(ttAlertFB);
        return;
    }

    //If translations don't have the same number of FB item returns.
    for (let i = 1; i < checklength; i++) {
        if (checkTextareas[i] !== "") {
            if (checkTextareas[i].match(reg) == null) {
                showErrorMessage(ttAlert1FB);
                $("#questionInfoForm").css("pointer-events", "auto");

                return;
            } else {
                if (checkTextareas[i].match(reg).length !== checkTextareas[i - 1].match(reg).length) {
                    showErrorMessage(ttAlert1FB);
                    $("#questionInfoForm").css("pointer-events", "auto");
                    return;
                }
            }
        }
    }

    /*CKeditor FB plugin put FB item with different id's SO I HAVE TO COPY THE VALUES OF ID'S IN MAIN LANGUAGE TO ALL OTHER TRANSLATIONS because a row in answer table database
    have field "score"=(iditem * scoreright) and all traslations must have same score.
    */

    //Taking the id's of FB items in main language
    let matching;
    if (translationsQ[mainLang] !== undefined && translationsQ[mainLang] != null && translationsQ[mainLang] !== "") {
        matching = translationsQ[mainLang].match(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g);
    }
    //IMPORTANTE: vedere se aggiungere un controllo sulla coppia [id,lingua] da resettare i parametri nel caso ci sia stata una modifica nelle caselline d una particolare traduzione
    // al momento i dati relativi a quegli id(della lingua che ho cambiato) rimarranno invariati poichè quegli id torneranno ad essere uguali a quelli della mainlang e quindi già presenti nel db
    // e quindi i dati rimarranno i vecchi (risposte suggerimento,ecc), insomma nel database le traduzioni cancellate non verranno cancellate al primo salvataggio ma a quello successivo
    //SOLUZIONE=controllare se l'id che sto cambiando è diverso da quello che sto andando ad inserire, in quel caso vuol dire che bisogna resettare la traduzione in questione

    //Copy id's in all other languages
    translationsQ.forEach(function (part, index, arr) {
        if (arr[index] !== undefined) {
            let i = 0;
            arr[index] = arr[index].replace(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g, function ($0) {
                if (i === matching.length) i = 0;
                return matching[i++];
            });
        }
    });

    updateSetQuestionAnswer_FB(translationsQ, questionsTable.row(questionRowSelected).data()[qtci.questionID], idTopic, type, difficulty, description, extras);

}

/**
 *  @name   updateSetQuestionAnswer_FB
 *  @descr  Binded function to choise which FB item i have to create, delete and update. At last call the update of question too.
 *    @param  all data required to update question info
 */
function updateSetQuestionAnswer_FB(translationsQ, fkRootQuestion, idTopic, type, difficulty, shortText, extras) {

    //get the current state of id's present at save
    let idSet = new Array();
    translationsQ.forEach(function (value, index, array) {
        if (array[index] !== "" && array[index] !== undefined) {
            idSet[index] = array[index].match(/(?<=\<input data-id=.@==@=@==@)(.*?)(?=@==@=@==@. name=.fb_item. type=.text. \/>)/g);
        }
    });

    let idToCreate = [];
    let idToDelete = [];

    //get the initial set of id's at opening of question
    let initialSetId = JSON.parse($(".idAtInitFB").val());

    //make comparison between two sets( initials id's - actual id's) and get the id's that i have to delete
    initialSetId.forEach(function (value, index, array) {
        if (index > 0 && array[index] != null && array[index] !== "") {
            array[index].forEach(function (value1, index1, array1) {
                if (array1[index1] != null && array1[index1] !== "" && !idToDelete.includes(array1[index1])) {
                    if (!idSet[mainLang].includes(array1[index1])) {
                        idToDelete.push(array1[index1]);
                    }
                }
            });
        }
    });

    //make the reverse comparison to detect id's that i have to create
    idSet[mainLang].forEach(function (value, index, array) {
        if (!initialSetId[mainLang].includes(array[index]))
            idToCreate.push(array[index]);
    });

    //set default translations of new entries
    let translations = [];
    translationsQ.forEach(function (value, index, array) {
        if (array[index] !== "" && array[index] != null && array[index] !== undefined) {
            translations[index] = "=@a=@b=empty=@a=@b=";
        }
    });

    //in case of deleting answers i open a dialog for warn user, then doing same operations( call function for: create new answer , update existing , update question) and delete answer if required
    if (idToDelete.length) {
        confirmDialog(ttWarning, ttConfirmDeleteFBItem, function () {

            //create this set of id's
            newEmptyAnswerFB(idToCreate, fkRootQuestion, translations, (1 / idSet[mainLang].length).toFixed(2));

            //UPDATE REMAINING ANSWERS (NOT NEW ID'S)
            idSet[mainLang].forEach((value, index, array) => {
                if (!idToDelete.includes(array[index]) && !idToCreate.includes(array[index])) {
                    updateSingleAnswerFB(array[index], fkRootQuestion, idSet);
                }
            });
            //update question
            updateQuestionFB(fkRootQuestion, idTopic, type, difficulty, translationsQ, shortText, extras);

            //delete answer
            idToDelete.forEach((value, index, array) => {
                deleteAnswer_FB(fkRootQuestion, array[index]);
            });

        }, false);
        setTimeout(function () {
            $("div.ui-dialog-buttonset button:contains('No')").click(function () {
                $("#questionInfoForm").css("pointer-events", "auto");
            });

            $(".ui-dialog-titlebar-close").click(function () {
                $("#questionInfoForm").css("pointer-events", "auto");
            });
        }, 1000);
    } else {
        newEmptyAnswerFB(idToCreate, fkRootQuestion, translations, (1 / idSet[mainLang].length).toFixed(2));

        updateQuestionFB(fkRootQuestion, idTopic, type, difficulty, translationsQ, shortText, extras);

        //UPDATE REMAINING ANSWERS (NOT NEW ID'S)
        idSet[mainLang].forEach((value, index, array) => {
            if (!idToDelete.includes(array[index]) && !idToCreate.includes(array[index])) {
                updateSingleAnswerFB(array[index], fkRootQuestion, idSet);
            }
        });
    }
}


/**
 *  @name   updateQuestionFB
 *  @descr  Binded function to update question FB
 *    @param  all data required to update question info
 */
function updateQuestionFB(idQuestion, idTopic, type, difficulty, translationsQ, shortText, extras) {
    $.ajax({
        url: "index.php?page=question/updatequestioninfo",
        type: "post",
        data: {
            idQuestion: idQuestion,
            idTopic: idTopic,
            type: type,
            difficulty: difficulty,
            translationsQ: JSON.stringify(translationsQ),
            shortText: shortText,
            extras: extras,
            mainLang: mainLang
        },
        success: function (data) {
            data = data.trim().split(ajaxSeparator);
            if (data.length > 1) {
                questionsTable.row(questionRowSelected).data(JSON.parse(data[1]));
                questionsTable.draw();
                questionEditing = false;
                setTimeout(function () {
                    if (close) {
                        showQuestionLanguageAndPreview(questionRowSelected);
                        closeQuestionInfo(false);
                        $("#questionInfoForm").css("pointer-events", "auto");
                    }
                }, 1000);
                scrollToRow(questionsTable, questionRowSelected);
                showSuccessMessage(ttUpdateQuestionFB);


            } else {
//                alert(data);
                showErrorMessage(data);
            }
        },
        error: function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}


/**
 *  @name   deleteAnswer_FB
 *  @descr  Binded function to delete a FB answer
 *  @param  idQuestion String   fkrootquestion of answer
 *  @param  idItemFB String        the score  that identify answer to delete with id of item
 */
function deleteAnswer_FB(idQuestion, idItemFB) {

    $.ajax({
        url: "index.php?page=question/deleteanswer",
        type: "post",
        data: {
            idQuestion: idQuestion,
            idAnswer: $("input.idAnswer[data-id=" + idItemFB + "][data-lang=" + mainLang + "]").val(),
            type: "FB"
        },
        success: function (data) {
        },
        error: function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   updateSingleAnswerFB
 *  @descr  Binded function to update a single item FB answers container
 *    @param  idItem   id of item
 *    @param fkRootQuestion   id question
 *    @param idSet    actual set of id's
 */

function updateSingleAnswerFB(idItem, fkRootQuestion, idSet) {

    let rowAnswerLengthLang = [];
    rowAnswerLengthLang.push("0");
    $(".tbl-contentFB").each(function () {
        let idLang = $(this).attr("id").replace("tbodyAnswer", "");
        rowAnswerLengthLang[idLang] = $("#tbodyAnswer" + idLang).find(".rowAnswer").length;
    });

    //SET SCORE FIELD
    let scoreContent = idItem + "*";

    //add score in case of right answer
        scoreContent += ((1 / idSet[mainLang].length).toFixed(3)).toString().substring(0,4);


    // traslationsA= array with language as index and content=accepted answers
    let translationsA = [];
    let length = rowAnswerLengthLang.length;
    for (let i = 1; i < length; i++) {

        if (rowAnswerLengthLang[i]) {
            //set answer accepted
            translationsA[i] = "";
            $(".answersMenuFB select[data-lang=" + i + "][data-id=" + idItem + "]").find("option").each(function () {
                if ($(this).attr("value") !== "hide") {
                    translationsA[i] += htmlEntities($(this).attr("value")) + "==@@=@@==";
                }
            });
            if (translationsA[i] === "") {
                translationsA[i] = "=@a=@b=empty=@a=@b=";
            } else {
                let index = translationsA[i].lastIndexOf("==@@=@@==");
                if (index !== -1) {
                    translationsA[i] = translationsA[i].substr(0, index);
                    translationsA[i] += "";
                }
            }

        } else if (idSet[i]) {
            translationsA[i] = "=@a=@b=empty=@a=@b=";
        }
    }

    $.ajax({
        url: "index.php?page=question/updateanswerinfo",
        type: "post",
        data: {
            idQuestion: fkRootQuestion,
            idAnswer: $("input.idAnswer[data-id=" + idItem + "][data-lang=" + mainLang + "]").val(),
            translationsA: JSON.stringify(translationsA),
            score: scoreContent,
            type: "FB",
            mainLang: mainLang
        },
        success: function (data) {
            data = data.trim().split(ajaxSeparator);
            if (data[0] === "ACK") {

            } else {
                showErrorMessage(data);
            }
        },
        error: function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}


/**
 *  @name   newEmptyAnswerFB
 *  @descr  Binded function to create new answers of FB question as a first empty container when user create a new question
 *  @param  matching   Array    array of id for create empty answer FB
 *  @param  idQuestion   string    id question of the answers that i have to create
 *  @param  translations   Array     traslations to create
 *  @param  score   string     string containing the effective right score
 */

function newEmptyAnswerFB(matching, idQuestion, translations, score) {
    let length = matching.length;

    //create new answer for every id found in question
    for (let i = 0; i < length; i++) {
        $.ajax({
            url: "index.php?page=question/newanswer",
            type: "post",
            data: {
                idQuestion: idQuestion,
                score: matching[i] + "*" + score,
                type: "FB",
                translationsA: JSON.stringify(translations),
                mainLang: mainLang
            },
            success: function (data) {
                data = data.trim().split(ajaxSeparator);
                if (data[0] === "ACK") {

                } else {
                    showErrorMessage(data);
                }
            },
            error: function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }

}
