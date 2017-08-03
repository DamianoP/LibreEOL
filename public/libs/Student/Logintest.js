/**
 * File:
 * User: Masterplan
 * Date: 5/2/13
 * Time: 11:02 PM
 * Desc:
 */

/**
 *  @descr  Binded event for exam password authorization
 */
$(function(){

    $("#password").focus();

    /**
     *  @descr  Binded event for ENTER key on password field
     */
    $("#password").on("keydown", function(event) {
        if(event.which == 13){
	    event.preventDefault();
            startTest();
	}
    });
});

/**
 *  @descr  Binded event for startTest button
 */
function startTest(){
    if($("#password").val() == "") {
        $("#result").text(ttEPassword);
    }
    $("#result").text(ttCheck);
    $.ajax({
        url     : "index.php?page=student/starttest",
        type    : "post",
        data    :{
            idExam    :  $("#idExam").val(),
            password  :  $("#password").val()
        },
        success : function (data){
            if(data == "ACK"){
                alert(ttCTest);
                location.href = "index.php?page=student/test";
            }else{
                //alert(data);
                $("#result").text(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
