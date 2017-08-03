/**
 * File: Test.js
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test function
 */

var countdownPosX = 20;
var calculatorPosX = 200;
var periodicTablePosX = 200;
var risposteFornite=[];

function countdownComplete(){
    alert(ttETimeExpired);
}

$(function(){

    loadCountdownAndExtra();

    /** @descr  Bind event for checkboxes */
    $("input[type='checkbox'] + span").on("click", function(event){
        $(this).prev().prop("checked", !($(this).prev().is(":checked")));
    });

    /** @descr  Bind event for radiobuttons */
    $("input[type='radio'] + span").on("click", function(event){
        $(this).prev().prop("checked", true);
    });

});

/**
 *  @name   submitTest
 *  @param  askConfirmation         Array       If askConfirmation[0] is true display confirma dialog
 *  @descr  Binded function for Submit button
 */
function submitTest(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCSubmitTest, submitTest, new Array(false)))){
        var questionsTest = [];
        var answersTest = [];
        $(".questionTest").each(function(index, div){
            questionsTest.push($(div).attr("value"));
            var answer = getGivenAnswer(this);
            answersTest.push(JSON.stringify(answer));
        });
        var idLang = $(".dropdownSystemLanguage dd ul li").find("span.value").text();
        if(questionsTest.length == answersTest.length){
            $.ajax({
                url     : "index.php?page=student/submittest",
                type    : "post",
                data    : {
                    //idSet : idSetAttuale,
                    questions  :  JSON.stringify(questionsTest),
                    answers    :  JSON.stringify(answersTest),
                    idLang    :  JSON.stringify(idLang),
                    submit     :  "true"
                },
                success : function (data) {
                    if(data.trim() == "ACK"){
                        showSuccessMessage(ttMTestSubmitted);
                        setTimeout(function(){location.href = "index.php?page=student/index"}, 1500);
                    }else{
                        errorDialog(ttError,  data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttEQuestAnswPicker);
        }
    }
}

function controllaCambiamenti(){
    var questionsTest = [];
    var answersTest = [];
    $(".questionTest").each(function(index, div){
        questionsTest.push($(div).attr("value"));
        var answer = getGivenAnswer(this);
        answersTest.push(JSON.stringify(answer));
    });
    if(JSON.stringify(answersTest)==JSON.stringify(risposteFornite)) return;
    risposteFornite=answersTest;
    var idLang = $(".dropdownSystemLanguage dd ul li").find("span.value").text();
    if(questionsTest.length == answersTest.length){
        $.ajax({
            url     : "index.php?page=student/submittest",
            type    : "post",
            data    : {
                //idSet : idSetAttuale,
                questions   :  JSON.stringify(questionsTest),
                answers     :  JSON.stringify(answersTest),
                idLang      :  JSON.stringify(idLang),
                submit      :  "false"
            },
            success : function (data) {
                if(data.trim() == "ACK"){
                    console.log("test correttamente salvato");
                }else{
                    //errorDialog(ttError,  data);
                }
            },
            error : function (request, status, error) {
                console.log("jQuery AJAX request error:".error);
            }
        });
    }
}





function getGivenAnswer(questionDiv){
    var type = $(questionDiv).attr("type");
    return window["getGivenAnswer_"+type](questionDiv);
}

function loadCountdownAndExtra(){

// ----------------------------------------------------------- //
//                          COUNTDOWN                          //
// ----------------------------------------------------------- //

    /** @descr  Add draggable attribute to countdown */
    $("#countdown").draggable({
        stop : function(event, ui) {
            countdownPosX = $("#countdown").offset().top - $(window).scrollTop();
        }
    });

// ----------------------------------------------------------- //
//                      EXTRA (CALCULATOR)                     //
// ----------------------------------------------------------- //

    /** @descr  Add draggable attribute to calculator */
    $("#calculator").draggable({
        cancel : 'object',
        stop : function(event, ui) {
            calculatorPosX = $("#calculator").offset().top - $(window).scrollTop();
        }
    });

    /** @descr  Binded function to show calculator */
    $(".questionText img.calculator").on("click", function(event){
        $("#calculator").show();
    });

// ----------------------------------------------------------- //
//                    EXTRA (PERIODIC TABLE)                   //
// ----------------------------------------------------------- //

    /** @descr  Add draggable attribute to periodic table */
    $("#periodicTable").draggable({
        cancel : 'img',
        stop : function(event, ui) {
            periodicTablePosX = $("#periodicTable").offset().top - $(window).scrollTop();
        }
    });

    /** @descr  Binded function to show periodic table */
    $(".questionText img.periodicTable").on("click", function(event){
        $("#periodicTable").show();
    });

// ----------------------------------------------------------- //
//                           COMMON                            //
// ----------------------------------------------------------- //


    /** @descr  Add srollable function for countdown and extras */
    $(window).scroll(function(event) {
        $('#countdown').css('top', (countdownPosX + $(this).scrollTop()) + "px");
        $('#calculator').css('top', (calculatorPosX + $(this).scrollTop()) + "px");
        $('#periodicTable').css('top', (periodicTablePosX + $(this).scrollTop()) + "px");
    });

    /** @descr  Hide extra */
    $("span.extraClose").on("click", function(event){
        $(this).closest(".extra").hide();
    });
}
function helpjs(){
    $("#dialogError p").html(ttHelpStudentTest);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}

function myFunction(valore) {
    if(!isNaN(valore)) return 'white';
    else{
        showErrorMessage (ttNumberError);
        return 'red';
    }
}


