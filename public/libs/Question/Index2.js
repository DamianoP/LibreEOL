/**
 * File: Index.js
 * User: Masterplan
 * Date: 4/9/13
 * Time: 7:33 PM
 * Desc: Shows topics, questions and answers info and preview
 */

// Question Table Column Index
var qtci = {
    status : 0,
    text : 1,
    languages : 2,
    topic : 3,
    type : 4,
    difficulty : 5,
    questionID : 6,
    topicID : 7,
    typeID : 8,
    languageID : 9
};

// Time for double click
var timer;

// Anchors for topic list
var topicRowSelected = null;
var topicRowEdit = null;
var topicEditing = false;

// Anchors for question table
var questionsTable = null;
var questionRowSelected = null;
var questionEditing = false;

// Anchor for answers list
var answersTable = null;
var answerRowSelected = null;
var answerEditing = false;

// Anchors for languages list
var languageRowSelected = null;

$(function(){
    $("#newQuestionTypeSelect").hide();
    $("#questionPreview .boxContent").hide();
    $("#languageList .boxContent").hide();

    /**
     *  @descr  Binded event to create new topic
     */
    $("#newTopic").html('');
    /**
     *  @descr  Binded event to create new question
     */
    $("#newQuestion").on("click", function(event){
        if($(".filterQuestion").length > 1)
            newLightbox($("#newQuestionTypeSelect"), {destroyOnClose : false});
        else
            showErrorMessage(ttENoTopicsForSubject);
    });

    /**
     *  @descr  Questions DataTables initialization
     */
    questionsTable = $("#questionsTable").DataTable({
            scrollY:        294,
            scrollCollapse: false,
            jQueryUI:       true,
            paging:         false,
            order: [[ qtci.text, "asc" ]],
            columns : [
                { className: "qStatus", searchable : false, type: "alt-string", width : "10px" },
                { className: "qText", width : "400px", mRender: function(data){return truncate(data, "400px")} },
//                { className: "qText", width : "650px" },
                { className: "qLanguages", searchable : false, type: "alt-string", width : "60px" },
                { className: "qTopic", width : "100px" },
                { className: "qType", width : "100px" },
                { className: "qDifficulty", width : "50px"},
                { className: "qQuestionID", visible : true, searchable : false },
                { className: "qTopicID", visible : false },
                { className: "qTypeID", visible : false, searchable : false },
                { className: "qLanguageID", visible : false, searchable : false }
            ],
            language : {
                info: ttDTQuestionInfo,
                infoFiltered: ttDTQuestionFiltered,
                infoEmpty: ttDTQuestionEmpty
            }
        })
        .on("click", "tr", function(){
            showQuestionLanguageAndPreview(this);
        })
        .on("dblclick", "td", function(){
            if($(this).hasClass("qStatus")){
                changeQuestionStatus(new Array($(this).parent(), true));
            }else{
                showQuestionInfo($(this).parent());
            }
        });
    $("#questionsTable_filter").css("margin-right", "50px")
                               .after($("#newQuestion").parent())
                               .before($("#questionsTable_info"));

    $("#topicList .boxBottomCenter").append(printBoxHelpMessage(ttHQuestTopicPanel));
    $("#questionsTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHQuestPanel));
    $("#languageList .boxBottomCenter").append(printBoxHelpMessage(ttHQuestLanguagesPanel));
    $("#questionPreview .boxBottomCenter").append(printBoxHelpMessage(ttHQuestPreviewPanel));
});

/**
 *  @name   filterQuestionsByTopic
 *  @descr  Apply text filter on questionsTable for idTopic hidden column
 *  @param  selectedTopic     DOM Element                     Selected topic <a>
 */
function filterQuestionsByTopic(selectedTopic){
    if(timer)
        clearTimeout(timer);

    topicRowSelected = $(selectedTopic);
    timer = setTimeout(function(){
        $(".filterQuestion[value]").removeClass("selected");
        topicRowSelected.addClass("selected");
        if($(topicRowSelected).attr("value") != "-1")
            questionsTable.columns(qtci.topicID)
                          .search("^"+$(topicRowSelected).attr("value").trim()+"$", true)
                          .draw();
        else
            questionsTable.columns(qtci.topicID)
                          .search("")
                          .draw();
    }, 350);
}

/**
 *  @name   newEmptyTopic
 *  @descr  Ajax request for show empty interface for define a new topic
 */
function newEmptyTopic() {
    $.ajax({
        url     : "index.php?page=subject/showtopicinfo",
        type    : "post",
        data    : {
            action  :   "new",
            idTopic :   null
        },
        success : function (data) {
            if(data == "NACK"){
                //alert(data);
            }else{
//                alert(data);
                $("body").append(data);
                newLightbox($("#topicInfo"), {});
                enableCharsCounter("infoName", "topicName");
                enableCharsCounter("infoDesc", "topicDesc");
                $("#infoName").focus();

                topicEditing = true;
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   showTopicInfo
 *  @descr  Ajax request for show topic's informations
 *  @param  selectedTopic     DOM Element                     Selected topic <a>
 */
function showTopicInfo(selectedTopic) {
    clearTimeout(timer);
    
}

/**
 *  @name   showQuestionLanguageAndPreview
 *  @descr  Ajax request for show question's preview
 *  @param  selectedQuestion     DOM Element                     Selected question <tr>
 */
function showQuestionLanguageAndPreview(selectedQuestion) {
    if(timer)
        clearTimeout(timer);

    timer = setTimeout(function(){
        if(selectedQuestion != null){
            questionRowSelected = $(selectedQuestion);
            questionsTable.$("tr.selected").removeClass("selected");
            $(selectedQuestion).addClass("selected");

            var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];
            var idLanguage = questionsTable.row(questionRowSelected).data()[qtci.languageID];

            showQuestionPreview(idQuestion, idLanguage, null);

            $.ajax({
                url     : "index.php?page=question/showquestionlanguages",
                type    : "post",
                data    : {
                    idQuestion :   idQuestion
                },
                success : function (data) {
                    if(data == "NACK"){
//                        alert(data);
                    }else{
//                        alert(data);
                        $("#languageList .boxContent").html(data)
                            .slideDown({
                                duration : 400
                            });
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }
    }, 350);
}

/**
 *  @name   showQuestionPreview
 *  @descr  Ajax request for show question's preview of requeted language
 *  @param  idQuestion          String                     Selected question's ID
 *  @param  idLanguage          String                     Selected question's language ID
 *  @param  selectedLanguage    DOM Element                If is set select <li>
 */
function showQuestionPreview(idQuestion, idLanguage, selectedLanguage) {
    if(timer)
        clearTimeout(timer);

    timer = setTimeout(function(){
        if(selectedLanguage != null){
            languageRowSelected = $(selectedLanguage);
            $(".showQuestionPreview").removeClass("selected");
            languageRowSelected.addClass("selected");
        }
        $.ajax({
            url     : "index.php?page=question/showquestionpreview",
            type    : "post",
            data    : {
                idQuestion :   idQuestion,
                idLanguage :   idLanguage,
                type       :   questionsTable.row(questionRowSelected).data()[qtci.typeID]
            },
            success : function (data) {
                if(data == "NACK"){
                   //alert(data);
                }else{
                   //alert('ciao'+data);
                    $("#questionPreview .boxContent").html(data)
                        .slideDown({
                            duration : 400
                        });
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }, 350);
}

/**
 *  @name   showQuestionInfo
 *  @descr  Ajax request for show question's informations
 *  @param  selectedQuestion    DOM Element     Selected question <tr>
 */
function showQuestionInfo(selectedQuestion) {
    clearTimeout(timer);
    showQuestionLanguageAndPreview(selectedQuestion);
    questionRowSelected = $(selectedQuestion);

    var idQuestion = questionsTable.row(questionRowSelected).data()[qtci.questionID];

    $.ajax({
        url     : "index.php?page=question/showquestioninfo",
        type    : "post",
        data    : {
            action      :   "show",
            idQuestion  :   idQuestion
        },
        success : function (data) {
            if(data == "NACK"){
//                alert(data);
            }else{
//                alert(data);
                $("body").append(data);
                newLightbox($("#questionInfo"), {});
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   updateQuestionTypeDescription
 *  @descr  Show correct question type's description
 */
function updateQuestionTypeDescription(){
    $(".QTDescription").hide();
    $("#QT"+$("#newQuestionType").val()).show();
}

/**
 *  @name   newEmptyQuestion
 *  @descr  Ajax request for show panel for new question
 */
function newEmptyQuestion() {
    if($("#newQuestionType").val() != null){
        $("#newQuestionTypeSelect").slideUp();
        $.ajax({
            url     : "index.php?page=question/showquestioninfo",
            type    : "post",
            data    : {
                action      :   "new",
                type        :   $("#newQuestionType").val(),
                topic       :   $(".filterQuestion.selected").attr("value")
            },
            success : function (data) {
                if(data == "NACK"){
    //                alert(data);
                }else{
    //                alert(data);
                    $("body").append(data);
                    newLightbox($("#questionInfo"), {});
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }else{
        showErrorMessage(ttESelectQuestionType);
    }
}

/**
 * @name    changeQuestionStatus
 * @descr   Binded function to change question's status
 * @param   selectedQuestionAndConfirm      Array         Selected question's <tr> | askConfirm
 */
function changeQuestionStatus(selectedQuestionAndConfirm){
    clearTimeout(timer);

    var rowTable = questionsTable.row(selectedQuestionAndConfirm[0]);
    var idQuestion = rowTable.data()[qtci.questionID];
    var newStatus, newStatusText;
    switch($(rowTable.data()[qtci.status]).attr("value")){
        case "i" : newStatus = "a"; newStatusText = ttActive; break;
        case "a" : newStatus = "i"; newStatusText = ttInactive; break;
        case "e" : newStatus = "a"; newStatusText = ttError; break;
    }
    if((!selectedQuestionAndConfirm[1]) || (confirmDialog(ttWarning, ttCChangeStatus.replace("_STATUS_", newStatusText), changeQuestionStatus, new Array(selectedQuestionAndConfirm[0], false)))){
        $.ajax({
            url     :   "index.php?page=question/changestatus",
            type    :   "post",
            data    :   {
                idQuestion  :   idQuestion,
                status      :   newStatus
            },
            success : function (data) {
//                alert(data);
                if(data == "ACK"){
                    // Update status icon
                    var questionStatus = $(rowTable.data()[qtci.status]);
                    $(questionStatus).attr("value", newStatus)
                        .attr("title", newStatusText)
                        .attr("alt", newStatusText)
                        .attr("src", imageDir + newStatusText + ".png");
                    questionsTable.cell(rowTable.index(), qtci.status).data(questionStatus.outer());

                    showSuccessMessage(ttMEdit);
                }else{
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
 *  @name   destroyAllCKEditorInstances
 *  @descr  Destroy all CKEditor instances in DOM
 */
function destroyAllCKEditorInstances(){
    for(name in CKEDITOR.instances)
        CKEDITOR.instances[name].destroy();
    $("textarea.ckeditor").hide();
}

/**
 *  @name   createCKEditorInstance
 *  @descr  Creates CKEditor instance with specified ID
 *  @param  instance        String      Instance ID
 */
function createCKEditorInstance(instance){
    destroyAllCKEditorInstances();
    var roxyFileman = '/fileman/index.html';
    var onchange = null;
    switch(instance.split("t")[0]){
        case "q" : onchange = function() { this.updateElement(); questionEditing = true; }; break;
        case "a" : onchange = function() { this.updateElement(); answerEditing = true; }; break;
        default : alert("CKEditor creation error");
    }
    CKEDITOR.replace(instance, {
        filebrowserBrowseUrl:roxyFileman,
//        filebrowserUploadUrl:roxyFileman,
        filebrowserImageBrowseUrl:roxyFileman+'?type=image',
//        filebrowserImageUploadUrl:roxyFileman+'?type=image',
        on: { change: onchange }
    });
}

function closeQuestionTypeSelect(){
    closeLightbox($('#newQuestionTypeSelect'));
}

function closeQuestionLanguagePanel(){
    $("#languageList .boxContent").slideUp();
}

function closeQuestionPreviewPanel(){
    $("#questionPreview .boxContent").slideUp();
}