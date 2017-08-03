/**
 * Created by tomma on 01/12/2016.
 */
/**
 * File: Newstudent.js
 * User: Masterplan
 * Date: 5/29/13
 * Time: 3:43 PM
 * Desc: Creates a new student from added information
 */

/**
 *  @name   createStudent
 *  @descr  Creates student from added informations
 */
function createGroup(){
    var group = $("#groupName").val().trim();
    console.log(group);
    if((group != '')){
        $.ajax({
            url     : "index.php?page=admin/addnewgroup",
            type    : "post",
            data    : {
                group        :  group
            },
            success : function (data) {

                if(data == "ACK"){
//                                    alert(data);
                    showSuccessMessage(ttMGroupCreated);
                    setTimeout(function(){ window.location = 'index.php?page=admin/newgroup'; }, 2000);
                }else showErrorMessage(data);
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }else showErrorMessage(ttEEmptyFields);
}
function createSubgroup(){
    var group = $( "#group option:selected" ).val();
    var subgroup = $("#subgroupName").val().trim();
    if((subgroup != '')){
        $.ajax({
            url     : "index.php?page=admin/newsubgroup",
            type    : "post",
            data    : {
                group           :  group,
                subgroup        :  subgroup
            },
            success : function (data) {
                data = data.split(ajaxSeparator);
                if(data[0] == "ACK"){
//                                    alert(data);
                    showSuccessMessage(ttMSubgroupCreated);
                    setTimeout(function(){ window.location = 'index.php?page=admin/newgroup'; }, 2000);
                }else showErrorMessage(data);
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }else showErrorMessage(ttEEmptyFields);
}

function helpjs(){

    $("#dialogError p").html(ttHelpADMINNewStudentDescription);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
        .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}