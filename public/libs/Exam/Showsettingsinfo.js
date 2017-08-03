/**
 * File: Showsettingsinfo.js
 * User: Masterplan
 * Date: 27/05/14
 * Time: 13:24
 * Desc: Shows test setting's info and allows to edit its informations
 */

var topicsQuestions = new Array();
var difficultiesQuestions = new Array();

$(function(){

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

    /**
     *  @descr  Enables chars counters for settingsName and settingsDesc fields
     */
    enableCharsCounter("settingsName", "settingsName");
    enableCharsCounter("settingsDesc", "settingsDesc");
});

/**
 *  @name   changeTopicQuestions
 *  @descr  Changes questions number for requested topic (with integer control)
 *  @param  topicQuestions          DOM Element         Changed topic's input field
 */
function changeTopicQuestions(topicQuestions){
    if(settingsEditing){
        var topicID = $(topicQuestions).closest("tr").attr("value");
        var questionsNum = parseInt($(topicQuestions).val());
        if((isNaN(questionsNum)) || (questionsNum < 0))
            $(topicQuestions).val(topicsQuestions[topicID]);
        else
            topicsQuestions[topicID] = questionsNum;
        updateQuestionsSummaries();
    }
}

/**
 *  @name   changeDifficultyQuestions
 *  @descr  Changes questions number for requested difficulty level (with integer control)
 *  @param  difficultyQuestions         DOM Element         Changed difficulty's input field
 */
function changeDifficultyQuestions(difficultyQuestions){
    if(settingsEditing){
        var difficultyLevel = $(difficultyQuestions).attr("id");
        var questionsNum = parseInt($(difficultyQuestions).val());
        if((isNaN(questionsNum)) || (questionsNum < 0))
            $(difficultyQuestions).val(difficultiesQuestions[difficultyLevel]);
        else
            difficultiesQuestions[difficultyLevel] = questionsNum;
        updateQuestionsSummaries();
    }

}

/**
 *  @name   selectQuestion
 *  @descr  Selects/Deselects single mandatory question and checks its topic and difficulty values to specified in topic/difficulty sections
 *  @param  selectedQuestion        DOM Element         Selected question's checkbox
 */
function selectQuestion(selectedQuestion){
    if(settingsEditing){
        var selectedQuestionRow = questionsTable.row(questionsTable.cell($(selectedQuestion).parent()).index().row).data();
        var topicID = selectedQuestionRow[qtci.topicID];
        var difficultyLevel = selectedQuestionRow[qtci.difficultyID];

        if($(selectedQuestion).is(":checked")){
            $("#topicQuestionsMandatory"+topicID).text(parseInt($("#topicQuestionsMandatory"+topicID).text()) + 1);
            $("#"+difficultyLevel+"Mandatory").text(parseInt($("#"+difficultyLevel+"Mandatory").text()) + 1);
        }else{
            $("#topicQuestionsMandatory"+topicID).text(parseInt($("#topicQuestionsMandatory"+topicID).text()) - 1);
            $("#"+difficultyLevel+"Mandatory").text(parseInt($("#"+difficultyLevel+"Mandatory").text()) - 1);
        }
        updateQuestionsSummaries();
    }
}

/**
 *  @name   updateQuestionsSummaries
 *  @descr  Calculates new questions summaries for topics and difficulties sections and updates summary boxes and question field
 */
function updateQuestionsSummaries(){
    topicQuestionSummary = 0;
    difficultyQuestionSummary = 0;

    $(".settingsTopic").each(function(){
        topicQuestionSummary += parseInt($(this).find(".settingsTopicQuestions input").val());
        topicQuestionSummary += parseInt($(this).find(".settingsTopicQuestionsMandatory span").text());
    });
    $(".settingsDifficulty").each(function(){
        difficultyQuestionSummary += parseInt($(this).find(".settingsDifficultyQuestions input").val());
        difficultyQuestionSummary += parseInt($(this).find(".settingsDifficultyQuestionsMandatory span").text());
    });

    $("#topicQuestionsSummary span").text(topicQuestionSummary);
    $("#difficultyQuestionsSummary span").text(difficultyQuestionSummary);
    if(topicQuestionSummary == difficultyQuestionSummary){
        $("#topicQuestionsSummary, #difficultyQuestionsSummary").removeClass("backError").addClass("backSuccess");
        $("#settingsQuestions").css("background", "rgb(12, 156, 12)");
        $("#settingsQuestions").val(topicQuestionSummary);
    }else{
        $("#topicQuestionsSummary, #difficultyQuestionsSummary").removeClass("backSuccess").addClass("backError");
        $("#settingsQuestions").css("background", "rgb(223, 58, 58)");
        $("#settingsQuestions").val("xXx");
    }
}