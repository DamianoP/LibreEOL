/**
 * File: Profile.js
 * User: Masterplan
 * Date: 5/30/13
 * Time: 4:49 PM
 * Desc: Shows profile page of user's account and updates user's information
 */

/**
 *  @name   saveProfile
 *  @descr  Save user's informations
 */
function errorEmail(){
    var idquestion = $("#idquestion").val().trim();
    var notes= $("#notes").val().trim();
    if(notes=="") {
        $("#notes").focus();
        showErrorMessage(ttEEmptyFields);
        return;
    }
    $.ajax({
        url     : "index.php?page=admin/erroremail",
        type    : "post",
        data    : {
            idquestion   :   idquestion,
            notes        :   notes
        },
        success : function (data, status) {
            if(data.trim() == "ACK"){
                showSuccessMessage(ttErrorSent);
                setTimeout(function(){location.href = "index.php"}, 2000);
            }else{
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}
function helpjs(){

    $("#dialogError p").html(ttHelpAdminErrorQuestion);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}