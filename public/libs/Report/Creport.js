/**
 *  @name   optionSelected
 *  @descr  Shows the next Select
 */
function optionSelected(option) {
    const next = document.getElementById(option);
    if (next.id === "years") {
        document.getElementById('crsearchedyear').value = null;
    }
    next.hidden = false;
}

/**
 *  @name   getExamYears
 *  @descr  Shows the years in which the exams took place
 */
function getExamYears() {
    $.ajax({
        type: "post",
        url: "index.php?page=report/getexamyears",
        data: {
            subject: $("#crsubject").val(),
        },
        success: function (response) {
            $("#crsearchedyear").html(response);
        },
        error: function (request, status, error) {
            alert(error);
        }
    });
}

/**
 *  @name   showExams
 *  @descr  Shows exams based on subject and year selected
 */
function showExams() {
    $.ajax({
        type: "post",
        url: "index.php?page=report/findexambyyear",
        data: {
            subject: $("#crsubject").val(),
            year: $("#crsearchedyear").val()
        },
        success: function (response) {
            $("#crexam").html(response);
        },
        error: function (request, status, error) {
            alert(error);
        }
    });
}

/**
 *  @name   unlock
 *  @descr  Enables and disables filtering by score
 */
function unlockFilter(el, el1, el2) {
    if (el.checked) {
        el1.disabled = false;
        el2.disabled = false;
    } else {
        el1.disabled = "disabled";
        el2.disabled = "disabled";
    }
}

/**
 *  @name   transferData
 *  @descr  Transfer parameters and move to the next coaching report page
 */
function transferData() {
    if (($("#crexam").val() == null)) {
        showErrorMessage(ttReportCoachingError);
    } else {
        const minscore = ($("#assessmentScore").is(":checked")) ? $("#assessmentMinScore").val() : -1;
        const maxscore = ($("#assessmentScore").is(":checked")) ? $("#assessmentMaxScore").val() : -1;
        $.ajax({
            url: "index.php?page=report/coachingparameters",
            type: "post",
            data: {
                subject: $("#crsubject option:selected").text(),
                exam: $("#crexam").val(),
                examdate: $("#crexam option:selected").text(),
                year: $("#crsearchedyear").val(),
                minscore: minscore,
                maxscore: maxscore
            },
            success: function () {
                window.location.assign("index.php?page=report/coachinglist")
            },
            error: function (request, status, error) {
                alert(error);
            }
        });
    }
}

function helpjs() {
    $("#dialogError p").html(ttHelpReportCreport);
    $("#dialogError").dialog("option", "title", ttHelpDefault)
        .dialog("open");
    $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}