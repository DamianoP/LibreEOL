/**
 * File: Newroom
 * User: Masterplan
 * Date: 6/10/13
 * Time: 1:55 PM
 * Desc: Creates a new room from added informations
 */

$(function(){

    /**
     *  @descr  Binded event to cancel create operation
     */
    $(".showInfo, #cancelNew").on("click", function (event) {
        confirmDialog(ttWarning, ttCDiscardNew, function(){location.href = "index.php?page=admin/rooms"}, null)
    });

    /**
     *  @descr  Enable chars counter for name and description fields
     */
    enableCharsCounter("infoName", "roomName");
    enableCharsCounter("infoDesc", "roomDesc");

});

/**
 *  @descr  Binded function for Create button
 */
function createRoom(event){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else{
        var name = $("#infoName").val();
        var desc = $("#infoDesc").val();
        if(name != ""){
            var error = false;
            var ipStart = "";
            var ipEnd = "";
            $("input[id^='infoIPStart']").each(function(index, input){
                ipStart += "." + $(input).val();
                if(($(input).val() == "") || (isNaN($(input).val())) || ($(input).val() < 0 ) || ($(input).val() > 255)){
                    error = true;
                }
            });
            $("input[id^='infoIPEnd']").each(function(index, input){
                ipEnd += "." + $(input).val();
                if(($(input).val() == "") || (isNaN($(input).val())) || ($(input).val() < 0 ) || ($(input).val() > 255)){
                    error = true;
                }
            });
            if(!error){
                $.ajax({
                    url     : "index.php?page=admin/newroom",
                    type    : "post",
                    data    : {
                        name     : name,
                        desc     : desc,
                        ipStart  : ipStart.substring(1),    // Remove first dot
                        ipEnd    : ipEnd.substring(1)       // Remove first dot
                    },
                    success : function (data, status) {
                        if(data == "ACK"){
                            //alert(data);
                            showSuccessMessage(ttMNewRoom);
                            setTimeout(function(){location.href = "index.php?page=admin/rooms"}, 1500);
                        }else{
                            showErrorMessage(data);
                        }
                    },
                    error : function (request, status, error) {
                        alert("jQuery AJAX request error:".error);
                    }
                });
            }else{
                showErrorMessage(ttEIncorrectField);
            }
        }else{
            showErrorMessage(ttEEmptyFields);
        }
    }
}