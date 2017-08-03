/**
 * File: Settings.js
 * User: Masterplan
 * Date: 4/19/13
 * Time: 11:12 AM
 * Desc: Javascript library to show settings
 */

var settingsEditing = false;
var settingsRowEdit = null;
var settingsNew = false;

var questionsTable = null;
var questionsSelected = null;
var qtci = {
    checkbox : 0,
    text : 1,
    languages : 2,
    topic : 3,
    type : 4,
    difficulty : 5,
    questionID : 6,
    topicID : 7,
    typeID : 8,
    difficultyID : 9,
    selected : 10
};

$(function(){

    /**
     *  @descr  Binded event for New button
     */
    $("#newSettings").on("click", function(){ newSettings(new Array(settingsEditing)); });

    questionsTable = $("#questionsTable").DataTable({
                                scrollY:        120,
                                scrollCollapse: false,
                                jQueryUI:       true,
                                paging:         false,
                                order: [ qtci.text, "asc" ],
                                columns : [
                                    { className: "qCheckbox", searchable : false, visible : false, "orderDataType": "dom-checkbox", width : "10px" },
                                    { className: "qText", width : "570px", mRender: function(data){return truncate(data, "600px")} },
                                    { className: "qLanguages", searchable : false, type: "alt-string", width : "60px" },
                                    { className: "qTopic", width : "80px" },
                                    { className: "qType", width : "100px" },
                                    { className: "qDifficulty", width : "50px"},
                                    { className: "qQuestionID", visible : false, searchable : false },
                                    { className: "qTopicID", visible : false },
                                    { className: "qTypeID", visible : false},
                                    { className: "qDifficultyID", visible : false},
                                    { className: "qSelected", visible : false}
                                ],
                                language : {
                                    info: ttDTQuestionInfo,
                                    infoFiltered: ttDTQuestionFiltered,
                                    infoEmpty: ttDTQuestionEmpty
                                }
                            });

    $("#questionTopicSelect").on("change", function(){
        if($(this).val().trim() != "-1")
            questionsTable.columns(qtci.topicID)
                          .search("^"+$(this).val().trim()+"$", true)
                          .draw();
        else
            questionsTable.columns(qtci.topicID)
                          .search("")
                          .draw();
    });
    $("#questionTypeSelect").on("change", function(){
        if($(this).val().trim() != "-1")
            questionsTable.columns(qtci.typeID)
                .search("^"+$(this).val().trim()+"$", true)
                .draw();
        else
            questionsTable.columns(qtci.typeID)
                .search("")
                .draw();
    });
    $("#questionDifficultySelect").on("change", function(){
        if($(this).val().trim() != "-1")
            questionsTable.columns(qtci.difficultyID)
                .search("^"+$(this).val().trim()+"$", true)
                .draw();
        else
            questionsTable.columns(qtci.difficultyID)
                .search("")
                .draw();
    });

    $("#questionsTable_filter").before($("#questionsTable_info"));

    $("#settingsInfo .boxContent").hide();

});

/**
 *  @name   showSettingsInfo
 *  @descr  Shows info about requested test settings
 *  @param  selectedSettingsAndConfirmation       Array           Array contains selected settings and boolean for ask confirmation
 */
function showSettingsInfo(selectedSettingsAndConfirmation){
    settingsRowEdit = selectedSettingsAndConfirmation[0];
    var askConfirmation = selectedSettingsAndConfirmation[1];
    if((!askConfirmation) || (askConfirmation && (confirmDialog(ttWarning, ttCDiscardEdits, showSettingsInfo, new Array(settingsRowEdit, false))))){
        $(".showSettingsInfo").removeClass("selected");
        $(settingsRowEdit).addClass("selected");
        $.ajax({
            url     : "index.php?page=exam/shownewsettingsinfo",
            type    : "post",
            data    : {
                action          :   "show",
                idTestSetting   :   $(".selected").attr('value')
            },
            success : function (data) {
//                alert($(".selected").attr('value'));
                if(data == "NACK"){
//                    alert(data);
                }else{
//                    alert(data);
                    if(settingsNew)
                        cancelNew(new Array(false));
                    if(settingsEditing)
                        cancelEdit(new Array(false));
                    $("#testSettingsInfo").html(data);
                    $("#questionTopicSelect option:eq(0)," +
                      "#questionTypeSelect option:eq(0)," +
                      "#questionDifficultySelect option:eq(0)").prop("selected", true).change();
                    var checkbox = null;
                    questionsTable.rows().eq(0).each(function(value, index){
                        checkbox = questionsTable.cell(index, qtci.checkbox).nodes().to$().find("input");
                        if($.inArray($(checkbox).val(), oldSelectedQuestion) > -1)
                            questionsTable.cell(index, qtci.selected).data("X");
                        else
                            questionsTable.cell(index, qtci.selected).data("");

                    });
                    questionsTable.rows().eq(0).each(function(value, index){
                        checkbox = questionsTable.cell(index, qtci.checkbox).nodes().to$().find("input");
                        $(checkbox).prop("checked", ($.inArray($(checkbox).val(), oldSelectedQuestion) > -1));
                    });
                    $("#settingsInfo .boxContent").slideDown({
                        duration : 500,
                        complete : function(){
                            questionsTable.columns(qtci.selected)
                                .search("X")
                                .draw();
                                }
                            });
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   editSettingsInfo
 *  @descr  Enables edits fields and shows Save button
 */
function editSettingsInfo(){
    makeWritable($("#settingsName, #settingsScoreMin, #settingsBonus, #settingsDesc, #settingsScoreType dt," +
                   "#settingsNegative dt, #settingsEditable dt, #settingsQuestions, #settingsDurationH dt, " +
                   "#settingsDurationM dt, #settingsCertificate dt, #setEasy, #setMedium ,#setHard "));
    $(".dropdownInfo dt.writable").on("click", function() {
        $(this).children("span").toggleClass("clicked");
        $(this).next().children("ol").slideToggle(200);
    });
    $(".settingsTopic").each(function(){
        $(this).removeClass("hidden");
        makeWritable($(this).find("td.settingsTopicQuestions input"));
    });
    $("#viewPanel").hide();
    $("#editPanel").show();
    questionsTable.column(qtci.checkbox).visible(true);
    questionsTable.columns(qtci.selected)
        .search("");
    questionsTable.order([[ qtci.checkbox, "desc" ], [ qtci.text, "asc" ]])
        .draw();
    settingsEditing = true;
}

/**
 *  @name   saveSettingsInfo
 *  @descr  Stores edited test setting's infos into database
 *  @param  completeUpdate          Array           If completeUpdate[0] == true save all infos, else save only name and description
 */
function saveSettingsInfo(completeUpdate){
    completeUpdate = completeUpdate[0];

    if(($("#settingsName").isEmpty()) ||
        ($("#settingsScoreMin").isEmpty()) || (isNaN($("#settingsScoreMin").val())) ||($("#settingsScoreMin").val() < 0) ||
        ($("#settingsBonus").isEmpty()) || (isNaN($("#settingsBonus").val())) ||($("#settingsBonus").val() < 0) ||
        ($("#setEasy").isEmpty()) || (isNaN($("#setEasy").val())) ||($("#setEasy").val() < 0) ||
        ($("#setMedium").isEmpty()) || (isNaN($("#setMedium").val())) || ($("#setMedium").val() < 0) ||
        ($("#setHard").isEmpty()) || (isNaN($("#setHard").val())) ||($("#setHard").val() < 0) ||
        ($("#settingsQuestions").isEmpty()) ||($("#settingsQuestions").val() < 0) || (isNaN($("#settingsQuestions").val()))){
        showErrorMessage(ttEIncorrectField);
    }else if(!checkSuddivisionIntegrity()) {
        showErrorMessage(ttEIncorrectField);
    }else if(!checkTotalsIntegrity()) {
    }else if(checkTopicIntegrity()){
        var id = null;
        var topicQuestionsId = [];
        var topicQuestionsValue = [];
        var numMaxEasy = [];
        var numMaxMedium = [];
        var numMaxHard = [];
        var counter = 0; var counterE = 0; var counterM = 0; var counterH = 0;
        $(".numQuestionsPerTopic").each(function(){
            id = $(this).attr("id");
            id = id.substring(12,id.length);
            topicQuestionsId[counter] = id;
            topicQuestionsValue[counter] = parseInt($(this).val());
            counter ++;
        });
        $(".numMaxEasy").each(function () {
            numMaxEasy[counterE] = parseInt(deleteFirstLastChar($(this).text()));
            counterE++;
        });
        $(".numMaxMedium").each(function () {
            numMaxMedium[counterM] = parseInt(deleteFirstLastChar($(this).text()));
            counterM++;
        });
        $(".numMaxHard").each(function () {
            numMaxHard[counterH] = parseInt(deleteFirstLastChar($(this).text()));
            counterH++;
        });

        $.ajax({
            url     : "index.php?page=exam/updatesettingsinfo",
            type    : "post",
            data    : {
                idTestSetting   :   $("#settingsList .selected").attr("value"),
                name            :   $("#settingsName").val(),
                scoreType       :   $("#settingsScoreType dt span.value").text(),
                scoreMin        :   $("#settingsScoreMin").val(),
                bonus           :   $("#settingsBonus").val(),
                negative        :   $("#settingsNegative dt span.value").text(),
                editable        :   $("#settingsEditable dt span.value").text(),
                certificate     :   $("#settingsCertificate dt span.value").text(),
                duration        :   (parseInt($("#settingsDurationH dt span.value").text()) * 60
                                    + parseInt($("#settingsDurationM dt span.value").text())),
                questions       :   $("#settingsQuestions").val(),
                easy            :   $("#setEasy").val(),
                medium          :   $("#setMedium").val(),
                hard            :   $("#setHard").val(),
                desc            :   $("#settingsDesc").val(),
                topicQuestionsId:   JSON.stringify(topicQuestionsId),
                topicQuestionsValue : JSON.stringify(topicQuestionsValue),
                numMaxE         :   JSON.stringify(numMaxEasy),
                numMaxM         :   JSON.stringify(numMaxMedium),
                numMaxH         :   JSON.stringify(numMaxHard),
                mandatQuestionsI:   questionsTable.$(".qCheckbox input").serialize().replace(/question=/g, ""),
                completeUpdate  :   completeUpdate
            },
            success : function (data) {
                data = data.trim().split(ajaxSeparator);
                if(data[0] == "ACK"){
//                    alert(data);
                    if(data[1] == "ACK"){         // PHP returns warning
                        showSuccessMessage(ttMEdit);
                        setTimeout(function(){
                            $(settingsRowEdit).text($("#settingsName").val());
                            showSettingsInfo(new Array(settingsRowEdit, false));
                        }, 1000);
                    }else{                      // PHP returns new TestSettings ID
                        confirmDialog(ttWarning, data[1], saveSettingsInfo, new Array(false));
                    }
                }else{
                    showErrorMessage(data[0]);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   newSettings
 *  @descr  Shows empty panels to create a new test settings
 *  @param  askConfirmation             Array           If askConfirmation[0] is true shows confirm dialog
 */
function newSettings(askConfirmation){
    if(settingsNew)
        errorDialog(ttWarning, ttESaveBeforeNew);
    else{
        if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardEdits, newSettings, new Array(false)))){
            $(".showSettingsInfo").removeClass("selected");
            $("#settingsList ul").append("<li class='lPad'><a class='showSettingsInfo selected' value='new'>" + ttNewTestSettings + "</a></li>");
            $.ajax({
                url     : "index.php?page=exam/shownewsettingsinfo",
                type    : "post",
                data    : {
                    action          : "new",
                    idTestSetting   : null
                },
                success : function (data) {
                    if(data == "NACK"){
//                        alert(data);
                    }else{
//                        alert(data);
                        $("#testSettingsInfo").html(data);
                        $("#settingsInfo .boxContent").slideDown({
                            duration : 500,
                            complete : function(){
                                $("#editPanel").hide();
                                $("#viewPanel").hide();
                                $("#newPanel").show();
                                $("#settingsName").focus();
                                settingsEditing = true;
                                settingsNew = true;
                                settingsRowEdit = $(".showSettingsInfo[value='new']");
                                $("#questionTopicSelect option:eq(0)," +
                                    "#questionTypeSelect option:eq(0)," +
                                    "#questionDifficultySelect option:eq(0)").prop("selected", true).change();
                                questionsTable.columns(qtci.selected)
                                              .search("")
                                              .draw();
                                questionsTable.column(qtci.checkbox).visible(true);
                                questionsTable.$(".qCheckbox input").prop("checked", false);
                            }
                        });
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }
    }
}

/**
 *  @name   deleteSettings
 *  @descr  Deletes Test Settings
 *  @param  askConfirmation             Array           If askConfirmation[0] is true shows confirm dialog
 */
function deleteSettings(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDeleteSettings, deleteSettings, new Array(false)))){

        $.ajax({
            url     : "index.php?page=exam/deletesettings",
            type    : "post",
            data    : {
                idTestSetting   :   $(settingsRowEdit).attr("value")
            },
            success : function (data) {
                if(data == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMSettingsDeleted);
                    setTimeout(function(){
                        settingsEditing = false;
                        $("#editPanel").hide();
                        $("#viewPanel").show();
                        questionsTable.column(qtci.checkbox).visible(false);
                        $(settingsRowEdit).parent().remove();
                        $("#settingsInfo .boxContent").slideUp({
                            duration : 400
                        });
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
}

/**
 *  @name   createNewSettings
 *  @descr  Creates new test settings with filled data
 */
function createNewSettings(){
    if(($("#settingsName").isEmpty()) ||
        ($("#settingsScoreMin").isEmpty()) || (isNaN($("#settingsScoreMin").val())) ||($("#settingsScoreMin").val() < 0) ||
        ($("#settingsBonus").isEmpty()) || (isNaN($("#settingsBonus").val())) ||($("#settingsBonus").val() < 0) ||
        ($("#setEasy").isEmpty()) || (isNaN($("#setEasy").val())) ||($("#setEasy").val() < 0) ||
        ($("#setMedium").isEmpty()) || (isNaN($("#setMedium").val())) || ($("#setMedium").val() < 0) ||
        ($("#setHard").isEmpty()) || (isNaN($("#setHard").val())) ||($("#setHard").val() < 0) ||
        ($("#settingsQuestions").isEmpty()) ||($("#settingsQuestions").val() < 0) || (isNaN($("#settingsQuestions").val()))){
        console.log("error type 1");
        showErrorMessage(ttEIncorrectField);
    }else if(!checkSuddivisionIntegrity()) {
        console.log("error type 2");
        showErrorMessage(ttEIncorrectField);
    }else if(!checkTotalsIntegrity()) {
        console.log("error type 3");
    }else if(checkTopicIntegrity()){
        var id = null;
        var topicQuestionsId = [];
        var topicQuestionsValue = [];
        var numMaxEasy = [];
        var numMaxMedium = [];
        var numMaxHard = [];
        var counter = 0; var counterE = 0; var counterM = 0; var counterH = 0;
        $(".numQuestionsPerTopic").each(function(){
            id = $(this).attr("id");
            id = id.substring(12,id.length);
            topicQuestionsId[counter] = id;
            topicQuestionsValue[counter] = parseInt($(this).val());
            counter ++;
        });
        $(".numMaxEasy").each(function () {
            numMaxEasy[counterE] = parseInt(deleteFirstLastChar($(this).text()));
            counterE++;
        });
        $(".numMaxMedium").each(function () {
            numMaxMedium[counterM] = parseInt(deleteFirstLastChar($(this).text()));
            counterM++;
        });
        $(".numMaxHard").each(function () {
            numMaxHard[counterH] = parseInt(deleteFirstLastChar($(this).text()));
            counterH++;
        });

        $.ajax({
            url     : "index.php?page=exam/newsettings",
            type    : "post",
            data    : {
                name            :   $("#settingsName").val(),
                scoreType       :   $("#settingsScoreType dt span.value").text(),
                scoreMin        :   $("#settingsScoreMin").val(),
                bonus           :   $("#settingsBonus").val(),
                negative        :   $("#settingsNegative dt span.value").text(),
                editable        :   $("#settingsEditable dt span.value").text(),
                certificate     :   $("#settingsCertificate dt span.value").text(),
                duration        :   (parseInt($("#settingsDurationH dt span.value").text()) * 60
                                    + parseInt($("#settingsDurationM dt span.value").text())),
                questions       :   $("#settingsQuestions").val(),
                easy            :   $("#setEasy").val(),
                medium          :   $("#setMedium").val(),
                hard            :   $("#setHard").val(),
                desc            :   $("#settingsDesc").val(),
                topicQuestionsId:   JSON.stringify(topicQuestionsId),
                topicQuestionsValue: JSON.stringify(topicQuestionsValue),
                numMaxE         :   JSON.stringify(numMaxEasy),
                numMaxM         :   JSON.stringify(numMaxMedium),
                numMaxH         :   JSON.stringify(numMaxHard),
                mandatQuestionsI:   questionsTable.$(".qCheckbox input").serialize().replace(/question=/g, "")
            },
            success : function (data) {
                data = data.trim().split(ajaxSeparator);
                if(data[0] == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMNewSettings);
                    setTimeout(function(){
                        $(settingsRowEdit).attr("value", data[1])
                                          .attr("onclick", "showSettingsInfo(new Array(this, settingsEditing));")
                                          .text($("#settingsName").val());
                        questionsTable.column(qtci.checkbox).visible(false);
                        showSettingsInfo(new Array(settingsRowEdit, false));
                    }, 1000);
                }else{
                    console.log("error type 4");
                    console.log(data[0]);
                    showErrorMessage(data[0]);
                }
            },
            error : function (request, status, error) {
                console.log("error type 5");
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   cancelEdit
 *  @descr  Goes back to original test settings's info
 *  @param  askConfirmation         Array         If [0] is true display confirmation dialog
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, new Array(false)))){
        settingsEditing = false;
        $("#editPanel").hide();
        $("#newPanel").hide();
        $("#viewPanel").show();
        questionsTable.columns(qtci.selected)
                      .search("X")
                      .draw();
        questionsTable.column(qtci.checkbox).visible(false);
        showSettingsInfo(new Array(settingsRowEdit, false));
    }
}

/**
 *  @name   cancelNew
 *  @descr  Closes new panel and slide up the right box
 *  @param  askConfirmation         Array         If [0] is true display confirmation dialog
 */
function cancelNew(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNew, new Array(false)))){
        $("#settingsList ul li a[value=new]").parent().remove();
        $("#settingsInfo .boxContent").slideUp();
        $("#editPanel").hide();
        $("#newPanel").hide();
        $("#viewPanel").show();
        settingsEditing = false;
        settingsNew = false;
        questionsTable.column(qtci.checkbox).visible(false);
    }
}
function helpjs(){

    $("#dialogError p").html(ttHelpExamsSettings);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}
