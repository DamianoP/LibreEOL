/**
 * File: View.js
 * User: Masterplan
 * Date: 5/6/13
 * Time: 5:33 PM
 * Desc: Views archived test
 */

var allOpened = false;

$(function(){

    $("#showHide").on("click", function(){showHide(null)});

});

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