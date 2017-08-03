/**
 * File: Editteacher.js
 * User: tomma
 * Date: 26/10/16
 * Time: 4:49 PM
 * Desc: Shows information about Admin/teacher
 */
var teacherRowSelected = null;

/**
 *  @descr  Binded event for showInfo List
 */
$(document).ready(function(){
    $("#teachersInfo .boxContent").hide();
});

/**
 *  @name   showTeacherInfo
 *  @descr  Shows info about requested teacher
 *  @param  selectedteacher
 */
function showTeacherInfo(selectedteacher) {
    teacherRowSelected = selectedteacher;
    $(".showTeacherInfo").removeClass("selected");
    $(teacherRowSelected).addClass("selected");

    $.ajax({
        url     : "index.php?page=admin/showteacherinfo",
        type    : "post",
        data    : {
            action       : "show",
            idTeacher    : $(teacherRowSelected).attr("value")
        },
        success : function (data) {
            if(data == "NACK"){
//                    alert(data);

            }else{
//                    alert(data);
                $("#teachersInfo .infoEdit").html(data);
                $("#editPanel").show();
                $("#infoName").focus();
                $("#teachersInfo .boxContent").slideDown();

            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}
function saveEdit(){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else {
        var idTeacher = $("#");
        var role = null;
        var name = $("#teacherName").val();
        var surname = $("#teacherSurname").val();
        var email = $("#teacherEmail").val();
        var str = $("#group option:selected").val();
        var ris = str.split('-');
        var group = ris[0];
        var subgroup = ris[1];
        if ($("input[name=teacherRole]:checked").val() == "t") {
            role = "t";
        } else if ($("input[name=teacherRole]:checked").val() == "at") {
            role = "at"
        }else if ($("input[name=teacherRole]:checked").val() == "e") {
            role = "e";
        }else if($("input[name=teacherRole]:checked").val() == "er"){
            role = "er";
        }else if($("input[name=teacherRole]:checked").val() == "a"){
            role = "a";
        }
        if((name != '') && (surname != '') && (email != '') && (group != '') && (subgroup != '')){
            if(isValidEmailAddress(email)){
                $.ajax({
                    url     : "index.php?page=admin/updateteacherinfo",
                    type    : "post",
                    data    : {
                        idTeacher   : $(teacherRowSelected).attr("value"),
                        name        :  name,
                        surname     :  surname,
                        email       :  email,
                        group       :  group,
                        subgroup    :  subgroup,
                        role        :  role
                    },
                    success : function (data) {

                        showSuccessMessage(ttMEdit);
                        $(teacherRowSelected).text(surname + " " + name);
                        cancelEdit(false);

                    },
                    error : function (request, status, error) {
                        alert("jQuery AJAX request error:".error);
                    }
                });
            }else showErrorMessage(ttEEmailNotValid);
        }else showErrorMessage(ttEEmptyFields);
    }
}
/**
 *  @name   cancelEdit
 *  @descr  Goes back to original teacher's info
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, false))){
        subjectEditing = false;
        $("#selectPanel").show();
        showTeacherInfo(teacherRowSelected);
    }
}

function deleteTeacher() {
    confirmDialog(ttWarning, ttConfirmProcedure, executeDelete, false);
}
function executeDelete(){
        $.ajax({
        url     : "index.php?page=admin/deleteuser",
        type    : "post",
        data    : {
            idUser    : $(teacherRowSelected).attr("value")
        },
        success : function (data) {
            if(data == "ACK"){
                showSuccessMessage(ttUserDeleted);
                setTimeout(function(){location.href = "index.php?page=admin/editteacher"}, 1500);
            }else{
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}