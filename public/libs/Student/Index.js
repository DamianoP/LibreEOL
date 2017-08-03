/**
 * File: Index.js
 * User: Masterplan
 * Date: 5/2/13
 * Time: 12:06 PM
 * Desc: Student's Home Page
 */

var subjectRowSelected = null;

/**
 *  @descr  Binded event for showInfo List
 */
$(function(){

    $("#subjectInfoAndExams .boxContent").hide();

});

/**
 *  @name   showSubjectInfoAndExams
 *  @descr  Shows info about requested subject and the list of all active exams
 *  @param  selected            DOM Element             <tr> of selected subject
 */
function showSubjectInfoAndExams(selected){
    $(".showSubjectInfoAndExams").removeClass("selected");
    subjectRowSelected = selected;
    $(subjectRowSelected).addClass("selected");
    $.ajax({
        url     : "index.php?page=subject/showsubjectinfoandexams",
        type    : "post",
        data    : {
            idSubject    : $(subjectRowSelected).attr("value")
        },
        success : function (data) {
            if(data == "NACK"){
//                alert(data);
            }else{
//                alert(data);
                $("#subjectInfoAndExams .infoEdit").html(data);
                $("#subjectInfoAndExams .boxContent").slideDown({
                    complete : function(){
                        examsAvailableTable.draw();
                    }
                });
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}
function helpjs(){
    $("#dialogError p").html(ttHelpStudent);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}
