/**
 * File: Newstudent.js
 * User: Masterplan
 * Date: 5/29/13
 * Time: 3:43 PM
 * Desc: Creates a new student from added information
 */

/**
 *  @name   createStudent
 *  @descr  Creates student from added informations
 */
function createStudent(){
    var name = $("#userName").val().trim();
    var surname = $("#userSurname").val().trim();
    var email = $("#userEmail").val().trim();
    var email2 = $("#userEmail2").val().trim();
    var str = $( "#group option:selected" ).val();
    var ris = str.split('-');
    var group = ris[0];
    var subgroup = ris[1];
    var password = "_";
    var password2 = "_";
    if($("#userPassword").length > 0){
        password = $("#userPassword").val().trim();
        password2 = $("#userPassword2").val().trim();
    }
    if((name != '') && (surname != '') && (email != '') && (email2 != '') && (password != '') && (password2 != '') && (group != '') && (subgroup != '')){
        if(email == email2){
            if(isValidEmailAddress(email)){
                if(password == password2){
                    if(($("#userPassword").length == 0) || (password.length >= 8)){
                        $.ajax({
                            url     : "index.php?page=admin/newstudent",
                            type    : "post",
                            data    : {
                                name        :  name,
                                surname     :  surname,
                                email       :  email,
                                password    :  password,
                                group       :  group,
                                subgroup    :  subgroup
                            },
                            success : function (data) {
                                data = data.split(ajaxSeparator);
                                if(data[0] == "ACK"){
//                                    alert(data);
                                    showSuccessMessage(ttMUserCreated);
                                    setTimeout(function(){ window.location = 'index.php'; }, 2000);
                                }else showErrorMessage(data);
                            },
                            error : function (request, status, error) {
                                alert("jQuery AJAX request error:".error);
                            }
                        });
                    }else showErrorMessage(ttEPasswordShort);
                }else showErrorMessage(ttEPasswordsNotMatch);
            }else showErrorMessage(ttEEmailNotValid);
        }else showErrorMessage(ttEEmailsNotMatch);
    }else showErrorMessage(ttEEmptyFields)
}

function helpjs(){

    $("#dialogError p").html(ttHelpADMINNewStudentDescription);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}