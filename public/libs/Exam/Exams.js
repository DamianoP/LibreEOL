/**
* File: Exams.js
* User: Masterplan
* Date: 4/22/13
* Time: 5:07 PM
* Desc: Shows and edits exams info, status and password
*/

var examEditing = false;
var examRowSelected = null;
var examRowEdit = null;
var examNew = false;
var altezza = $(window).height()-400;
if(altezza<250){
    altezza=250;
}
var examsTable = null;
var etci = {
    status : 0,
    day : 1,
    time : 2,
    subject : 3,
    name : 4,
    settings : 5,
    numStudents : 6,
    subgroupinfo : 7,
    password : 8,
    manage : 9,
    examID : 10,
    subjectID : 11,
    settingsID : 12,
    statusID : 13
};

statuses = {"w" : {"imageTitle"  : "Waiting",
                   "actionTitle" : "Start",
                   "action"      : "start",
                   "confirm"     : ttCStartExam,
                   "newStatus"   : "s",
                   "message"     : ttMExamStarted},
            "s" : {"imageTitle"  : "Started",
                   "actionTitle" : "Stop",
                   "action"      : "stop",
                   "confirm"     : ttCStopExam,
                   "newStatus"   : "e",
                   "message"     : ttMExamStopped},
            "e" : {"imageTitle"  : "Stopped",
                   "actionTitle" : "Start",
                   "action"      : "start",
                   "confirm"     : ttCStartExam,
                   "newStatus"   : "s",
                   "message"     : ttMExamStarted}
            };

$(function(){

    /**
     *  @descr  Binded event for New button
     */
    $("#newExam").on("click", function(){ newExam(); });

    examsTable = $("#examsTable").DataTable({
        "paging":true,
        "pageLength": 30,
        "processing": true,
        deferRender:    true,
        "lengthChange": false,
        data:dataset,
        scrollY:        altezza,
        scrollCollapse: false,
        jQueryUI:       true,
        order: [ [etci.day, "desc"], [etci.time, "desc"] ],
        columns : [
            { className: "eStatus", searchable : false, type: "alt-string", width : "10px", sortable : false },
            { className: "eDay", type: "date-eu", width: "60px"},
            { className: "eTime", width : "30px" },
            { className: "eSubject" },
            { className: "eName" },
            { className: "eSettings" },

            { className: "eNumTests", searchable : false},
            { className: "eGroup" },

            { className: "ePassword", width : "30px", sortable : false },
            { className: "eManage", searchable : false, sortable : false },
            { className: "eExamID", searchable : false,visible : false },
            { className: "eSubjectID", searchable : false,visible : false },
            { className: "eSettingsID", searchable : false,visible : false },
            { className: "eStatusID", searchable : false,visible : false }
        ],
        language : {
            info: ttDTExamInfo,
            infoFiltered: ttDTExamFiltered,
            infoEmpty: ttDTExamEmpty
        }
    });
    $("#examsTable_filter").css("margin-right", "50px")
                           .after($("#newExam").parent())
                           .before($("#examsTable_info"));
});

/**
 *  @name   showExamInfo
 *  @descr  Shows requested exam's informations
 *  @param  selected            DOM Element             Selected exam
 */
function showExamInfo(selected){
    $("#examsTable tr").removeClass("selected");
    $(selected).closest("tr").addClass("selected");
    examRowEdit = $(selected).closest("tr");
    var idExam = examsTable.row(examRowEdit).data()[etci.examID];
    $.ajax({
        url     : "index.php?page=exam/showexaminfo",
        type    : "post",
        data    : {
            idExam  :   idExam,
            action  :   "show"
        },
        success : function (data){
            if(data == "NACK"){
//                alert(data);
            }else{
//                alert(data);
                $("body").append(data);
                newLightbox($("#examInfo"), {});
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   showStudentsList
 *  @descr  Shows requested exam's students list
 *  @param  selected            DOM Element             Selected exam
 */
function showStudentsList(selected,idExam=null){
    if(idExam==null){
	    if(selected == null){
	        selected = $("#examsTable tr.selected");
	    }else{
	        $("#examsTable tr").removeClass("selected");
        	$(selected).closest("tr").addClass("selected");
	    }
	    examRowEdit = $(selected).closest("tr");
	    var idExam = examsTable.row(examRowEdit).data()[etci.examID];
    }
    $.ajax({
        url     : "index.php?page=exam/showregistrationslist",
        type    : "post",
        data    : {
            idExam  :   idExam
        },
        success : function (data){
            if(data == "NACK"){
//                alert(data);
            }else{
//                alert(data);
                $("body").append(data);
                newLightbox($("#registrationsList"), {});
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   newExam
 *  @descr  Shows empty panel to add new exam
 */
function newExam(){
    $("#examsTable tr").removeClass("selected");
    $.ajax({
        url     : "index.php?page=exam/showexaminfo",
        type    : "post",
        data    : {
            idExam  :   "none",
            action  :   "new"
        },
        success : function (data){
            if(data == "NACK"){
//                alert(data);
            }else{
//                alert(data);
                $("body").append(data);
                newLightbox($("#examInfo"), {});
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   archiveExam
 *  @descr  Archives requested exam
 *  @param  askConfirmationAndExamToArchive         Array        askConfirmation Boolean, img of exam to archive
 */
function archiveExam(askConfirmationAndExamToArchive){
    if((!askConfirmationAndExamToArchive[0]) || (confirmDialog(ttWarning, ttCArchiveExam, archiveExam, new Array(false, askConfirmationAndExamToArchive[1])))){
        examRowEdit = $(askConfirmationAndExamToArchive[1]).closest("tr");
        var idExam = examsTable.row(examRowEdit).data()[etci.examID];
        $.ajax({
            url     : "index.php?page=exam/archiveexam",
            type    : "post",
            data    : {
                idExam      :   idExam
            },
            success : function (data) {
                if(data.trim() == "ACK"){
//                    alert(data);
                    var newStatus = status["newStatus"];
                    examsTable.cell(examsTable.row(examRowEdit).index(), etci.status).data(
                        '<img alt="'+ttArchived+'"' +
                        '     title="'+ttArchived+'"' +
                        '     src="'+imageDir+'Archive.png">');
                    examsTable.cell(examsTable.row(examRowEdit).index(), etci.statusID).data('a');
                    examRowEdit.find("span.manageButton.action img").remove();
                    examRowEdit.find("span.manageButton.archive img").remove();
                    showSuccessMessage(ttMExamArchived);
		    resultStudent(0);
                }else{
//                    alert(data);
                    errorDialog(ttError, data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   deleteExam
 *  @descr  Deletes requested exam
 *  @param  askConfirmationAndExamToDelete      Array        askConfirmation Boolean, img of exam to delete
 */
function deleteExam(askConfirmationAndExamToDelete){
    if((!askConfirmationAndExamToDelete[0]) || (confirmDialog(ttWarning, ttCDeleteExam, deleteExam, new Array(false, askConfirmationAndExamToDelete[1])))){
        examRowEdit = $(askConfirmationAndExamToDelete[1]).closest("tr");
        var idExam = examsTable.row(examRowEdit).data()[etci.examID];
        $.ajax({
            url     : "index.php?page=exam/deleteexam",
            type    : "post",
            data    : {
                idExam      :   idExam
            },
            success : function (data) {
                if(data == "ACK"){
//                    alert(data);
                    examsTable.row(examRowEdit).remove().draw();
                    examRowEdit = null;
                    showSuccessMessage(ttMExamDeleted);
                }else{
//                    alert(data);
                    errorDialog(ttError, data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   changeExamStatus
 *  @param  askConfirmationAndExamToChange            Array        askConfirmation Boolean, img of exam to change status
 *  @descr  Changes exam's status
 */
function changeExamStatus(askConfirmationAndExamToChange){
    examRowEdit = $(askConfirmationAndExamToChange[1]).closest("tr");
    var status = statuses[examsTable.row(examRowEdit).data()[etci.statusID]];
    if((!askConfirmationAndExamToChange[0]) || (confirmDialog(ttWarning, status["confirm"], changeExamStatus, new Array(false, askConfirmationAndExamToChange[1])))){
        var idExam = examsTable.row(examRowEdit).data()[etci.examID];
        $.ajax({
            url     : "index.php?page=exam/changestatus",
            type    : "post",
            data    : {
                idExam      :   idExam,
                action      :   status["action"]
            },
            success : function (data) {
                if(data.trim() == "ACK"){
//                    alert(data);
                    var newStatus = status["newStatus"];
                    examsTable.cell(examsTable.row(examRowEdit).index(), etci.status).data(
                        '<img alt="'+statuses[newStatus]["imageTitle"]+'"' +
                        '     title="'+statuses[newStatus]["imageTitle"]+'"' +
                        '     src="'+imageDir+statuses[newStatus]["imageTitle"]+'.png">');
                    examsTable.cell(examsTable.row(examRowEdit).index(), etci.statusID).data(newStatus);
                    examRowEdit.find("span.manageButton.action img").attr("src", imageDir+statuses[newStatus]["actionTitle"]+".png")
                                                                    .attr("title", statuses[newStatus]["actionTitle"]);
                    showSuccessMessage(status["message"]);
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
function helpjs(){

    $("#dialogError p").html(ttHelpExams);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}

function resultStudent(click=0){
    var idExam = examsTable.row(examRowEdit).data()[etci.examID];
    var subject = examsTable.row(examRowEdit).data()[etci.name];
    var examDate = examsTable.row(examRowEdit).data()[etci.day];
    $.ajax({
        url     : "index.php?page=report/resultstudent",
        type    : "post",
        dataType: 'json',
        data    : {
            idExam: idExam,
            subject:subject,
            date: examDate
        },
        success : function (data){
            if(data[0] == "success"){
                if(click==1){
                    var path = data[1];
                    var link = document.createElement("a");
                    link.download = subject+"_"+examDate+".csv";
                    link.target = "_blank";
                    link.href = path;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    delete link;
                }
            }else{
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}


function resultExams(){
    $.ajax({
        url     : "index.php?page=report/resultsexams",
        type    : "post",        
        dataType: 'json',
        success : function (data){
            if(data[0] == "success"){
                var path = data[1];
                var link = document.createElement("a");
                link.download = "report"+".csv";
                link.target = "_blank";
                link.href = path;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                delete link;
            }else{
                showErrorMessage(data);
                console.log(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
