/**
 * Created by tomma on 03/12/2016.
 */
var groupRowSelected = null;

/**
 *  @descr  Binded event for showInfo List
 */
$(function(){
    $("#groupInfo .boxContent").hide();

});

function showGroupInfo(selectedGroup) {
    groupRowSelected = selectedGroup;
    $(".showGroupInfo").removeClass("selected");
    $(groupRowSelected).addClass("selected");
    $.ajax({
        url     : "index.php?page=admin/showgroupinfo",
        type    : "post",
        data    : {
            action       : "show",
            type         : "g",
            idGroup    : $(groupRowSelected).attr("value")
        },
        success : function (data) {
            if(data == "NACK"){
//                    alert(data);

            }else{
//                    alert(data);

                $("#groupInfo .infoEdit").html(data);
                $("#editPanel").show();
                $("#infoName").focus();
                $("#groupInfo .boxContent").slideDown();

            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

function showSubgroupInfo(selectedGroup) {
    groupRowSelected = selectedGroup;
    $(".showGroupInfo").removeClass("selected");
    $(groupRowSelected).addClass("selected");
    $.ajax({
        url     : "index.php?page=admin/showgroupinfo",
        type    : "post",
        data    : {
            action       : "show",
            type         : "s",
            idSubGroup   : $(groupRowSelected).attr("value")

        },
        success : function (data) {
            if(data == "NACK"){
//                    alert(data);

            }else{
//                    alert(data);

                $("#groupInfo .infoEdit").html(data);
                $("#editPanel").show();
                $("#infoName").focus();
                $("#groupInfo .boxContent").slideDown();

            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
function saveGroupEdit(){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else{
        var idGroup = $(groupRowSelected).attr("value");
        var groupName = $("#groupName").val();
        if((idGroup != '')&&(groupName != '')){
            $.ajax({
                url     : "index.php?page=admin/updategroupinfo",
                type    : "post",
                data    : {
                    idGroup     :  idGroup,
                    groupName   :  groupName
                },
                success : function (data) {

                    showSuccessMessage(ttMEdit);
                    $(groupRowSelected).text(groupName);
                    cancelEdit(false);
                }
            });
        }else showErrorMessage(ttEEmptyFields);
    }
}

function saveSubgroupEdit(){
    if($(".overlimit").length > 0)
        showErrorMessage(ttEIncorrectField);
    else{
        var idSubgroup = $(groupRowSelected).attr("value");
        var subgroupName = $("#subgroupName").val();
        var fkGroup = $( "#group option:selected" ).val();
        if((idSubgroup != '')&&(subgroupName != '')&&(fkGroup != '')){
            $.ajax({
                url     : "index.php?page=admin/updatesubgroupinfo",
                type    : "post",
                data    : {
                    idSubgroup  : idSubgroup,
                    subgroupName: subgroupName,
                    fkGroup     : fkGroup
                },
                success : function (data) {

                    showSuccessMessage(ttMEdit);
                    location.reload();
                    cancelEdit(false);

                }
            });
        }else showErrorMessage(ttEEmptyFields);
    }
}
/**
 *  @name   cancelEdit
 *  @descr  Goes back to original group's info
 */
function cancelEdit(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, cancelEdit, false))){
        subjectEditing = false;
        $("#selectPanel").show();
        var type = $(groupRowSelected).attr("name");
        if(type == "group" ){
            showGroupInfo(groupRowSelected);
        }else{
            showSubgroupInfo(groupRowSelected);
        }

    }
}
