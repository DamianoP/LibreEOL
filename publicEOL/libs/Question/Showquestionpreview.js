/**
 * File: Showquestionpreview.js
 * User: Masterplan
 * Date: 17/05/14
 * Time: 17:31
 * Desc: Shows question's preview with answers
 */

$(function(){

    /** @descr  Bind event for checkboxes */
    $("#questionPreview input[type='checkbox'] + span").on("click", function(event){
        $(this).prev().prop("checked", !($(this).prev().is(":checked")));
    });

    /** @descr  Bind event for radiobuttons */
    $("#questionPreview input[type='radio'] + span").on("click", function(event){
        $(this).prev().prop("checked", true);
    });

});


// Sposta il puntatore durante la visualizzazione della preview della domanda
function getClickPosition(event) {

    var container = $( "#contentContainer" );
    var theThing = $("#thing");
    var parentOffset = container.parent().offset();
    var x = (event.pageX - parentOffset.left - 20 - 13);
    var y = (event.pageY - parentOffset.top);
    y=-(container.height()-y);
    var xPositionPX = x + "px";
    var yPositionPX = y + "px";
    theThing.css("left",xPositionPX);
    theThing.css("top",yPositionPX);
    //alert("pageX&Y  x="+ xPositionPX + ", y=" + yPositionPX);


}

// Controlla che il valore inserito in fase di test nella domanda numeric sia un NUMERO viene visualizzato in fase di preview
function controlNum(){
    var valore = document.getElementById("inputNumber").value;
    num = $.isNumeric(valore);
    if ((num == false) || (valore !="")) {
        showErrorMessage (ttNumberError);
    }
}

