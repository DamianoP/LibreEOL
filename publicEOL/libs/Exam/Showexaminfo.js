/**
* File: Showexaminfo.js
* User: Masterplan
* Date: 4/22/13
* Time: 5:07 PM
* Desc: Shows and edits exams info, status and password
*/

$(function(){

    $(".readonly").attr("disabled", "");

    /**
     *  @descr  Enables subject exam dropdown
     */
    $("#examSubject dt.writable").on("click", function() {
        $(this).children("span").toggleClass("clicked");
        $(this).next().children("ol").slideToggle(200);
    });
    $("#examSubject dd ol li").on("click", function() {
        updateTestSettingsList($(this));
    });
    $(document).on('click', function(e) {
        var $clicked = $(e.target);
        if(!($clicked.parents().hasClass("dropdownInfo"))){
            $(".dropdownInfo dd ol").slideUp(200);
            $(".dropdownInfo dt span").removeClass("clicked");
        }
    });

    /**
     *  @descr  Enables examDay datepicker
     */
    $("#examDay, #examRegStartDay, #examRegEndDay").datepicker({
        dateFormat: "yy-mm-dd"
    });
    /**
     *  @descr  Enables examTime timepicker
     */
    $("#examTime, #examRegStartTime, #examRegEndTime").timepicker({
        controlType: "select",
        timeFormat: "HH:mm",
        stepMinute: 5
    });
    /**
     *  @descr  Registration mode events
     */
    $("input[type=radio][name=examReg]").on("change", function(){
        if($(this).val() == "manual"){
            $("#examRegiDiv").slideUp(200);
        }else if($(this).val() == "auto"){
            $("#examRegiDiv").slideDown(200);
        }
    });
    /**
     *  @descr  Rooms checkbox auto enable/disable events
     */
    $("#allRooms").on("change", function(){
        checkRooms($(this).is(":checked"));
    });

    /**
     *  @descr  Enables chars counters for examName and examInfo fields
     */
    enableCharsCounter("examName", "examName");
    enableCharsCounter("examDesc", "examDesc");

});

/**
 *  @name   editExamInfo
 *  @descr  Allows edits exam informations
 */
function editExamInfo(){
    makeWritable($("#examName, #examDesc, input[name=examReg], .datepicker, .timepicker, #allRooms, input[name=rooms]"));
    $("#viewPanel").hide();
    $("#editPanel").show();
    checkRooms($("#allRooms").is(":checked"));
    examEditing = true;
}

/**
 *  @name   saveExamInfo
 *  @descr  Saves exam's informations
 */
function saveExamInfo(){
    var error = false;
    var idExam = examsTable.row(examRowEdit).data()[etci.examID];
    var name = $("#examName").val().trim();
    var day = $("#examDay").val().trim();
    var time = $("#examTime").val().trim();
    var date =  day + " " + time;
    var desc = $("#examDesc").val().trim();
    var registration = $("input[name=examReg]:checked").val();
    var regStartDate = "";
    var regEndDate = "";
    switch(registration){
        case "auto" :
            regStartDate = "'"+$("#examRegStartDay").val()+" "+$("#examRegStartTime").val()+"'";
            regEndDate = "'"+$("#examRegEndDay").val()+" "+$("#examRegEndTime").val()+"'";
            break;
        case "manual" :
            regStartDate = "NULL";
            regEndDate = "NULL";
            break;
//        case "open" : break;              // Coming Soon
    }
    var rooms = new Array();
    if(!$("#allRooms").is(":checked")){
        $("input[name=rooms]:checked:not([disabled])").each(function(index, input){
            rooms.push($(input).val());
        });
    }
    if((registration == "auto") && ((new Date(date) < new Date(regEndDate)) || (new Date(regEndDate) < new Date(regStartDate)))){
        error = true;
        showErrorMessage(ttEDates);
    }else if(($(".overlimit").length > 0) || (name == "") || (day == "") || (time == "")){
        error = true;
        showErrorMessage(ttEIncorrectField);
    }
    if(!error){
        $.ajax({
            url     : "index.php?page=exam/updateexaminfo",
            type    : "post",
            data    : {
                idExam          :   idExam,
                name            :   name,
                datetime        :   date,
                desc            :   desc,
                regStart        :   regStartDate,
                regEnd          :   regEndDate,
                rooms           :   JSON.stringify(rooms)
            },
            success : function (data){
//                alert(data);
                data = data.trim().split(ajaxSeparator);
                if(data.length > 1){
                    var examInfo = JSON.parse(data[1]);
                    examsTable.row(examRowEdit).data(examInfo).draw();
                    showSuccessMessage(ttMEdit);
                    examEditing = false;
                    cancelEdit(new Array(false));
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
 *  @name   createNewExam
 *  @descr  Creates new exam with filled informations
 */
function createNewExam(){
    var error = false;
    var name = $("#examName").val().trim();
    var idSubject = $("#examSubject dt span.value").text();
    var idTestSetting = $("#examSettings dt span.value").text();
    var day = $("#examDay").val().trim();
    var time = $("#examTime").val().trim();
    var date =  day + " " + time;
    var desc = $("#examDesc").val().trim();
    var registration = $("input[name=examReg]:checked").val();
    var regStartDate = "";
    var regEndDate = "";
    switch(registration){
        case "auto" :
            regStartDate = "'"+$("#examRegStartDay").val()+" "+$("#examRegStartTime").val()+"'";
            regEndDate = "'"+$("#examRegEndDay").val()+" "+$("#examRegEndTime").val()+"'";
            break;
        case "manual" :
            regStartDate = "NULL";
            regEndDate = "NULL";
            break;
//        case "open" : break;              // Coming Soon
    }
    var rooms = new Array();
    if(!$("#allRooms").is(":checked")){
        $("input[name=rooms]:checked:not([disabled])").each(function(index, input){
            rooms.push($(input).val());
        });
    }
    if((registration == "auto") && ((new Date(date) < new Date(regEndDate)) || (new Date(regEndDate) < new Date(regStartDate)))){
        error = true;
        showErrorMessage(ttEDates);
    }else if(idSubject == "-1"){
        error = true;
        showErrorMessage(ttESelectSubject);
    }else if(idTestSetting == ""){
        error = true;
        showErrorMessage(ttESelectTestSettings);
    }else if(($(".overlimit").length > 0) || (name == "") || (day == "") || (time == "")){
        error = true;
        showErrorMessage(ttEIncorrectField);
    }
    if(!error){
        $.ajax({
            url     : "index.php?page=exam/newexam",
            type    : "post",
            data    : {
                name            :   name,
                idSubject       :   idSubject,
                idTestSettings  :   idTestSetting,
                datetime        :   date,
                desc            :   desc,
                regStart        :   regStartDate,
                regEnd          :   regEndDate,
                rooms           :   JSON.stringify(rooms)
            },
            success : function (data){
//                alert(data);
                data = data.trim().split(ajaxSeparator);
                if(data.length > 1){
                    var examInfo = JSON.parse(data[1]);
                    examsTable.row.add(examInfo).draw();
                    showSuccessMessage(ttMEdit);
                    examEditing = false;
                    cancelEdit(new Array(false));
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
 *  @name   renewPassword
 *  @param  askConfirmation         Array       If askConfirmation[0] is true show confirm dialog
 *  @descr  Updates exam's password
 */
function renewPassword(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCRenewPassword, renewPassword, new Array(false)))){
        var idExam = examsTable.row(examRowEdit).data()[etci.examID];
        $.ajax({
            url     : "index.php?page=exam/updateexaminfo",
            type    : "post",
            data    : {
                password  :   "password",
                idExam    :   idExam
            },
            success : function (data){
                data = data.split(ajaxSeparator);
                if(data[0] == "ACK"){
//                    alert(data);
                    examsTable.cell(examsTable.row(examRowEdit).index(), etci.password).data(data[1]).draw();
                    showSuccessMessage(ttMPasswordRenewed);
                    cancelEdit(new Array(false));
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
 *  @name   updateTestSettingsList
 *  @descr  Updates test setting's list on exam's subject update
 */
function updateTestSettingsList(selected){
    $.ajax({
        url     : "index.php?page=exam/testsettingslist",
        type    : "post",
        data    : {
            idSubject   :   selected.find("span.value").text()
        },
        success : function (data) {
//            alert(data);
            $("#examSettings").html(data);
            updateDropdown(selected);
            $("#examSettings dt").on("click", function() {
                $(this).children("span").toggleClass("clicked");
                $(this).next().children("ol").slideToggle(200);
            });
            $("#examSettings dd ol li").on("click", function() {
                updateDropdown($(this));
            });
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   checkRooms
 *  @descr  Checks rooms selections
 *  @param  isChecked           Boolean         If true disable every others room checkbox
 */
function checkRooms(isChecked){
    if(isChecked){
        $("input[name=rooms]").attr("disabled", "");
    }else{
        $("input[name=rooms]").removeAttr("disabled");
    }
}

/**
 *  @name   cancelEdit
 *  @descr  Close exam's informations after confirm dialog preventing to loose changes
 *  @param  askConfirmation         Array           If askConfirmation[0] is true display a confirmation dialog
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, new Array(false)))){
        closeLightbox($('#examInfo'));
        examEditing = false;
        examRowEdit = null;
    }
}

/**
 *  @name   cancelNew
 *  @descr  Closes new exam's information panel after confirm dialog
 *  @param  askConfirmation         Array       If [0] is true display a confirmation dialog
 */
function cancelNew(askConfirmation){
    if(((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNew, new Array(false))))){
        cancelEdit(new Array(false));
    }
}