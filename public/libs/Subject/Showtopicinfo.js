/**
 * File: Showtopicinfo.js
 * User: Masterplan
 * Date: 17/05/14
 * Time: 11:10
 * Desc: Show topic info panel or show empty infos for new topic
 */

$(function(){

    // read class (for readonly input tag)
    $(".readonly").attr("disabled", "");

});

/**
 *  @name   createTopic
 *  @descr  Create topic with name and description
 */
function createTopic(){
    if($(".overlimit").length > 0){
        showErrorMessage(ttEIncorrectField);
    }else{
        var topicName = $("#infoName").val().trim();
        if(topicName != ""){
            $.ajax({
                url     : "index.php?page=subject/newtopic",
                type    : "post",
                data    : $("#topicInfoForm").serialize(),
                success : function (data, status) {
                    if(!isNaN(parseInt(data))){
//                        alert(data);
                        showSuccessMessage(ttMNewTopic);
                        setTimeout(function(){

                            $("#topicList").find(".boxContent ul").append("" +
                                "<li class='lPad'>" +
                                "   <a class='filterQuestion' value='"+data+"'" +
                                "      onclick='filterQuestionsByTopic(this);'" +
                                "      ondblclick='showTopicInfo(this);'>"+topicName+"</a>" +
                                "</li>");
                            // Add new topic in list
                            // topicRowEdit.text(topicName);

                            closeTopicInfo();
                        }, 750);
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttEEmptyFields);
        }
    }
}

/**
 *  @name   editTopicInfo
 *  @descr  Enable topic's information edits, show Save and Delete button and enable chars control for name and description fields
 */
function editTopicInfo(){
    makeWritable($("#infoName, #infoDesc"));
    $("#saveTopicInfo").removeAttr("style");
    $("#deleteTopic").removeAttr("style");
    $("#editTopicInfo").attr("style", "display:none;");

    enableCharsCounter("infoName", "topicName");
    enableCharsCounter("infoDesc", "topicDesc");
    $("#infoName").focus();

    topicEditing = true;
}

/**
 *  @name   saveTopicInfo
 *  @descr  Save topic info into database
 */
function saveTopicInfo(event){
    if($(".overlimit").length > 0){
        showErrorMessage(ttEIncorrectField);
    }else{
        var topicName = $("#infoName").val().trim();
        if(topicName != ""){
            $.ajax({
                url     : "index.php?page=subject/updatetopicinfo",
                type    : "post",
                data    : $("#topicInfoForm").serialize(),
                success : function (data, status) {
                    if(data == "ACK"){
                        //alert(data);
                        showSuccessMessage(ttMEdit);
                        setTimeout(function(){
                            topicRowEdit.text(topicName);
                            closeTopicInfo();
                        }, 750);
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttEEmptyFields);
        }
    }
}

/**
 *  @name   deleteTopic
 *  @descr  Delete topic and all releated data
 */
function deleteTopic(readyToDelete){
    if(!readyToDelete){
        confirmDialog(ttWarning, ttCDeleteTopic, deleteTopic, true);
    }else{
        $.ajax({
            url     : "index.php?page=subject/deletetopic",
            type    : "post",
            data    : $("#topicInfoForm").serialize(),
            success : function (data, status) {
                if(status == "success"){
                    if(data == "ACK"){
                        //alert(data);
                        showSuccessMessage(ttMTopicDeleted);
                        setTimeout(function(){
                            topicRowEdit.parent().remove();
                            if(topicRowEdit.val() == topicRowSelected.val()){
                                $("#questionList .boxContent").slideUp();
                                $("#questionPreview .boxContent").slideUp();
                            }
                            var questionToDelete = questionsTable.rows().eq(0).filter(function(rowIdx){
                                return !!(questionsTable.cell(rowIdx, qtci.topicID).data() === topicRowSelected.attr("value"));
                            });
                            questionsTable.rows(questionToDelete).remove().draw();
                            closeTopicInfo();
                        }, 750);
                    }else{
                        showErrorMessage(data);
                    }
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   closeTopicInfo
 *  @descr  Close topic's informations; show a confirm dialog preventing to loose changes
 */
function closeTopicInfo(askConfirmation){
    if( (!askConfirmation) || (!topicEditing) || ((topicEditing) && (confirmDialog(ttWarning, ttCDiscardEdits, closeTopicInfo, false))) ){
        closeLightbox($('#topicInfo'));
        topicEditing = false;
        topicRowEdit = null;
    }
}