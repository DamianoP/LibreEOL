/**
 * File: Systemconfiguration.js
 * User: Masterplan
 * Date: 27/10/14
 * Time: 19:24
 * Desc: Show page to edit system's configurations
 */


$(function(){

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

});

function saveConfiguration(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCUpdateConfigurations, saveConfiguration, new Array(false)))){
        var empty = false;
        $("input[type=text]").each(function(){
            if($(this).val().trim() == "")
                empty = true;
        });
        if(!empty){
            $.ajax({
                url     : "index.php?page=admin/updatesystemconfiguration",
                type    : "post",
                data    : {
                    skin        :   $("#configurationTheme dt span.value").text(),
                    logo        :   $("#configurationLogo").val().trim(),
                    title       :   $("#configurationTitle").val().trim(),
                    home        :   $("#configurationHome").val().trim(),
                    email       :   $("#configurationEmail").val().trim(),
                    language    :   $("#configurationLanguage dt span.value").text(),
                    timezone    :   $("#configurationTimeZone dt span.value").text(),
                    dbType      :   $("#configurationDBType dt span.value").text(),
                    dbHost      :   $("#configurationDBHost").val().trim(),
                    dbPort      :   $("#configurationDBPort").val().trim(),
                    dbName      :   $("#configurationDBName").val().trim(),
                    dbUsername  :   $("#configurationDBUsername").val().trim(),
                    dbPassword  :   $("#configurationDBPassword").val().trim()
                },
                success : function(data){
                    console.log(data);
                    if(data=="ACK"){
                        console.log("successo");
                        showSuccessMessage(ttMConfigurationsUpdated);
                        setTimeout(function(){
                            window.location = "index.php";
                        }, 1500);
                    }else{
                        console.log(data);
                        showErrorMessage(data);
                    }

                },
                error : function(){
                    alert("jQuery AJAX request error:");
                }
            });
        }else showErrorMessage(ttEEmptyFields);
    }
}
function helpjs(){

    $("#dialogError p").html(ttHelpADMINSystemConf);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}