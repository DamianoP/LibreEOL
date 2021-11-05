var allOpened = true;

$(function () {

    $("#showHide").on("click", function () {showHide(null)});

});

/**
 *  @name   showHide
 *  @param  selected            DOM Element             <div> of selected question
 *  @descr  Shows or Hide answers sections
 */
function showHide(selected) {
    if (selected == null) {
        if (allOpened) {
            $(".questionAnswers").slideUp();
            allOpened = false;
        } else {
            $(".questionAnswers").slideDown();
            allOpened = true;
        }
    } else {
        $(selected).parent().find(".questionAnswers").slideToggle();
    }
}

function riposizionaInCorrezione(elemento, left, top) {
    setTimeout(function () {
        var immagine = $(elemento);
        immagine.css("top", top + "px");
        immagine.css("left", left + "px");
    }, 2000);
}

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