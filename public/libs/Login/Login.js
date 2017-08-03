/**
 * File: Login.js
 * User: Masterplan
 * Date: 3/15/13
 * Time: 7:32 PM
 * Desc: Javascript library for login module
 */

$(document).ready(function () {
    $("#email").focus();

    /**
     *  @descr  Binded event for ENTER key on fields
     */
    $("#email, #password").on("keydown", function(event) {
        if(event.which == 13)
            logIn(null);
    });
});


/**
 *  @descr  Binded login function
 */
function logIn() {
    if(($("#email").val() == "") || ($("#password").val() == "")){
        $("#result").text(ttELogin);
    }else{
        $("#result").text(ttLogin);
        $.ajax({
            url     : "index.php?page=login/login",
            type    : "post",
            data    :{
                email       :   $("#email").val(),
                password    :   $("#password").val()
            },
            success : function (data, status) {
                if(data == "ACK"){
                    //alert(data);
                    $("#result").text(ttLoginOk);
                    location.href = "index.php";
                }else{
                    //alert(data);
                    $("#result").text(ttELogin);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}
