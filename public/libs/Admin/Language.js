/**
 * File: Language.js
 * User: Masterplan
 * Date: 7/5/13
 * Time: 12:51 PM
 * Desc: Shows language edit page
 */

// Translations Table Column Index
var ttci = {
    translationID : 0,
    langFrom : 1,
    langTo : 2
};

$(function(){
    if($("#dialogAdd").length > 0){
        $("#dialogAdd").dialog({
            autoOpen        :   false,
            draggable       :   false,
            resizable       :   true,
            width           :   400,
            height          :   "auto",
            modal           :   true,
            closeOnEscape   :   false,
            position        :   ["center", 50],
            buttons : {
                No : function(){
                    $(this).dialog("close");
                    confirmCallback($(this).data("callback"), $(this).data("params"), false);
                },
                Yes : function(){
                    $(this).dialog("close");
                    confirmCallback($(this).data("callback"), $(this).data("params"), true);
                }
            }
        });
    }
});

$(function(){

    /**
     *  @descr  Translation DataTables initialization
     */
    translationsTable = $("#translationsTable").DataTable({
        scrollY:        400,
        scrollCollapse: false,
        jQueryUI:       true,
        paging:         false,
        bSort : false,
        columns : [
            { className: "translationID"},
            { className: "langFrom", width: "45%"},
            { className: "langTo", width: "45%"}
        ],
        language : {
            info: "",
            infoFiltered: "",
            infoEmpty: ""
        }
    });

    $("#translationsTable_filter").css("margin-right", "50px")
        .after($("#newTranslation").parent())
        .before($("#translationsTable_info"));

//    $("#translationsTableContainer .ui-corner-bl").append(printBoxHelpMessage(ttHTransPanel));

    /**
     *  @descr  Binded event to create new translation
     */
    $("#newTranslation").on("click", function(event){
        newEmptyTranslation();
    });

    /**
     *  @descr  Autoresize all textareas
     */
    $("textarea.language").autoresize();

    /**
     *  @descr  Bind event for textarea lost focus
     */
    $("textarea.language").on("focusout", function(){ checkTranslation(this) });
    $("textarea.language").on("change", function(){ $(this).parent().find("span").text($(this).val()) });

});


function addDialog (callback,params) {
    $('#dialogAdd p').html(
        '<label>'+ ttIdLangCell +'<input type="text" id="idlangcell"></label><br><br>' +
        '<label>'+ ttTextLangCell +'<input type="text" id="textlangcell"></label>'
    );
    $('#dialogAdd').data("callback", callback)
        .data("params", params)
        .dialog("option", "title", ttAddLangCellTitle )
        .dialog("open");
}


/**
 *  @name   updateLanguageFiles
 *  @descr  Saves PHP/Javascript file of requested language
 */
function saveLanguageFiles(){
    confirmDialog(ttWarning, ttCUpdateLanguage, function(){
        var langAlias = $("#langAlias").val();
        var empty = false;
        var constants = new Array();
        var translations = new Array();

        translationsTable.rows().eq(0).filter(function(rowIndex){
            var id = translationsTable.cell(rowIndex, ttci.translationID).data().trim();
            var text = $(translationsTable.cell(rowIndex, ttci.langTo).node()).find("span").text();
	if(text == "")
                empty = true;
            constants.push(id);
            translations.push(text);
        });

        if(!empty){
            $.ajax({
                url     : "index.php?page=admin/savelanguage",
                type    : "post",
                data    :{
                    alias        :   langAlias,
                    constants    :   JSON.stringify(constants),
                    translations :   JSON.stringify(translations)
                },
                success : function (data) {
                    if(data == "ACK"){
                        showSuccessMessage(ttMLanguageUpdated);
                        setTimeout(function(){window.location = 'index.php?page=admin/selectlanguage'}, 1500);
                    }else
                        showErrorMessage(data);
                },
                error : function (request, status, error){ alert("jQuery AJAX request error:".error); }
            });
        }else
            showErrorMessage(ttEEmptyFields);
    })
}

/**
 *  @name   addLanguageCell
 *  @descr  Adds a new language cell of the requested language
 */
function addLanguageCell(){
    addDialog(function (){
        let id = $("#idlangcell").val().trim();
        let text = $("#textlangcell").val();
        let chekRep = false;
        translationsTable.rows().eq(0).filter(function (index) {
            let tableId = translationsTable.cell(index, ttci.translationID).data().trim();
            if (id === tableId) {
                chekRep = true;
            }
        });
        if(!chekRep){
            let textarea = '<span class="value hidden">'+text+'</span><textarea class="language green">'+text+'</textarea>';
            translationsTable.row.add([ id, text, textarea ]).draw();

            showSuccessMessage(ttLanguageCellAdded);
        }else{
            showErrorMessage(ttETransIdExisting);
        }
    });
}

/**
 *  @name   checkTranslation
 *  @descr  Checks if exists the translation in requested textarea,
 *          or (if textarea is null) checks every textareas in page
 *  @param  textarea        DOM Element         Textarea to check
 */
function checkTranslation(textarea){
    if(textarea == null){
        $("textarea.language").each(function(index, textarea){
            if($(this).val().trim() != "")
                $(this).switchClass("red", "green");
            else
                $(this).switchClass("green", "red");
        });
    }else{
        if($(textarea).val() != "")
            $(textarea).switchClass("red", "green");
        else
            $(textarea).switchClass("green", "red");
    }
}
function helpjs(){

    $("#dialogError p").html(ttHelpADMINLanguageDescription);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}
