/**
 * File:
 * User: Masterplan
 * Date: 7/9/13
 * Time: 3:00 PM
 * Desc:
 */

$(function(){

    /**
     *  @descr  Binded event for Create button
     */
    $("#createNew").on("click", function (event) {
        var alias = $("#infoAlias").val();
        var description = $("#infoDescription").val();
        if((alias != "") && (description != "")){
            $.ajax({
                url     : "index.php?page=admin/newlanguage",
                type    : "post",
                data    :{
                    alias       :   alias,
                    description :   description
                },
                success : function (data, status) {
                    if(status == "success"){
                        if(data == "ACK"){
                            showMessage("message", ttMLanguageCreated);
                            setTimeout(function(){location.href = "index.php?page=admin/selectlanguage"}, 1500);
                        }else if(data == "0"){
                            errorDialog(ttError, ttETranslationExists);
                        }else{
                            errorDialog(ttError, data);
                        }
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }
    });

    /**
     *  @descr  Binded event for Cancel button
     */
    $("#cancelNew").on("click", function (event) {
        window.location = "index.php?page=admin/selectlanguage";
    });

});