/**
 * File: Editstudent.js
 * User: tomma
 * Date: 26/10/16
 * Time: 4:49 PM
 * Desc: Shows information about student
 */
var studentRowSelected = null;

/**
 *  @descr  Binded event for showInfo List
 */
$(function(){
    $("#studentsInfo .boxContent").hide();

});

/**
 *  @name   showStudentInfo
 *  @descr  Shows info about requested student
 *  @param  selectedStudent
 */
function showStudentInfo(selectedStudent) {
    studentRowSelected = selectedStudent;
    console.log($(studentRowSelected).attr("value"));
    $(".showStudentInfo").removeClass("selected");
    $(studentRowSelected).addClass("selected");
    $.ajax({
        url     : "index.php?page=admin/showstudentinfo",
        type    : "post",
        data    : {
            action       : "show",
            idStudent    : $(studentRowSelected).attr("value")
        },
        success : function (data) {
            if(data == "NACK"){
//                    alert(data);

            }else{
//                    alert(data);

                $("#studentsInfo .infoEdit").html(data);
                $("#editPanel").show();
                $("#infoName").focus();
                $("#studentsInfo .boxContent").slideDown();

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
    else{
        var idStudent =$("#");
        var name = $("#studentName").val();
        var surname = $("#studentSurname").val();
        var email = $("#studentEmail").val();
        var str = $( "#group option:selected" ).val();
        var ris = str.split('-');
        var group = ris[0];
        var subgroup = ris[1];
        var password = $("#studentPassword").val();
        var role = null ;
        if ($("input[name=studentRole]:checked").val() == "t") {
            role = "t";
        } else if ($("input[name=studentRole]:checked").val() == "at") {
            role = "at"
        }else if ($("input[name=studentRole]:checked").val() == "e") {
            role = "e";
        }else if($("input[name=studentRole]:checked").val() == "er"){
            role = "er";
        }else if($("input[name=studentRole]:checked").val() == "a"){
            role = "a";
        }else{
            role = "s";
        }

        if((name != '') && (surname != '') && (email != '') && (group != '') && (subgroup != '')){
            if(isValidEmailAddress(email)){
                $.ajax({
                    url     : "index.php?page=admin/updatestudentinfo",
                    type    : "post",
                    data    : {
                        idStudent   : $(studentRowSelected).attr("value"),
                        name        :  name,
                        surname     :  surname,
                        email       :  email,
                        group       :  group,
                        subgroup    :  subgroup,
                        role        :  role,
                        password    :  password
                    },
                    success : function (data) {
                        if(data == "ACK"){
                            showSuccessMessage(ttMEdit);
                            $(studentRowSelected).text(surname + " " + name);
                            cancelEdit(false);
                        }else{
                            showErrorMessage(data);
                        }
                    }
                });
            }else showErrorMessage(ttEEmailNotValid);
        }else showErrorMessage(ttEEmptyFields);
    }
}
/**
 *  @name   cancelEdit
 *  @descr  Goes back to original student's info
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, false))){
        subjectEditing = false;
        $("#selectPanel").show();
        showStudentInfo(studentRowSelected);
    }
}

function deleteStudent() {
    confirmDialog(ttWarning, ttConfirmProcedure, executeDelete, false);
    console.log("finito");
}
function executeDelete(){
        $.ajax({
        url     : "index.php?page=admin/deleteuser",
        type    : "post",
        data    : {
           idUser    : $(studentRowSelected).attr("value")
        },
        success : function (data) {
            console.log(data);
            if(data == "ACK"){
                showSuccessMessage(ttUserDeleted);
                setTimeout(function(){location.href = "index.php?page=admin/editstudent"}, 1500);
            }else{
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
