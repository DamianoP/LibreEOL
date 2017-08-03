/**
 * File: Subjects.js
 * User: Masterplan
 * Date: 3/25/13
 * Time: 12:48 PM
 * Desc: Javascript library for show subject
 */

var subjectEditing = false;
var subjectRowEdit = null;
var subjectNew = false;
var infos = { "selected"    :   null,
              "name"        :   "",
              "desc"        :   ""
};
var teachersTable = null;
var ttci = {
    checkbox : 0,
    surname : 1,
    name : 2,
    email : 3,
    userID : 4,
    selected : 5
};

$(function(){
    $("#subjectInfo .boxContent").hide();

    teachersTable = $("#teachersTable").DataTable({
        scrollY:        100,
        scrollCollapse: false,
        jQueryUI:       true,
        paging:         false,
        order: [ [ttci.surname, "asc"], [ttci.name, "asc"] ],
        columns : [
            { className: "tCheckbox", searchable : false, "orderDataType": "dom-checkbox", width : "10px" },
            { className: "tSurname" },
            { className: "tName" },
            { className: "tEmail" },
            { className: "tUserID", visible : false },
            { className: "tSelected", visible : false }
        ],
        language : {
            info: ttDTTeacherInfo,
            infoFiltered: ttDTTeacherFiltered,
            infoEmpty: ttDTTeacherEmpty
        }
    });
    $("#teachersTable_filter").before($("#teachersTable_info"));

    /**
     *  @descr  Binded event for New button
     */
    $("#new").html('');
});

/**
 *  @name   showSubjectInfo
 *  @descr  Shows info about requested subject
 *  @param  askConfirmationAndSelectedSubject           Array           Array contains boolean for ask confirmation and selected subject
 */
function showSubjectInfo(askConfirmationAndSelectedSubject){
    var askConfirmation = askConfirmationAndSelectedSubject[0];
    subjectRowEdit = askConfirmationAndSelectedSubject[1];
    if((!askConfirmation) || (askConfirmation && (confirmDialog(ttWarning, ttCDiscardEdits, showSubjectInfo, new Array(false, subjectRowEdit))))){
        $(".showSubjectInfo").removeClass("selected");
        $(subjectRowEdit).addClass("selected");
        $.ajax({
            url     : "index.php?page=subject/showsubjectinfo",
            type    : "post",
            data    : {
                action       : "show",
                idSubject    : $(subjectRowEdit).attr("value")
            },
            success : function (data) {
                if(data == "NACK"){
//                    alert(data);
                }else{
//                    alert(data);
                    $("#subjectInfo .infoEdit").html(data);
                    if(subjectEditing)
                        cancelEdit(false);
                    if(subjectNew)
                        cancelNew(false);
                    $("#editPanel").hide();
                    $("#createPanel").hide();
                    $("#selectPanel").show();
                    var checkbox = null;
//                    teachersTable.rows().eq(0).each(function(value, index){
//                        checkbox = teachersTable.cell(index, ttci.checkbox).nodes().to$().find("input");
//                        if($.inArray($(checkbox).val(), oldAssignedTeachers) > -1)
//                            teachersTable.cell(index, ttci.selected).data("X");
//                        else
//                            teachersTable.cell(index, ttci.selected).data("");
//
//                    });
                    teachersTable.rows().eq(0).each(function(value, index){
                        checkbox = teachersTable.cell(index, ttci.checkbox).nodes().to$().find("input");
                        $(checkbox).prop("checked", ($.inArray($(checkbox).val(), oldAssignedTeachers) > -1));
                    });
                    $("#teachersTableContainer").hide();
                    $("#subjectInfo .boxContent").slideDown();
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   newSubject
 *  @descr  Shows panel to add new subject's infos
 *  @param  askConfirmation         Boolean         If true show confirmation dialog
 */
function newSubject(askConfirmation){
    if(subjectNew)
        errorDialog(ttWarning, ttESaveBeforeNew);
    else{
        if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, newSubject, false))){
            $(".showSubjectInfo").removeClass("selected");
            $("#subjectsList ul").append("<li><a class='showSubjectInfo selected' value='new'>" + ttNewSubject + "</a></li>");
            $.ajax({
                url     : "index.php?page=subject/showsubjectinfo",
                type    : "post",
                data    : {
                    action       : "new",
                    idSubject    : null
                },
                success : function (data) {
                    if(data == "NACK"){
//                        alert(data);
                    }else{
//                        alert(data);
                        $("#subjectInfo").find(".infoEdit").html(data);
                        $("#editPanel").hide();
                        $("#createPanel").show();
                        $("#selectPanel").hide();
                        $("#infoName").focus();
                        subjectEditing = true;
                        subjectNew = true;
                        subjectRowEdit = $(".showSubjectInfo[value='new']");
                        teachersTable.rows().eq(0).each(function(value, index){
                            teachersTable.cell(index, ttci.checkbox).nodes().to$().find("input").prop("checked", false);
                        });
                        $("#teachersTableContainer").show();
                        $("#subjectInfo .boxContent").slideDown();
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }
    }
}