
function printReport() {
    var printContents = document.getElementById("print-section").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function helpjs() {
    $("#dialogError p").html(ttHelpReportAreport);
    $("#dialogError").dialog("option", "title", ttHelpDefault)
        .dialog("open");
    $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}