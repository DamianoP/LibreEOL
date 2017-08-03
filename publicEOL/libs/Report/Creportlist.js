/**
 * Created by michele on 19/12/15.
 */
/**
 *  @name   printParticipant
 *  @descr  Shows partecipants in the select form
 */

$(document).ready(function(){
    $.ajax({
        url     : "index.php?page=report/showtestscreport",
        type    : "post",
        data : {

        },
        success : function (data){
            $("#crtests").append(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

});

/**
 *  @name   showCreportDetails
 *  @descr  Send data for create Coaching report pdf
 */
function showCreportDetails() {
    var scoreFinal = $("#crtests tr:hover").find(".scoreFinal").text();
    var dateTaken = $("#crtests tr:hover").find(".dateTaken").text();
    var status = $("#crtests tr:hover").find(".status").text();
    var idTest = $("#crtests tr:hover").attr("id");
    $.ajax({
         url     : "index.php?page=report/loadcreportresult",
         type    : "post",
         data : {
         scoreFinal: scoreFinal,
         dateTaken: dateTaken,
         status: status,
         idTest: idTest
         },
         success : function (data){
             window.location.assign("index.php?page=report/creportpdf")
         },
         error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
         }
    });
}
function helpjs(){
    $("#dialogError p").html(ttHelpReportCreportlist);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}