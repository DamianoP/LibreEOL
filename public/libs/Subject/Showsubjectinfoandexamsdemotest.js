/**
 * File: Showsubjectinfoandexams.js
 * User: Masterplan
 * Date: 10/07/14
 * Time: 12:49
 * Desc: Show informations abount requested subject with list of all available exams
 */

var examsAvailableTable = null;
var eatci = {
  status: 0,
  day: 1,
  time: 2,
  name: 3,
  regEnd: 4,
  manage: 5,
  examInfo: 6,
  examID: 7
};

/**
 *  @name   showExamInfo
 *  @descr  Shows info about requested exam
 *  @param  selected            DOM Element             <img> of selected subject
 */
function showExamInfo(selected) {
  var tr = $(selected).closest("tr");
  var row = examsAvailableTable.row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass("shown");
  } else {
    var cls = "odd";
    if (tr.hasClass("even"))
      cls = "even"
    row.child('<div class="center tSpace bSpace italic">' + row.data()[eatci.examInfo] + '</div>').show();
    row.child().addClass(cls);
    tr.addClass("shown");
  }
}

/**
 *  @name   register
 *  @descr  Registers student to requested exam
 *  @param  askConfirmationAndSelectedExam          Array           Array contains boolean for ask confirmation and register <img> of selected exam
 */
function register(askConfirmationAndSelectedExam) {
  if ((!askConfirmationAndSelectedExam[0]) || (confirmDialog(ttWarning, ttCRegister, register, new Array(false, askConfirmationAndSelectedExam[1])))) {
    var row = $(askConfirmationAndSelectedExam[1]).closest("tr");
    var idExam = examsAvailableTable.row(row).data()[eatci.examID];
    $.ajax({
      url: "index.php?page=student/register",
      type: "post",
      data: {
        idExam: idExam
      },
      success: function (data) {
        if (data == "ACK") {
          //                    alert(data);
          showSuccessMessage(ttMRegistration);
          setTimeout(function () {
            showSubjectInfoAndExams(subjectRowSelected);
          }, 1000);
        } else {
          showErrorMessage(data);
        }
      },
      error: function (request, status, error) {
        alert("jQuery AJAX request error:".error);
      }
    });
  }
}

/**
 *  @name   startTest
 *  @descr  Starts requested test
 *  @param  askConfirmationAndSelectedExam          Array           Array contains boolean for ask confirmation and register <img> of selected exam
 */
function startTest(file) {
  $.redirect('index.php?page=student/dodemotest', { 'file': file });
}
