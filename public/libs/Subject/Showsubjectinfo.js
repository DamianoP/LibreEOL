/**
 * File: Showsubjectinfo.js
 * User: Masterplan
 * Date: 17/05/14
 * Time: 11:10
 * Desc: Show subject info panel or show empty infos for new subject
 */

$(function(){

    // read class (for readonly input tag)
    $(".readonly").attr("disabled", "");

    /**
     *  @descr  Enables language dropdown
     */
    $("#infoLanguage dt.writable").on("click", function() {
                                                   $(this).children("span").toggleClass("clicked");
                                                   $(this).next().children("ol").slideToggle(200);
                                               });
    $("#infoLanguage dd ol li").on("click", function() {
                                                subjectEditing = true;
                                                updateDropdown($(this));
                                            });
    $(document).on('click', function(e) {
        var $clicked = $(e.target);
        if (!($clicked.parents().hasClass("dropdownInfo"))){
            $(".dropdownInfo dd ol").slideUp(200);
            $(".dropdownInfo dt span").removeClass("clicked");
        }
    });

    /**
     *  @descr  Enables chars counters for infoName and infoDesc fields
     */
    enableCharsCounter("infoName", "subjectName");
    enableCharsCounter("infoDesc", "subjectDesc");

});

$(function(){
    if($("#dialogExport").length > 0){
        $("#dialogExport").dialog({
            autoOpen        :   false,
            draggable       :   false,
            resizable       :   true,
            width           :   "auto",
            height          :   "auto",
            modal           :   true,
            closeOnEscape   :   false,
            position        :   ["center", 50],
            buttons : {
                No : function(){
                    $(this).dialog("close");
                    confirmCallback($(this).data("callback"), 'none', false);
                },
                QTI : function(){
                    $(this).dialog("close");
                    confirmCallback($(this).data("callback"), 'qti', true);
                },
                Moodle : function (){
                    $(this).dialog("close");
                    confirmCallback($(this).data("callback"), 'moodle', true);
                }
            }
        });
    }
});

/**
 *  @name   selectSubject
 *  @descr  Select subject and go to Topics/Questions edit page or Test Settings edit page
 */
function selectSubject(){
    $("input[name=idSubject]").attr("value", $(".selected").attr("value"));
    var request = $("input[name=request]").attr("value").trim();


    if((request == "1") || (request == "qstn"))
        $("#idSubjectForm").attr("action", "index.php?page=question");
    else if((request == "2") || (request == "qstn2"))
        $("#idSubjectForm").attr("action", "index.php?page=question/index2");
    else if(request == "set")
        $("#idSubjectForm").attr("action", "index.php?page=exam/settings");
    $("#idSubjectForm").submit();
}

/**
 *  @name   editSubjectInfo
 *  @descr  Enable subject's information edits
 */
function editSubjectInfo(){
    makeWritable($("#infoName, #infoDesc"));
    enableCharsCounter("infoName", "topicName");
    enableCharsCounter("infoDesc", "topicDesc");

    teachersTable.search("").draw();
    teachersTable.order([ ttci.checkbox, "desc" ]).draw();
    $("#teachersTableContainer").slideDown({
        duration:400,
        complete:function(){
            teachersTable.columns.adjust();
            $("#teachersTable_filter input").val("");
        }
    });

    $("#editPanel").show();
    $("#selectPanel").hide();
    $("#createPanel").hide();
    $("#infoName").focus();
    subjectEditing = true;
}

/**
 *  @name   saveEdit
 *  @descr  Saves subject's infos
 */
function saveEdit(){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else{
        var name = $("#infoName").val();
        var desc = $("#infoDesc").val();
        var teachers = teachersTable.$(".tCheckbox input").serialize().replace(/teacher=/g, "");
        if(name != ""){
            $.ajax({
                url     : "index.php?page=subject/updatesubjectinfo",
                type    : "post",
                data    : {
                    idSubject    : $(subjectRowEdit).attr('value'),
                    subjectName  : name,
                    subjectDesc  : desc,
                    teachers     : teachers
                },
                success : function (data) {
                    if(data == "ACK"){
//                        alert(data);
                        showSuccessMessage(ttMEdit);
                        $(subjectRowEdit).text(name);
                        cancelEdit(false);
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttEEmptyFields);
        }
    }
}

/**
 *  @name   deleteSubject
 *  @descr  Deletes subject after confirmation
 *  @param  askConfirmation         Boolean         If true shows confirmation dialog
 */
function deleteSubject(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteSubject, deleteSubject, false))){
        $.ajax({
            url     : "index.php?page=subject/deletesubject",
            type    : "post",
            data    : {
                idSubject    : $(subjectRowEdit).attr('value')
            },
            success : function (data, status) {
                if(data == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMSubjectDeleted);
                    subjectEditing = false;
                    setTimeout(function(){
                            $(subjectRowEdit).parent().remove();
                            $("#subjectInfo .boxContent").slideUp({
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
 *  @name   cancelEdit
 *  @descr  Goes back to original subject's info
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, false))){
        subjectEditing = false;
        $("#editPanel").hide();
        $("#createPanel").hide();
        $("#selectPanel").show();
        showSubjectInfo(new Array(false, subjectRowEdit));
        $("#teachersTableContainer").slideUp();
    }
}

/**
 *  @name   createNewSubject
 *  @descr  Creates new subject with specified name, description and main language
 */
function createNewSubject(){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else{
        var name = $("#infoName").val();
        var vers = $("#infoVers").val();
        var desc = $("#infoDesc").val();
        var lang = $("#infoLanguage dt span.value").text();
        if((name != "") && (lang != "") && (vers != "") && !isNaN(parseFloat(vers))){
            $.ajax({
                url     : "index.php?page=subject/newsubject",
                type    : "post",
                data    : {
                    subjectName  : name,
                    subjectVers  : vers,
                    subjectDesc  : desc,
                    subjectLang  : lang
                },
                success : function (data, status) {
                    data = data.trim();
                    if(!isNaN(data)){
//                        alert(data);
                        showSuccessMessage(ttMNewSubject);
                        setTimeout(function(){
                            $(subjectRowEdit).attr("value", data)
                                .attr("onclick", "showSubjectInfo(new Array(subjectEditing, this));")
                                .text(name);
                            subjectEditing = false;
                            subjectNew = false;
                            showSubjectInfo(new Array(false, subjectRowEdit));
                        }, 1000);
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else
            showErrorMessage(ttEEmptyFields);
    }
}

/**
 *  @name   cancelNew
 *  @descr  Closes new panel and slide up the right box
 */
function cancelNew(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNew, false))){
        $("#subjectsList ul li a[value=new]").parent().remove();
        $("#subjectInfo .boxContent").slideUp();
        $("#editPanel").hide();
        $("#createPanel").hide();
        $("#selectPanel").show();
        $("#teachersTableContainer").slideUp();
        subjectEditing = false;
        subjectNew = false;
        subjectRowEdit =null;
    }
}

/**
 *  @name   exportSubject
 *  @descr  Download the requested subject
 */
function exportSubject(type) {

    let sub = $(".selected").attr("value");
    $.ajax({
            url: "index.php?page=export/exportrequest",
            type: "post",
            data: {
                idSubject: sub,
                type: type
            },
            success: function (data) {
                switch (data) {
                    case 'ACK':
                        showSuccessMessage(ttMExportRequestSent);
                        break;
                    case 'EMPTYSUB':
                        showErrorMessage(ttEExportEmptySub)
                        break;
                    case 'NOANSWER':
                        showErrorMessage(ttEExportNoAnswers)
                        break;
                    case 'NOEANSWERS':
                        showErrorMessage(ttEExportNotEnAnswers)
                        break;
                    default:
                        showErrorMessage(data)
                        break;
                }
            },
            error: function (request, status, error) {
                alert("jQuery AJAX request error:".error)
            }
        }
    );
}

function exportDialog (callback,params) {
    $('#dialogExport p').html(ttExportMessage);
    $('#dialogExport').data("callback", callback)
        .data("params", params)
        .dialog("option", "title", ttExport )
        .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"confirmDialog.png')");
}

function selectExportType(){
    exportDialog(function (type){
        exportSubject(type);
    });
}
