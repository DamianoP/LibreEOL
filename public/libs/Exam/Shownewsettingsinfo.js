/**
 * File: Showsettingsinfo.js
 * User: Masterplan
 * Date: 27/05/14
 * Time: 13:24
 * Desc: Shows test setting's info and allows to edit its informations
 */

var topicsQuestions = new Array();
var difficultiesQuestions = new Array();

function checkQuestionsPerTopic(){
        try{
            var tot = parseInt($("#topicQuestionsTotals").text().trim());
            if( tot>0 && parseInt($("#settingsQuestions").val().trim())!=tot){
                $('#rigaTotale').css("background", "rgb(223, 58, 58)");
            }else{            
                $('#rigaTotale').css("background", "rgb(12, 156, 12)");
            } 
            //console.log(tot);
            //console.log($("#settingsQuestions").val().trim());
        }catch(error){
            console.log(error.message);
        }
}

$(function(){

    var numTot = $("#settingsQuestions").val().trim();
    var numEasy = $("#setEasy").val().trim();
    var numMedium = $("#setMedium").val().trim();
    var numHard = $("#setHard").val().trim();
    
    if($.isNumeric(numTot)){
        if($.isNumeric(numEasy)){
            if($.isNumeric(numMedium)){
                if($.isNumeric(numHard)){
                    var tot =parseInt(numEasy)+ parseInt(numMedium) + parseInt(numHard);
                    if(numTot != tot){
                        $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                    }else {
                        $(".setDifficulty").css("background", "rgb(12, 156, 12)");
                    }
                    checkQuestionsPerTopic();
                }else{
                    $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                    showErrorMessage(ttField+" "+ttHard+": "+ttMustBeaNumber);
                }
            }else{
                $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                showErrorMessage(ttField+" "+ttMedium+": "+ttMustBeaNumber);
            }
        }else{
            $(".setDifficulty").css("background", "rgb(223, 58, 58)");
            showErrorMessage(ttField+" "+ttEasy+": "+ttMustBeaNumber);
        }
    }else{
        $(".setDifficulty").css("background", "rgb(223, 58, 58)");
        showErrorMessage(ttField+" "+ttQuestion+": "+ttMustBeaNumber);
    }

    $(".readonly").attr("disabled", "");

    /**
     *  @descr  Enables test settings info dropdown
     */
    $(".dropdownInfo dt.writable").on("click", function() {
                                                    $(this).children("span").toggleClass("clicked");
                                                    $(this).next().children("ol").slideToggle(200);
                                                });
    $(".dropdownInfo dd ol li").on("click", function() {
                                                updateDropdown($(this));
                                            });
    $(document).on('click', function(e) {
        var $clicked = $(e.target);
        if(!($clicked.parents().hasClass("dropdownInfo"))){
            $(".dropdownInfo dd ol").slideUp(200);
            $(".dropdownInfo dt span").removeClass("clicked");
        }
    });

    //Checks if Questions=Hard+Medium+Easy
    $(".setDifficulty").change(function() {
        if(isNaN(parseInt($(this).val())) || parseInt($(this).val()) < 0){
            $(this).val('0');
        }
        var numTot = $("#settingsQuestions").val().trim();
        var numEasy = $("#setEasy").val().trim();
        var numMedium = $("#setMedium").val().trim();
        var numHard = $("#setHard").val().trim();

        if($.isNumeric(numTot)){
            if($.isNumeric(numEasy)){
                if($.isNumeric(numMedium)){
                    if($.isNumeric(numHard)){
                        var tot =parseInt(numEasy)+ parseInt(numMedium) + parseInt(numHard);
                        if(numTot != tot){
                            $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                        }else {
                            $(".setDifficulty").css("background", "rgb(12, 156, 12)");
                        }
                        checkQuestionsPerTopic();
                    }else{
                        $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                        showErrorMessage(ttField+" "+ttHard+": "+ttMustBeaNumber);
                    }
                }else{
                    $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                    showErrorMessage(ttField+" "+ttMedium+": "+ttMustBeaNumber);
                }
            }else{
                $(".setDifficulty").css("background", "rgb(223, 58, 58)");
                showErrorMessage(ttField+" "+ttEasy+": "+ttMustBeaNumber);
            }
        }else{
            $(".setDifficulty").css("background", "rgb(223, 58, 58)");
            showErrorMessage(ttField+" "+ttQuestion+": "+ttMustBeaNumber);
        }
    });


    $("#setEasy").change(function () {

        var num = $("#setEasy").val().trim();
        var mandatory = countMandatory(ttD1);
        if(num < mandatory){
            showErrorMessage(ttErrorMax);
            $("#setEasy").val(countMandatory(ttD1));
        }
        checkQuestionsPerTopic();
    });
    $("#setMedium").change(function () {

        var num = $("#setMedium").val().trim();
        var mandatory = countMandatory(ttD2);
        if(num < mandatory){
            showErrorMessage(ttErrorMax);
            $("#setMedium").val(countMandatory(ttD2));
        }
        checkQuestionsPerTopic();
    });
    $("#setHard").change(function () {

        var num = $("#setHard").val().trim();
        var mandatory = countMandatory(ttD3);
        if(num < mandatory){
            showErrorMessage(ttErrorMax);
            $("#setHard").val(countMandatory(ttD3));
        }
        checkQuestionsPerTopic();
    });

    $(".numQuestionsPerTopic").change(function () {
        var tot = 0;
        if(isNaN(parseInt($(this).val()))|| parseInt($(this).val()) < parseInt($(this).attr('min'))){
            $(this).val(parseInt($(this).attr('min')));
        }
        console.log( $(this).val());
        if($(this).val() > parseInt($(this).attr('max'))){
            console.log('entrato');
            $(this).val(parseInt($(this).attr('max')));
        }
        $(".numQuestionsPerTopic").each(function () {
            tot += parseInt($(this).val());
        })
        $('#topicQuestionsTotals').text(tot);
        
        checkQuestionsPerTopic();


    });
    /**
     *  @descr  Enables chars counters for settingsName and settingsDesc fields
     */
    enableCharsCounter("settingsName", "settingsName");
    enableCharsCounter("settingsDesc", "settingsDesc");
});


/**
 *  @name   selectQuestion
 *  @descr  Selects/Deselects single mandatory question and checks its topic and difficulty values to specified in topic/difficulty sections
 *  @param  selectedQuestion        DOM Element         Selected question's checkbox
 */
function selectQuestion(selectedQuestion){
    if(settingsEditing){
        var selectedQuestionRow = questionsTable.row(questionsTable.cell($(selectedQuestion).parent()).index().row).data();
        var topicID = selectedQuestionRow[qtci.topicID];
        var questionID = selectedQuestionRow[qtci.questionID];
        var difficultyLevel = selectedQuestionRow[qtci.difficultyID];
        var counter = 0;
        var maxAllowed = 0;
        if($(selectedQuestion).is(":checked")) {
            if (difficultyLevel == "settingsD1") {
                maxAllowed = $("#setEasy").val().trim();
                if ($.isNumeric(maxAllowed)) {
                    counter = countMandatory(ttD1);
                    if (counter > maxAllowed) {
                        showErrorMessage(ttMaxEasyReached);
                        $("[value="+ questionID +"]").attr('checked', false);
                        return;
                    } else {
                        $("#topicQuestionsMandatory" + topicID).text(parseInt($("#topicQuestionsMandatory" + topicID).text()) + 1);
                        $("#topicQuestionsMandatoryTotals").text(parseInt($("#topicQuestionsMandatoryTotals").text()) + 1);
                        $("#topicQuestionsTotals").text(parseInt($("#topicQuestionsTotals").text()) + 1);
                        $("#numQuestions"+topicID).val(parseInt($("#numQuestions"+topicID).val()) + 1);
                        $("#numQuestions"+topicID).attr('min',parseInt($("#numQuestions"+topicID).attr('min'))+ 1);
                    }
                } else {
                    showErrorMessage(ttField + " " + ttEasy + ": " + ttMustBeaNumber);
                    $("[value="+ questionID +"]").attr('checked', false);
                    return;
                }
            } else if (difficultyLevel == "settingsD2") {
                maxAllowed = $("#setMedium").val().trim();
                if ($.isNumeric(maxAllowed)) {
                    counter = countMandatory(ttD2);
                    if (counter > maxAllowed) {
                        showErrorMessage(ttMaxMediumReached);
                        $("[value="+ questionID +"]").attr('checked', false);
                        return;
                    } else {
                        $("#topicQuestionsMandatory" + topicID).text(parseInt($("#topicQuestionsMandatory" + topicID).text()) + 1);
                        $("#topicQuestionsMandatoryTotals").text(parseInt($("#topicQuestionsMandatoryTotals").text()) + 1);
                        $("#topicQuestionsTotals").text(parseInt($("#topicQuestionsTotals").text()) + 1);
                        $("#numQuestions"+topicID).val(parseInt($("#numQuestions"+topicID).val()) + 1);
                        $("#numQuestions"+topicID).attr('min',parseInt($("#numQuestions"+topicID).attr('min'))+ 1);
                    }
                } else {
                    showErrorMessage(ttField + " " + ttEasy + ": " + ttMustBeaNumber);
                    $("[value="+ questionID +"]").attr('checked', false);
                    return;
                }
            } else if (difficultyLevel == "settingsD3") {
                maxAllowed = $("#setHard").val().trim();
                if ($.isNumeric(maxAllowed)) {
                    counter = countMandatory(ttD3);
                    if (counter > maxAllowed) {
                        showErrorMessage(ttMaxHardReached);
                        $("[value="+ questionID +"]").attr('checked', false);
                        return;
                    } else {
                        $("#topicQuestionsMandatory" + topicID).text(parseInt($("#topicQuestionsMandatory" + topicID).text()) + 1);
                        $("#topicQuestionsMandatoryTotals").text(parseInt($("#topicQuestionsMandatoryTotals").text()) + 1);
                        $("#topicQuestionsTotals").text(parseInt($("#topicQuestionsTotals").text()) + 1);
                        $("#numQuestions"+topicID).val(parseInt($("#numQuestions"+topicID).val()) + 1);
                        $("#numQuestions"+topicID).attr('min',parseInt($("#numQuestions"+topicID).attr('min'))+ 1);
                    }
                } else {
                    showErrorMessage(ttField + " " + ttEasy + ": " + ttMustBeaNumber);
                    $("[value="+ questionID +"]").attr('checked', false);
                    return;
                }
            }
        }else{
            $("#topicQuestionsMandatory" + topicID).text(parseInt($("#topicQuestionsMandatory" + topicID).text()) - 1);
            $("#topicQuestionsMandatoryTotals").text(parseInt($("#topicQuestionsMandatoryTotals").text()) - 1);
            $("#topicQuestionsTotals").text(parseInt($("#topicQuestionsTotals").text()) - 1);
            $("#numQuestions"+topicID).val(parseInt($("#numQuestions"+topicID).val()) - 1);
            $("#numQuestions"+topicID).attr('min',parseInt($("#numQuestions"+topicID).attr('min'))- 1);
        }
   }
}

/**
 *  @name   checkSuddivisionIntegrity
 *  @descr  Checks that Questions input field is correctly divide between Hard,Medium and Easy
 */
function checkSuddivisionIntegrity(){
    var numTot = $("#settingsQuestions").val().trim();
    var numEasy = $("#setEasy").val().trim();
    var numMedium = $("#setMedium").val().trim();
    var numHard = $("#setHard").val().trim();

    var tot =parseInt(numEasy)+ parseInt(numMedium) + parseInt(numHard);
    if(numTot != tot){
        return false;
    }else {
        return true;
    }
}
/**
 *  @name   checkTotalsIntegrity
 *  @descr  Checks that Questions, Easy, Medium and Hard input fields are correct
 */
function checkTotalsIntegrity() {
    var inputTot = parseInt($("#settingsQuestions").val().trim());
    var realTot = parseInt($("#topicQuestionsTotals").text().trim());
    var inputTotE = parseInt($("#setEasy").val().trim());
    var realTotE = parseInt(deleteFirstLastChar($("#numMaxEasyTotals").text().trim()));
    var inputTotM = parseInt($("#setMedium").val().trim());
    var realTotM = parseInt(deleteFirstLastChar($("#numMaxMediumTotals").text().trim()));
    var inputTotH = parseInt($("#setHard").val().trim());
    var realTotH = parseInt(deleteFirstLastChar($("#numMaxHardTotals").text().trim()));

    if(realTot > inputTot){
        showErrorMessage(ttErrorTopic);
        return false;
    }else if((realTotE+ realTotM + realTotH) < inputTot){
        showErrorMessage(ttNotAvailable);
        return false;
    }else if(realTotE < inputTotE){
        showErrorMessage(ttNotAvailable + " " + ttD1s);
        return false;
    }else if(realTotM < inputTotM){
        showErrorMessage(ttNotAvailable + " " + ttD2s);
        return false;
    }else if(realTotH < inputTotH){
        showErrorMessage(ttNotAvailable + " " + ttD3s);
        return false;
    }

    return true;

}

/**
 *  @name   checkTopicIntegrity
 *  @descr  Checks that QuestionsTopic is lesser than MaxEasy+MaxMedium+MaxHard for all topics
 */
function  checkTopicIntegrity() {
    var id;
    var goOn = true;
    $(".topicQuestions").each(function () {
        id = $(this).attr("id");
        id = id.substring(12,id.length);
        var easy = parseInt(deleteFirstLastChar($("#numMaxEasy"+id).text()));
        var medium = parseInt(deleteFirstLastChar($("#numMaxMedium"+id).text()));
        var hard = parseInt(deleteFirstLastChar($("#numMaxHard"+id).text()));
        var tot = parseInt($(this).val());
        if(tot > easy+medium+hard){
            showErrorMessage(ttThereAreNot +" "+ tot +" "+ ttAvailable + " " + $(this).attr("name"));
            goOn = false;
        }
    });
    return goOn;

}
/**
 *  @name   countMandatory
 *  @descr  Counts the number of mandatory selected of a given type
 */
function countMandatory(type) {
    var counter = 0;
    var difficulties = [];
    questionsTable
        .column(qtci.difficulty)
        .data()
        .each( function (value , index ) {
            difficulties[index]=value;
        } );

    questionsTable
        .column(qtci.questionID)
        .data()
        .each( function (value , index ) {
            if($("#check"+value).is(":checked")){
                 if(type == difficulties[index]){
                     counter++;
                 }
             }
        } );

    return counter;
}
/**
 *  @name   deleteFirstLastChar
 *  @descr  return the given string without the first and the last charachter
 */
function deleteFirstLastChar(str){
    str = str.substring(1,str.length);
    str = str.substring(0,str.length -1);
    return str;
}