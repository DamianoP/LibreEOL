/**
 * Created by michele on 22/10/15.
 */
var exams=new Array(100);//array of selected exams
var paramuser=""; //variable that contain the user id or email want to show on the report
var groups=new Array(100);//array of selected groups
var minscore;//variable that contain the minimal score of the exam to show
var maxscore;//variable that contain the maximum score of the exam to show

/*************************************
 * Tabs function**
 * This function manage css for active tabs and show or hide the div selected
 * ***********************************
 */
$(document).ready(function(){
    // $("#tab1").click(function(){
    //     $("#partecipantstab").hide();
    //     $("#groupstab").hide();
    //     $("#maintab").show();
    //     $("#t1").addClass("active");
    //     $("#t3, #t2").removeClass("active");
    // });
    // $("#tab2").click(function(){
    //     $("#partecipantstab").hide();
    //     $("#maintab").hide();
    //     $("#groupstab").show();
    //     $("#t1, #t3").removeClass("active");
    //     $("#t2").addClass("active");
    // });
    // $("#tab3").click(function(){
    //     $("#partecipantstab").show();
    //     $("#groupstab").hide();
    //     $("#maintab").hide();
    //     $("#t3").addClass("active");
    //     $("#t1, #t2").removeClass("active");
    // });
});

/**
 * nextMainTab
 * move on the next tab from maintab
 */
function nextMainTab(){
    if($("#selected option").length == 0){
        showErrorMessage(ttReportCoachingError2);
    }else{
        $("#partecipantstab").show();
        $("#maintab").hide();
        $("#t1").removeClass("active");
        $("#t3").addClass("active");
    }

}

/**
 * nextGroupTab
 * move on the next tab from maintab
 */
function nextGroupTab(){

    $("#partecipantstab").show();
    $("#maintab, #groupstab").hide();
    $("#t1, #t2").removeClass("active");
    $("#t3").addClass("active");
}

/**
 * prevGroupTab
 * move on the next tab from maintab
 */
function prevGroupTab(){
    $("#maintab").show();
    $("#partecipantstab, #groupstab").hide();
    $("#t3, #t2").removeClass("active");
    $("#t1").addClass("active");
}


/**
 * prevPartecipantsTab
 * move on the next tab from maintab
 */
function prevPartecipantsTab(){
    $("#maintab").show();
    $("#partecipantstab").hide();
    $("#t3").removeClass("active");
    $("#t1").addClass("active");
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
            $("#searched").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   addAssesments
 *  @descr  Select assesments
 */
function addAssessment(exam){
    var trovato=false;
    for (v=0; v < exams.length; v++) {
        if (exams[v] == exam) {
            trovato = true;
        }
    }
    if (!trovato){
        for (v=0; v < exams.length; v++){
            if (exams[v]==null){
                exams[v]=exam;
                $("#selected").append("<option id=\""+exam.replace(/ /gi,"_")+"\" value=\""+exam.replace(/ /gi,"_")+"\">"+exam+"</option>");
                //console.log(exam);
                break;
            }
        }
    }
}

/**
 *  @name   clearAssesments
 *  @descr  Empty selected assesment form
 */
function clearAssessments(){
    exams=new Array(100);

    $("#selected").html("");

   /* for (t=0; t< exams.length; t++) {
        console.log(exams[t]);
    }*/
}

/**
 *  @name   removeAssesments
 *  @descr  Delete specific selected assessment
 */
function removeAssessment(exam){
    var x=0;
    if(exams[exams.length]==exam.replace(/_/," ")){
        exams[exams.length]=null;
        $("#"+exam.replace(" ","_")).remove();
        x=1;
    }
    for (i = 0; i < exams.length-1; i++){
        if (exams[i]==exam.replace(/_/gi," ")){
            for (r=i; r < exams.length; r++){
                exams[r]=exams[r+1];
            }
        }
    }
    if (x==0){
        exams[i]=null;
        $("#"+exam.replace(" ","_")).remove();
    }


    for (t=0; t< exams.length; t++) {
        console.log(exams[t]);
    }

}

/**
 *  @name   showPartecipant
 *  @descr  Shows lightbox of partecipants
 */
function showPartecipant(){
   $.ajax({
        url     : "index.php?page=report/showpartecipant",
        type    : "post",
        success : function (data){

                $("body").append(data);
                newLightbox($("#partecipants"), {});
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *@name closePartecipant
 *@descr Close lightbox of partecipants
 */
function closePartecipant(){
    closeLightbox($('#partecipants'));
}

function unlock(el,el1,el2){
    if(el.checked){
        el1.disabled = false;
        el2.disabled = false;
        showErrorMessage(ttReportScoreFilter);
    }
    else{
        el1.disabled = "disabled";
        el2.disabled = "disabled";
    }
}

/**
 *  @name   printStudent
 *  @descr  Shows partecipants in the select form of lightbox
 */
function printStudent(){
    /*for (t=0; t< exams.length; t++){
        console.log(exams[t]);
    }*/
    minscore=($("#assesmentScore").is(":checked"))? $("#assesmentMinScore").val():-1; //check if minscore is enable and eventually return the value
    maxscore=($("#assesmentScore").is(":checked"))? $("#assesmentMaxScore").val():-1; //check if maxscore is enable and eventually return the value
    $.ajax({
        url     : "index.php?page=report/showstudent",
        type    : "post",
        data : {
            exams : JSON.stringify(exams),
            groups: JSON.stringify(groups),
            minscore: minscore,
            maxscore: maxscore,
            datein: $("#dateIn").val(),
            datefn:$("#dateFn").val()
        },
        success : function (data){
            $("#searchedstud").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   addStudent
 *  @descr  Show selected student in the textarea of main page
 */
function addStudent(iduser){
    $.ajax({
        url     : "index.php?page=report/addstudent",
        type    : "post",
        data    : {
            iduser: iduser
        },
        success : function (data){
                paramuser=iduser;
                $("#student").html(data);
                closeLightbox($('#partecipants'));

        },
        error : function (request, status, error) {
           alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   removePartecipant
 *  @descr  Remove the selected student from the textarea
 */
function removePartecipant(iduser){
    $("#student").html("");
    paramuser="";
}


/**
 *  @name   showParticipantDetails
 *  @descr  Shows lightbox of partecipants
 */
function showParticipantDetails(){
    $.ajax({
        url     : "index.php?page=report/showparticipantdetails",
        type    : "post",
        success : function (data){
            $("body").append(data);
            newLightbox($("#participantsdetails"), {});
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *@name closePartecipantDetails
 *@descr Close lightbox of partecipant details
 */
function closePartecipantDetails(){
    closeLightbox($('#participantsdetails'));
}

/**
 *  @name   printStudentDetail
 *  @descr  Show students details in the select form of showparticipantdetail ligthbox
 */
function printStudentDetail(){
    $.ajax({
        url     : "index.php?page=report/printparticipantdetails",
        type    : "post",
        data    : {
            iduser : paramuser
        },
        success : function (data){
            $("#detail").html(data);

        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   addStudentDetail
 *  @descr  Show students detail in the relative textarea
 */
function addStudentDetail(param){
            paramuser=param;
            $("#studentDetail").html(param);
            closeLightbox($('#participantsdetails'));
}


/**
 *  @name   removePartecipantDetail
 *  @descr  Remove the detail of selected student from the textarea
 */
function removePartecipantDetail(){
    $("#studentDetail").html("");
    paramuser="";
}

/**
 *  @name   printGroups
 *  @descr  Shows groups in the select form of search
 */
function printGroups(letter){
    minscore=($("#assesmentScore").is(":checked"))? $("#assesmentMinScore").val():-1; //check if minscore is enable and eventually return the value
    maxscore=($("#assesmentScore").is(":checked"))? $("#assesmentMaxScore").val():-1; //check if maxscore is enable and eventually return the value
    $.ajax({
        url     : "index.php?page=report/showgroups",
        type    : "post",
        data    : {
            letter : letter,
            exams : JSON.stringify(exams),
            minscore: minscore,
            maxscore: maxscore,
            datein: $("#dateIn").val(),
            datefn:$("#dateFn").val()
        },
        success : function (data){
            $("#searchedgroup").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   addGroup
 *  @descr  Select assesments
 */
function addGroup(group){
    var trovato=false;
    for (v=0; v < groups.length; v++) {
        if (groups[v] == group) {
            trovato = true;
        }
    }
    if (!trovato){
        for (v=0; v < exams.length; v++){
            if (groups[v]==null){
                groups[v]=group;
                $("#selectedgroup").append("<option id="+group+" value="+group+">"+group+"</option>");
                break;
            }
        }
    }

    /*for (t=0; t< groups.length; t++){
        console.log(groups[t]);
    }*/

}

/**
 *  @name   clearGroups
 *  @descr  Empty selected groups form
 */
function clearGroups(){
    groups=new Array(100);

    $("#selectedgroup").html("");

    /*for (t=0; t< groups.length; t++) {
     console.log(groups[t]);
     }*/
}

/**
 *  @name   removeGroup
 *  @descr  Delete specific selected group
 */
function removeGroup(group){
    var x=0;
    if(groups[groups.length]==group){
        groups[groups.length]=null;
        $("#"+group).remove();
        x=1;
    }
    for (i = 0; i < groups.length-1; i++){
        if (groups[i]==group){
            for (r=i; r < groups.length; r++){
                groups[r]=groups[r+1];
            }
        }
    }
    if (x==0){
        groups[i]=null;
        $("#"+group).remove();
    }


    for (t=0; t< groups.length; t++) {
     console.log(groups[t]);
     }

}

/**
 *  @name   transferData
 *  @descr  transfer all filter parameters to the report template
 */
function transferData(min,max){
    minscore=($("#assesmentScore").is(":checked"))? min:-1; //check if minscore is enable and eventually return the value
    maxscore=($("#assesmentScore").is(":checked"))? max:-1; //check if maxscore is enable and eventually return the value

    $.ajax({
        url     : "index.php?page=report/aoreportparameters",
        type    : "post",
        data    : {
            iduser : paramuser,
            exams : JSON.stringify(exams),
            minscore: minscore,
            maxscore: maxscore,
            groups: JSON.stringify(groups),
            datein: $("#dateIn").val(),
            datefn:$("#dateFn").val()
            },
            success : function (data){
                window.location.assign("index.php?page=report/aoreporttemplate")
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
    });
}
function helpjs(){
    $("#dialogError p").html(ttHelpReportAreport);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");

}