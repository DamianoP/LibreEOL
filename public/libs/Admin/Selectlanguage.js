/**
 * File: Selectlanguage.js
 * User: Masterplan
 * Date: 4/4/13
 * Time: 6:37 PM
 * Desc: Selects language to manage
 */

$(function(){

    /**
     *  @descr  Binded event to select language
     */
    $(".selectLanguage[value]").on("click", function (event) {
        $(this).addClass("selected");
        $("input[name='alias']").attr("value", $(".selected").attr("value"));
        $("#languageForm").attr("action", "index.php?page=admin/language");
        $("#languageForm").submit();
    });

    /**
     *  @descr  Binded event for New button
     */
    $("#new").on("click", function (event) {
        window.location = "index.php?page=admin/newlanguage";
    });

});
function helpjs(){

    $("#dialogError p").html(ttHelpADMINSelLanguageDescription);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}