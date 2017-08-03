/**
 * File: Setpassword.js
 * User: Masterplan
 * Date: 6/7/13
 * Time: 1:03 PM
 * Desc: Shows page to insert the first password and activate user's account or sets a new password after reset operation
 */

/**
 *  @name   setPassword
 *  @descr  Set new account's password
 */
function setPassword(){
    var password = $("#infoPassword").val().trim();
    var password2 = $("#infoPassword2").val().trim();
    if(password == password2){
        if(password.length >= 8){
            $.ajax({
                url     : "index.php?page=admin/setpassword",
                type    : "post",
                data    : {
                    token       :   $(".infoEdit").attr("id"),
                    password    :   password
                },
                success : function (data, status) {
                    if(data == "ACK"){
//                        alert(data);
                        showSuccessMessage(ttMActivation);
                        setTimeout(function(){ window.location = 'index.php'; }, 2000);
                    }else showErrorMessage(data);
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else showErrorMessage(ttEPasswordShort);
    }else showErrorMessage(ttEPasswordsNotMatch);
}