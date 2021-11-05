/**
 *  @name   sendCoachingReport
 *  @descr  Sends data to create Coaching report pdf
 */
function sendCoachingReport(idtest) {
    $.ajax({
        url: "index.php?page=report/coachingtestparam",
        type: "post",
        data: {
            idTest: idtest,
        },
        success: function () {
            window.location.assign("index.php?page=report/coachingresult")
        },
        error: function (request, status, error) {
            alert(error);
        }
    });
}

function helpjs() {
    $("#dialogError p").html(ttHelpReportCreportlist);
    $("#dialogError").dialog("option", "title", ttHelpDefault)
        .dialog("open");
    $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}