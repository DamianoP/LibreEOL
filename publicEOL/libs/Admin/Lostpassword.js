/**
 * File:
 * User: Masterplan
 * Date: 6/9/13
 * Time: 2:12 PM
 * Desc: Shows form for reset account password
 */

/**
 *  @name reset
 *  @descr  Sends email to server and resets account password
 */
function reset(){
    var email = $("#infoEmail").val().trim();
    if(email != ''){
        $.ajax({
            url     : "index.php?page=admin/lostpassword",
            type    : "post",
            data    : {
                email   :   email
            },
            success : function (data) {
                if(data == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMResetConfirmed);
                    setTimeout(function(){ window.location = 'index.php'; }, 2000);
                }else{
//                    alert(data);
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }else showErrorMessage(ttEEmptyFields);

}