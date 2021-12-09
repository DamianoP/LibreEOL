/**
 * File:
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc:
 */

var allOpened = false;

$(function(){

    $("#showHide").on("click", function(){showHide(null)});

    /**
     *  @descr  Function for dropdown menu effects
     */
    $(".dropdownScore dt, .dropdownBonus dt, .dropdownFinalScore dt").on("click", function(event){
        $(this).children("span").toggleClass("clicked");
        $(this).next().children("ol").slideToggle(200);
    });

    /**
     *  @descr  Function to change score for open questions
     */
    $(".dropdownScore dd ol li, .dropdownBonus dd ol li, .dropdownFinalScore dd ol li").on("click", function(event){ updateTestScore($(this)); });

    // Close all dropdowns when click out of it
    // Maybe too heavy for system... IMPROVE
    $(document).on('click', function(e) {
        var $clicked = $(e.target);
        if (!(($clicked.parents().hasClass("dropdownScore")) ||
              ($clicked.parents().hasClass("dropdownBonus")) ||
              ($clicked.parents().hasClass("dropdownFinalScore")) )){
            $(".dropdownScore dd ol, .dropdownBonus dd ol, .dropdownFinalScore dd ol").slideUp(200);
            $(".dropdownScore dt span, .dropdownBonus dt span, .dropdownFinalScore dt span").removeClass("clicked");
        }
    });

});

/**
 *  @name   confirmTest
 *  @param  askConfirmation         Array       If askConfirmation[0] is true display confirm dialog
 *  @descr  Confirm final score and archive test
 */
function confirmTest(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCConfirmTest, confirmTest, new Array(false)))){
        var correctScores = [];
        $("div.questionTest").each(function(){
            correctScores[$(this).attr("value")] = $(this).find(".responseScore").text();

        });

        var maxScore = parseFloat($("#maxScore").val());
        var scoreFinal = $("#scorePost").text().split("/")[0];
        if((scoreFinal == maxScore) && ($("#scoreLaudae").prop("checked")))
            scoreFinal = maxScore + 1;
        $.ajax({
            url     : "index.php?page=exam/correct",
            type    : "post",
            data    : {
                idTest        :   $("#idTest").val(),
                correctScores :   JSON.stringify(correctScores),
                scoreTest     :   $("#scorePre").text(),
                bonus         :   $("#scoreBonus").text(),
                scoreFinal    :   scoreFinal
            },
            success : function (data) {
                if(data == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMConfirm);
                    //setTimeout(function(){ $("#idExamForm").submit(); }, 1500);
                    setTimeout(function(){ window.close() }, 1500);
                }else{
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   updateTestScore
 *  @param  selected            DOM Element         Selected dropdown
 *  @descr  Updates question and test's score
 */
function updateTestScore(selected){
    var dropdown = selected.closest("dl");
    dropdown.children("dt").children("span").toggleClass("clicked");
    var text = selected.html();
    var bonus = 0;
    var maxScore = parseFloat($("#maxScore").val());
    if(dropdown.hasClass("dropdownScore")){
        var oldScore = parseFloat(dropdown.children("dt").find("span.value").text());
        var newScore = parseFloat(selected.children("span.value").text());
        bonus = parseFloat($("#scoreBonus").text());

        var scorePre = parseFloat($("#scorePre").text()) - oldScore + newScore;
        var scorePost = scorePre + bonus;
        if(scorePre > maxScore){
            scorePre = maxScore;
        }
        if(scorePost > maxScore){
            scorePost = maxScore;
        }else if(scorePost < 0){
            scorePost = 0;
        }
        $("#scorePre").text(scorePre.toFixed(1));
        $(".dropdownFinalScore dt").html("<span>"+scorePost.toFixed(0)+"<span class='value'>"+scorePost.toFixed(0)+"</span></span>");
        $("#scorePost").text(scorePost.toFixed(0));

        $(selected).closest(".questionTest").removeClass('correctQuestion wrongQuestion rightQuestion');
        if(newScore > 0)
            $(selected).closest(".questionTest").addClass('rightQuestion');
        else
            $(selected).closest(".questionTest").addClass('wrongQuestion');

        $(selected).closest(".questionTest").find(".questionText span.responseScore").html(newScore);
    }
    selected.parent().parent().prev().children("span").html(text);
    selected.parent().hide();
    if(dropdown.hasClass("dropdownBonus")){
        bonus = parseFloat(selected.children("span.value").text());
        $("#scoreBonus").text(bonus);
        scorePost = parseFloat($("#scorePre").text()) + bonus;
        if(scorePost > maxScore){
            scorePost = maxScore;
        }else if(scorePost < 0){
            scorePost = 0;
        }
        $(".dropdownFinalScore dt").html("<span>"+scorePost.toFixed(0)+"<span class='value'>"+scorePost.toFixed(0)+"</span></span>");
        $("#scorePost").text(scorePost.toFixed(0));
    }
    if(dropdown.hasClass("dropdownFinalScore")){
        scorePost = parseFloat(selected.children("span.value").text());
        $("#scorePost").text(scorePost.toFixed(0));
    }
    if(scorePost == maxScore)
        $("#laudae").show();
    else{
        $("#laudae").hide();
        $("#scoreLaudae").prop("checked", false);
    }
}

/**
 *  @name   showHide
 *  @param  selected            DOM Element             <div> of selected question
 *  @descr  Shows or Hide answers sections
 */
function showHide(selected){
    if(selected == null){
        if(allOpened){
            $(".questionAnswers").slideUp();
            allOpened = false;
        }else{
            $(".questionAnswers").slideDown();
            allOpened = true;
        }
    }else{
        $(selected).parent().find(".questionAnswers").slideToggle();
    }
}

// funzione che sposta il puntatore nella posizione del click dello studente in fase di correzione
function prova(x,y){
    var theThing = $("#thing");
    theThing.css("left",x);
    theThing.css("top",y);
}
function riposizionaInCorrezione(elemento,left,top){
    setTimeout(function() { 
        var immagine= $(elemento);
        immagine.css("top",top+"px");
        immagine.css("left",left+"px");   
    }, 2000);
}
function rettangoloRispostaCorrettaHS(elemento,alto,basso,sinistra,destra){
    console.log("Ciao")
}
function helpjs(){

    $("#dialogError p").html(ttHelpExamsCorrect);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}