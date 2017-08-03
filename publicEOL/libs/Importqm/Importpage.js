/**
 * File: ImportQM.js
 * User: Emanuele Gragnoli
 * Date: 08/24/15
 * Time: 7:32 PM
 * Desc: Javascript library for ImportQM module
 */

$(document).ready(function () {
    init();
});

/**
 *  @descr  init function
 */
function init() {
    $.ajax({
        url     : "index.php?page=ImportQM/init",
        type    : "post",
        data    :{
        },
        success : function (data, status) {
            var res=JSON.parse(data);
            $("#ImportMsg").html(res[0]);
            if(res[1].toString()=='true'){
                //ENABLE BUTTONS
                $("#previewImport").on("click",preview);
                $("#importQuestions").on("click",startImport);
            }
            else{
                //DISABLE BUTTONS
                $("#previewImport").on("click",function(){showErrorMessage('FOLDER NOT FOUND')});
                $("#importQuestions").on("click",function(){showErrorMessage('FOLDER NOT FOUND')});

            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @descr prepare function
 */
function preview() {
    $.ajax({
        url     : "index.php?page=ImportQM/preview",
        type    : "post",
        data    :{
        },
        success : function (data, status) {
            $("#ImportMsg").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @descr  stat Import Procedure function
 */
function startImport() {
        $.ajax({
            url     : "index.php?page=ImportQM/import",
            type    : "post",
            data    :{
            },
            success : function (data, status) {
                if(data=="ACK"){
                    showSuccessMessage(ttImportComplete);
                }
                else{
                    showSuccessMessage(ttAImportComplete);
                }
                setTimeout(function(){ location.replace("index.php?page=admin/index") }, 3000);
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });

}
function helpjs(){

    $("#dialogError p").html(ttHelpADMINImport);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}
