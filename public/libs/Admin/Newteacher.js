/**
 * File: Newteacher.js
 * User: Masterplan
 * Date: 5/29/13
 * Time: 3:43 PM
 * Desc: Creates a new teacher or administrator from added informations
 */

$(function(){

    /**
     *  @descr  Binded event for role checkbox
     */
    $("#administratorRole").attr("disabled", "");
    $("input[name=userRole]").on("change", function(event){
        if($("input[name=userRole]:checked").val() == "t" ){
            $("#administratorRole").removeAttr("disabled");
        }else{
            $("#administratorRole").attr("disabled", "");
            $("#administratorRole").prop('checked', false); 
        }
        if($("input[name=userRole]:checked").val() == "e" ){
            $("#administratorRoleExaminer").removeAttr("disabled");
        }else{
            $("#administratorRoleExaminer").attr("disabled", "");
            $("#administratorRoleExaminer").prop('checked', false); 
        }
    });
});

/**
 *  @name   createTeacher
 *  @descr  Creates user from added informations
 */
function createTeacher(){
    var name = $("#userName").val().trim();
    var surname = $("#userSurname").val().trim();
    var email = $("#userEmail").val().trim();
    var email2 = $("#userEmail2").val().trim();
    var str = $( "#group option:selected" ).val();
    var ris = str.split('-');
    var group = ris[0];
    var subgroup = ris[1];
    if((name != '') && (surname != '') && (email != '') && (email2 != '')){
        if(email == email2){
            if(isValidEmailAddress(email)){
                if($("input[name=userRole]:checked").val() == "t"){
                    if($("#administratorRole").is(":checked")){
                        role = "at";
                    }else{
                        role = "t";
                    }
                }
                else if($("input[name=userRole]:checked").val() == "e"){
                    if($("#administratorRoleExaminer").is(":checked")){
                        role = "er";
                    }else{
                        role = "e";
                    }
                }else if($("input[name=userRole]:checked").val() == "a"){
                    role = "a";
                }
                $.ajax({
                    url     : "index.php?page=admin/newteacher",
                    type    : "post",
                    data    : {
                        name        :  name,
                        surname     :  surname,
                        email       :  email,
                        role        :  role,
                        group       :  group,
                        subgroup    :  subgroup
                    },
                    success : function (data, status) {
                        if(data == "ACK"){
//                            alert(data);
                            showSuccessMessage(ttMUserCreated);
                            setTimeout(function(){ window.location = 'index.php'; }, 2000);
                        }else showErrorMessage(data);
                    },
                    error : function (request, status, error) {
                        alert("jQuery AJAX request error:".error);
                    }
                });
            }else showErrorMessage(ttEEmailNotValid);
        }else showErrorMessage(ttEEmailsNotMatch);
    }else showErrorMessage(ttEEmptyFields);
}
function helpjs(){

    $("#dialogError p").html(ttHelpADMINNewTeacherDescription);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}