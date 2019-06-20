/**
 * File:
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc:
 */

var allOpened = false;

$(function () {

  $("#showHide").on("click", function () { showHide(null) });

  /**
   *  @descr  Function for dropdown menu effects
   */
  $(".dropdownScore dt, .dropdownBonus dt, .dropdownFinalScore dt").on("click", function (event) {
    $(this).children("span").toggleClass("clicked");
    $(this).next().children("ol").slideToggle(200);
  });

  // Close all dropdowns when click out of it
  // Maybe too heavy for system... IMPROVE
  $(document).on('click', function (e) {
    var $clicked = $(e.target);
    if (!(($clicked.parents().hasClass("dropdownScore")) ||
      ($clicked.parents().hasClass("dropdownBonus")) ||
      ($clicked.parents().hasClass("dropdownFinalScore")))) {
      $(".dropdownScore dd ol, .dropdownBonus dd ol, .dropdownFinalScore dd ol").slideUp(200);
      $(".dropdownScore dt span, .dropdownBonus dt span, .dropdownFinalScore dt span").removeClass("clicked");
    }
  });

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

// funzione che sposta il puntatore nella posizione del click dello studente in fase di correzione
function prova(x, y) {
  var theThing = $("#thing");
  theThing.css("left", x);
  theThing.css("top", y);
}
function riposizionaInCorrezione(elemento, left, top) {
  setTimeout(function () {
    var immagine = $(elemento);
    immagine.css("top", top + "px");
    immagine.css("left", left + "px");
  }, 2000);
}
function rettangoloRispostaCorrettaHS(elemento, alto, basso, sinistra, destra) {
  console.log("Ciao")
}
function helpjs() {

  $("#dialogError p").html(ttHelpExamsCorrect);
  $("#dialogError").dialog("option", "title", ttHelpDefault)
    .dialog("open");
  $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");

}
