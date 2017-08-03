/**
 * Created by michele on 22/10/15.
 */
var minscore;//variable that contain the minimal score
var maxscore;//variable that contain the maximum score

/**
 * nextMainTab
 * move on the next tab from maintab
 */
function nextMainTab(){
    $("#groupstab").show();
    $("#maintab").hide();
    $("#t1, #t3").removeClass("active");
    $("#t2").addClass("active");
}

/**
 *  @name   printAssesments
 *  @descr  Shows assesments in the select form of search
 */
function printAssesments(letter,id){
    $.ajax({
        url     : "index.php?page=report/showassesments",
        type    : "post",
        data    : {
            letter : letter,
            idUser : id
        },
        success : function (data){
            $("#crsearched_ass").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   printParticipant
 *  @descr  Shows partecipants in the select form
 */
function printParticipant(){
    minscore=($("#assesmentScore").is(":checked"))? $("#assesmentMinScore").val():-1; //check if minscore is enable and eventually return the value
    maxscore=($("#assesmentScore").is(":checked"))? $("#assesmentMaxScore").val():-1; //check if maxscore is enable and eventually return the value
    $.ajax({
        url     : "index.php?page=report/showstudentcreport",
        type    : "post",
        data : {
            exam : $("#crsearched_ass").val(),
            minscore: minscore,
            maxscore: maxscore,
            datein: $("#crdateIn").val(),
            datefn:$("#crdateFn").val()
        },
        success : function (data){
            $("#crparticipant").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   transferData
 *  @descr  transfer all filter parameters
 */
function transferData(min,max){
    minscore=($("#assesmentScore").is(":checked"))? min:-1; //check if minscore is enable and eventually return the value
    maxscore=($("#assesmentScore").is(":checked"))? max:-1; //check if maxscore is enable and eventually return the value

   if (($("#crsearched_ass").val()==null) || ($("#crparticipant").val()==null)){
       showErrorMessage(ttReportCoachingError);
   }else{
       $.ajax({
           url     : "index.php?page=report/creportparameters",
           type    : "post",
           data    : {
               CRiduser : $("#crparticipant").val(),
               CRexam : $("#crsearched_ass").val(),
               CRminscore: minscore,
               CRmaxscore: maxscore,
               CRdatein: $("#crdateIn").val(),
               CRdatefn:$("#crdateFn").val()
           },
           success : function (data){
              window.location.assign("index.php?page=report/creportlist")
           },
           error : function (request, status, error) {
               alert("jQuery AJAX request error:".error);
           }
       });
    }

}


function unlock(el,el1,el2){
    if(el.checked){
        el1.disabled = false;
        el2.disabled = false;
    }
    else{
        el1.disabled = "disabled";
        el2.disabled = "disabled";
    }
}
function helpjs(){
    $("#dialogError p").html(ttHelpReportCreport);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}