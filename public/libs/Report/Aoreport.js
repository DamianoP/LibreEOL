let exams = new Array(100);//array of selected exams

/**
 *  @name   optionSelected
 *  @descr  Shows the next Select
 */
function optionSelected(option) {
    const next = document.getElementById(option);
    if (next.id === "years") {
        document.getElementById('arsearched_year').value = null;
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
            subject: $("#arsubject").val(),
        },
        success: function (response) {
            $("#arsearched_year").html(response);
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
            subject: $("#arsubject").val(),
            year: $("#arsearched_year").val(),
        },
        success: function (response) {
            $("#arexam").html(response);
        },
        error: function (request, status, error) {
            alert(error);
        }
    });
}

/**
 *  @name   addExam
 *  @descr  Add the selected exam
 */
function addExam() {
    let year = document.getElementById("arsearched_year").value;
    let exam = document.getElementById("arexam").value;
    let selected = document.getElementById("arexam");
    let selectedText = selected.options[selected.selectedIndex].text;
    let found = false;
    for (i = 0; i < exams.length; i++) {
        if (exams[i] === exam) {
            found = true;
        }
    }
    if (!found) {
        for (i = 0; i < exams.length; i++) {
            if (exam !== "" && exams[i] == null) {
                exams[i] = exam;
                $("#selectedexams").append("<option id=\"" + exam.replace(/ /gi, "_") + "\" value=\"" + exam.replace(/ /gi, "_") + "\">" + selectedText + " " + year + "</option>");
                break;
            }
        }
    }
}

/**
 *  @name   addAll
 *  @descr  Add all the exam of the selected year
 */
function addAll() {
    const options = document.getElementById('arexam').options;
    const year = document.getElementById('arsearched_year').value;
    for (i = 0; i < options.length; i++) {
        let found = false;
        for (j = 0; j < exams.length; j++) {
            if (exams[j] === options[i].value) {
                found = true;
            }
        }
        if (!found) {
            for (j = 0; j < exams.length; j++) {
                if (options[i].value !== "" && exams[j] == null) {
                    exams[j] = options[i].value;
                    $("#selectedexams").append("<option id=\"" + options[i].value.replace(/ /gi, "_") + "\" value=\"" + options[i].value.replace(/ /gi, "_") + "\">" + options[i].text + " " + year + "</option>");
                    break;
                }
            }
        }
    }
}

/**
 *  @name   removeExam
 *  @descr  Delete the selected exam
 */
function removeExam() {
    const exam = document.getElementById("selectedexams").value;
    let x = 0;
    if (exams[exams.length] === exam.replace(/_/, " ")) {
        exams[exams.length] = null;
        $("#" + exam.replace(" ", "_")).remove();
        x = 1;
    }
    for (i = 0; i < exams.length - 1; i++) {
        if (exams[i] == exam.replace(/_/gi, " ")) {
            for (r = i; r < exams.length; r++) {
                exams[r] = exams[r + 1];
            }
        }
    }
    if (x == 0) {
        exams[i] = null;
        $("#" + exam.replace(" ", "_")).remove();
    }
}

/**
 *  @name   removeAllExams
 *  @descr  Clear the selected exams box
 */
function removeAllExams() {
    exams = new Array(100);
    $("#selectedexams").html("");
}

/**
 *  @name   unlockFilter
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
 *  @descr  Transfer parameters and move to the next assessment report page
 */
function transferData(min, max) {
    if ($("#selectedexams").text() == "") {
        showErrorMessage(ttAssessmentError);
    } else {
        const minscore = ($("#assessmentScore").is(":checked")) ? min : -1;
        const maxscore = ($("#assessmentScore").is(":checked")) ? max : -1;
        $.ajax({
            url: "index.php?page=report/assessmentparameters",
            type: "post",
            data: {
                exams: exams.filter(Boolean),
                minscore: minscore,
                maxscore: maxscore,
                year: $("#arsearched_year").val()
            },
            success: function () {
                window.location.assign("index.php?page=report/assessmentresult")
            },
            error: function (request, status, error) {
                alert(error);
            }
        });
    }
}

function helpjs() {
    $("#dialogError p").html(ttHelpReportAreport);
    $("#dialogError").dialog("option", "title", ttHelpDefault)
        .dialog("open");
    $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}

