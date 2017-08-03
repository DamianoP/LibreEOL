/**
 * Created by michele on 29/11/15.
 */

var checkedass=false;
var checkedtopic=false;
var checkedgraphic=false;

/***********************
 * @name selectAllAssesment
 * @desc Select or deselect all checkboxes relative to assesment information
 **********************/
function selectAllAssesment(){
    if(!checkedass){
        $(".checkass").each(function(){
            $(this).prop("checked",true);
            checkedass=true;
            //console.log(checked);
            $("#selectall").text(ttDeselectAll);
        });
    }
    else{
        $(".checkass").each(function(){
            $(this).prop("checked",false);
            checkedass=false;
            //console.log(checked);
            $("#selectall").text(ttSelectAll);
        });
    }
}

/***********************
 * @name selectAllTopic
 * @desc Select or deselect all checkboxes relative to topic information
 **********************/
function selectAllTopic(){
    if(!checkedtopic){
        $(".checktopic").each(function(){
            $(this).prop("checked",true);
            checkedtopic=true;
            //console.log(checked);
            $("#selectallt").text(ttDeselectAll);
        });
    }
    else{
        $(".checktopic").each(function(){
            $(this).prop("checked",false);
            checkedtopic=false;
            //console.log(checked);
            $("#selectallt").text(ttSelectAll);
        });
    }
}

/***********************
 * @name selectAllGraphic
 * @desc Select or deselect all checkboxes relative to graphical displays
 **********************/
function selectAllGraphic(){
    if(!checkedgraphic){
        $(".checkgraphic").each(function(){
            $(this).prop("checked",true);
            checkedgraphic=true;
            //console.log(checked);
            $("#selectallg").text(ttDeselectAll);
        });
    }
    else{
        $(".checkgraphic").each(function(){
            $(this).prop("checked",false);
            checkedgraphic=false;
            //console.log(checked);
            $("#selectallg").text(ttSelectAll);
        });
    }
}

/***********************
 * @name saveTemplate
 * @desc Save a Report Template
 **********************/
function saveTemplate(){
    var str=$("#template_name").val();
    var trovato=str.indexOf(" ");
    console.log(trovato);
    if (trovato!=-1){
        showErrorMessage(ttReportTemplateSaveError);
    }
    else{
        $.ajax({
            url     : "index.php?page=report/savetemplate",
            type    : "post",
            data    : {
                templateName:$("#template_name").val(),
                assesmentName:$("input[type=checkbox][name=assesmentName]:checked").val(),
                assesmentID:$("input[type=checkbox][name=assesmentID]:checked").val(),
                assesmentAuthor:$("input[type=checkbox][name=assesmentAuthor]:checked").val(),
                assesmentDateTimeFirst:$("input[type=checkbox][name=assesmentDateTimeFirst]:checked").val(),
                assesmentDateTimeLast:$("input[type=checkbox][name=assesmentDateTimeLast]:checked").val(),
                assesmentNumberStarted:$("input[type=checkbox][name=assesmentNumberStarted]:checked").val(),
                assesmentNumberNotFinished:$("input[type=checkbox][name=assesmentNumberNotFinished]:checked").val(),
                assesmentNumberFinished:$("input[type=checkbox][name=assesmentNumberFinished]:checked").val(),
                assesmentMinscoreFinished:$("input[type=checkbox][name=assesmentMinscoreFinished]:checked").val(),
                assesmentMaxscoreFinished:$("input[type=checkbox][name=assesmentMaxscoreFinished]:checked").val(),
                assesmentMediumFinished:$("input[type=checkbox][name=assesmentMediumFinished]:checked").val(),
                assesmentLeastTimeFinished:$("input[type=checkbox][name=assesmentLeastTimeFinished]:checked").val(),
                assesmentMostTimeFinished:$("input[type=checkbox][name=assesmentMostTimeFinished]:checked").val(),
                assesmentMediumTimeFinished:$("input[type=checkbox][name=assesmentMediumTimeFinished]:checked").val(),
                assesmentStdDeviation:$("input[type=checkbox][name=assesmentStdDeviation]:checked").val(),
                topicAverageScore:$("input[type=checkbox][name=topicAverageScore]:checked").val(),
                topicMinimumScore:$("input[type=checkbox][name=topicMinimumScore]:checked").val(),
                topicMaximumScore:$("input[type=checkbox][name=topicMaximumScore]:checked").val(),
                topicStdDeviation:$("input[type=checkbox][name=topicStdDeviation]:checked").val(),
                graphicHistogram:$("input[type=checkbox][name=graphicHistogram]:checked").val(),
                graphicTopicScore:$("input[type=checkbox][name=graphicTopicScore]:checked").val()
            },
            success : function (data){
                console.log(data);
                if (data!="true"){
                    showErrorMessage(ttReportTemplateDuplicateError);
                }else{
                    showSuccessMessage(ttReportTemplateSaved);
                    $("#template_name").val("");
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }

}

/***********************
 * @name LoadCheckbox
 * @desc Set the checkbox from database data
 **********************/
function LoadCheckbox(){
        $.ajax({
            url     : "index.php?page=report/loadtemplate",
            type    : "post",
            data    : {
                templateName:$("#template").val(),
            },
            success : function (data){
                //console.log(JSON.parse(data));
                var obj=JSON.parse(data);
                obj.assesmentName==1 ? $("input[type=checkbox][name=assesmentName]").prop("checked",true):$("input[type=checkbox][name=assesmentName]").prop("checked",false);
                obj.assesmentID==1 ? $("input[type=checkbox][name=assesmentID]").prop("checked",true):$("input[type=checkbox][name=assesmentID]").prop("checked",false);
                obj.assesmentAuthor==1 ? $("input[type=checkbox][name=assesmentAuthor]").prop("checked",true):$("input[type=checkbox][name=assesmentAuthor]").prop("checked",false);
                obj.assesmentDateTimeFirst==1 ? $("input[type=checkbox][name=assesmentDateTimeFirst]").prop("checked",true):$("input[type=checkbox][name=assesmentDateTimeFirst]").prop("checked",false);
                obj.assesmentDateTimeLast==1 ? $("input[type=checkbox][name=assesmentDateTimeLast]").prop("checked",true):$("input[type=checkbox][name=assesmentDateTimeLast]").prop("checked",false);
                obj.assesmentNumberStarted==1 ? $("input[type=checkbox][name=assesmentNumberStarted]").prop("checked",true):$("input[type=checkbox][name=assesmentNumberStarted]").prop("checked",false);
                obj.assesmentNumberNotFinished==1 ? $("input[type=checkbox][name=assesmentNumberNotFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentNumberNotFinished]").prop("checked",false);
                obj.assesmentNumberFinished==1 ? $("input[type=checkbox][name=assesmentNumberFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentNumberFinished]").prop("checked",false);
                obj.assesmentMinscoreFinished==1 ? $("input[type=checkbox][name=assesmentMinscoreFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentMinscoreFinished]").prop("checked",false);
                obj.assesmentMaxscoreFinished==1 ? $("input[type=checkbox][name=assesmentMaxscoreFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentMaxscoreFinished]").prop("checked",false);
                obj.assesmentMediumFinished==1 ? $("input[type=checkbox][name=assesmentMediumFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentMediumFinished]").prop("checked",false);
                obj.assesmentLeastTimeFinished==1 ? $("input[type=checkbox][name=assesmentLeastTimeFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentLeastTimeFinished]").prop("checked",false);
                obj.assesmentMostTimeFinished==1 ? $("input[type=checkbox][name=assesmentMostTimeFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentMostTimeFinished]").prop("checked",false);
                obj.assesmentMediumTimeFinished==1 ? $("input[type=checkbox][name=assesmentMediumTimeFinished]").prop("checked",true):$("input[type=checkbox][name=assesmentMediumTimeFinished]").prop("checked",false);
                obj.assesmentStdDeviation==1 ? $("input[type=checkbox][name=assesmentStdDeviation]").prop("checked",true):$("input[type=checkbox][name=assesmentStdDeviation]").prop("checked",false);
                obj.topicAverageScore==1 ? $("input[type=checkbox][name=topicAverageScore]").prop("checked",true):$("input[type=checkbox][name=topicAverageScore]").prop("checked",false);
                obj.topicMinimumScore==1 ? $("input[type=checkbox][name=topicMinimumScore]").prop("checked",true):$("input[type=checkbox][name=topicMinimumScore]").prop("checked",false);
                obj.topicMaximumScore==1 ? $("input[type=checkbox][name=topicMaximumScore]").prop("checked",true):$("input[type=checkbox][name=topicMaximumScore]").prop("checked",false);
                obj.topicStdDeviation==1 ? $("input[type=checkbox][name=topicStdDeviation]").prop("checked",true):$("input[type=checkbox][name=topicStdDeviation]").prop("checked",false);
                obj.graphicHistogram==1 ? $("input[type=checkbox][name=graphicHistogram]").prop("checked",true):$("input[type=checkbox][name=graphicHistogram]").prop("checked",false);
                obj.graphicTopicScore==1 ? $("input[type=checkbox][name=graphicTopicScore]").prop("checked",true):$("input[type=checkbox][name=graphicTopicScore]").prop("checked",false);

                showSuccessMessage(ttReportTemplateLoaded);
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });

}

/***********************
 * @name DeleteTemplate
 * @desc Delete selected template
 **********************/
function DeleteTemplate(){
        $.ajax({
            url     : "index.php?page=report/deletetemplate",
            type    : "post",
            data    : {
                templateName:$("#template").val(),
            },
            success : function (data){
		if (data=="error"){
		    showErrorMessage(ttReportTemplateDeleteFailed);
		}else{
                showSuccessMessage(ttReportTemplateDelete);
		}
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });

}
function helpjs(){
    $("#dialogError p").html(ttHelpReportAreportTemplate);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}
