/**
 * File: Rooms.js    // Fixme Write again!!!!
 * User: Masterplan
 * Date: 6/10/13
 * Time: 9:21 AM
 * Desc: Shows rooms edit page
 */

/**
 *  @descr  Binded event for showInfo List
 */
$(function(){
    $("#roomInfo .boxContent").hide();

    /**
     *  @descr  Binded event to show info about a room
     */
    $(".showInfo[value]").on("click", function (event) {
        if( (!(isEditing())) || ((isEditing()) && (confirmDialog(ttWarning, ttCDiscardEdits, anotherInfo, new Array($(this)))))){
            anotherInfo(new Array($(this)));
        }
    });

    /**
     *  @descr  Binded event for New button
     */
    $("#new").on("click", function(event){
        location.href = "index.php?page=admin/newroom";
    });

    /**
     *  @descr  Enable chars counters for name and description fields
     */
    enableCharsCounter("infoName", "roomName");
    enableCharsCounter("infoDesc", "roomDesc");

});

/**
 *  @descr  Show info binded function
 */
function anotherInfo(selected){
    $(".showInfo").removeClass("selected");
    selected[0].addClass("selected");
    showInfo(null)
}

/**
 *  @descr  Show info binded function
 */
function showInfo(event) {
    $.ajax({
        url     : "index.php?page=admin/showroominfo",
        type    : "post",
        data    : {
            idRoom    :    $(".selected").attr('value')
        },
        success : function (data) {
            if(data == "NACK"){
//                alert(data);
                showErrorMessage("Error...");
            }else{
//                alert(data);
                var infos = JSON.parse(data);                   // Parse JSON data writed by PHP function
                var ipStart = infos[3].split('.');
                var ipEnd = infos[4].split('.');
                if(isEditing())
                    cancelEdit(event);                  // Delete all buttons and fields added for Edit function
                                                        // If event = "saved" then this function was called after Save function successfully done
                                                        // so don't replace subject's name in left list
                $("#infoName").val(infos[1]);
                $("#infoDesc").val(infos[2]);
                $("#infoIPStart0").val(ipStart[0]);
                $("#infoIPStart1").val(ipStart[1]);
                $("#infoIPStart2").val(ipStart[2]);
                $("#infoIPStart3").val(ipStart[3]);
                $("#infoIPEnd0").val(ipEnd[0]);
                $("#infoIPEnd1").val(ipEnd[1]);
                $("#infoIPEnd2").val(ipEnd[2]);
                $("#infoIPEnd3").val(ipEnd[3]);

                $("#roomInfo .boxContent").slideDown({
                    duration : 400
                });
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @descr  Binded editInfo function
 */
function editInfo(event){
    infos = { "selected"    :   $(".selected"),
        "name"      :   $("#infoName").val(),
        "desc"      :   $("#infoDesc").val(),
        "start0"    :   $("#infoIPStart0").val(),
        "start1"    :   $("#infoIPStart1").val(),
        "start2"    :   $("#infoIPStart2").val(),
        "start3"    :   $("#infoIPStart3").val(),
        "end0"      :   $("#infoIPEnd0").val(),
        "end1"      :   $("#infoIPEnd1").val(),
        "end2"      :   $("#infoIPEnd2").val(),
        "end3"      :   $("#infoIPEnd3").val()
    };
    makeWritable($("#infoName, #infoDesc, input[id^='infoIP']"));
//    Create Save and Cancel buttons and delete Edit button
    var cancel = $("<a></a>").addClass("normal button right")
        .attr("id", "cancelEdit")
        .html(ttCancel)
        .on("click", function(event){ confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit) });
    var save = $("<a></a>").addClass("ok button right lSpace")
        .attr("id", "saveEdit")
        .html(ttSave)
        .on("click", saveEdit);
    var del = $("<a></a>").addClass("delete button left lSpace")
        .attr("id", "deleteRoom")
        .html(ttDelete)
        .on("click", function(event){ confirmDialog(ttWarning, ttCDeleteRoom, deleteRoom) });
    $("#editInfo").after(del).after(cancel).after(save).remove();
    $("#infoName").focus();
}

/**
 *  @descr  Binded function for Delete button
 */
function deleteRoom(){
    $.ajax({
        url     : "index.php?page=admin/deleteroom",
        type    : "post",
        data    : {
            idRoom    : $(".selected").attr('value')
        },
        success : function (data) {
            if(data == "ACK"){
                //alert(data);
                showSuccessMessage(ttMRoomDeleted);
                setTimeout(function(){location.href = "index.php?page=admin/rooms"}, 1500);
            }else{
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @descr  Binded function for Cancel button
 */
function cancelEdit(event){
    makeReadonly($("#infoName, #infoDesc, input[id^='infoIP']"));
    $("#infoName").val(infos["name"]);                              // Cancel all edits for all fields and re-add Edit button
    if(event != "saved")                                            // If event = "saved" then this function was called after Save function successfully done
        infos["selected"].text(infos["name"]);                      // so don't replace subject's name in left list
    $("#infoDesc").val(infos["desc"]);
    $("#infoIPStart0").val(infos['start0']);
    $("#infoIPStart1").val(infos['start1']);
    $("#infoIPStart2").val(infos['start2']);
    $("#infoIPStart3").val(infos['start3']);
    $("#infoIPEnd0").val(infos['end0']);
    $("#infoIPEnd1").val(infos['end1']);
    $("#infoIPEnd2").val(infos['end2']);
    $("#infoIPEnd3").val(infos['end3']);
    $(".charsCounter").removeClass("overlimit").hide();
    var edit = $("<a></a>").addClass("normal button right")
        .attr("id", "editInfo")
        .html(ttEdit)
        .on("click", editInfo);
    $("#saveEdit").after(edit).remove();
    $("#cancelEdit").remove();
    $("#deleteRoom").remove();
}

/**
 *  @descr  Binded function for Save button
 */
function saveEdit(event){
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
                    url     : "index.php?page=admin/updateroominfo",
                    type    : "post",
                    data    : {
                        idRoom   : $(".selected").attr('value'),
                        name     : name,
                        desc     : desc,
                        ipStart  : ipStart.substring(1),    // Remove first dot
                        ipEnd    : ipEnd.substring(1)       // Remove first dot
                    },
                    success : function (data) {
                        if(data == "ACK"){
                            //alert(data);
                            showSuccessMessage(ttMEdit);
                            setTimeout(function(){showInfo("saved")}, 1500);    // Don't replace subject's name in left list
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

/**
 *  @descr  Function to check if Edit button was pressed
 */
function isEditing(){
    return $("#cancelEdit").length > 0;
}
function helpjs(){

    $("#dialogError p").html(ttHelpADMINRoom);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}