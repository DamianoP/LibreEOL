/**
 * File: Teacher.js
 * User: Masterplan
 * Date: 3/21/13
 * Time: 11:05 PM
 * Desc: Teacher's Homepage
 */

// Exams Table Column Index
var etci = {
    status : 0,
    exam : 1,
    subject : 2,
    day : 3,
    time : 4,
    examID : 5
};

// Tests Table Column Index
var ttci = {
    name : 0,
    subject : 1,
    time : 2,
    score: 3,
    testID : 4,
    testStatus : 5
};

var examsTable = null;
var testsTable = null;
var altezza = $(window).height()-400;
if(altezza<250){
    altezza=250;
}
$(function(){

    /**
     *  @descr  Exams DataTables initialization
     */
    examsTable = $("#homeExamsTable").DataTable({
        "paging":true,
        "pageLength": 30,
        "processing": true,
        deferRender:    true,
        "lengthChange": false,
        data:dataset1,
        scrollY:        altezza,
        scrollCollapse: false,
        jQueryUI:       true,
        order: [ etci.day, "dsc" ],
        columns : [
            { className: "eStatus", searchable : false, type: "alt-string", width : "10px",sortable : false },
            { className: "eName"},
            { className: "eSubject"},
            { className: "eDay", type: "date-eu"},
            { className: "eTime"},
            { className: "eExamID",searchable : false, visible : false }
        ],
        language : {
            info: ttDTExamInfo,
            infoFiltered: ttDTExamFiltered,
            infoEmpty: ttDTExamEmpty
        }
    }).on("click", "tr", function(){
            goToExam(this);
    });
    $("#homeExamsTable_filter").before($("#homeExamsTable_info"));

    /**
     *  @descr  Tests DataTables initialization
     */
    testsTable = $("#homeTestsTable").DataTable({
        "paging":true,
        "pageLength": 30,
        "processing": true,
        deferRender:    true,
        "lengthChange": false,
        data:dataset,
        scrollY:        altezza,
        scrollCollapse: false,
        jQueryUI:       true,
        order: [[ ttci.time, "dsc" ]],
        columns : [
            { className: "tName"},
            { className: "tSubject"},
            { className: "tTime"},
            { className: "tScore"},
            { className: "tTestID", visible : false,searchable : false },
            { className: "tTestStatus", visible : false,searchable : false }
        ],
        language : {
            info: ttDTTestInfo,
            infoFiltered: ttDTTestFiltered,
            infoEmpty: ttDTTestEmpty
        }
    }).on("click", "tr", function(){
            goToTest(this);
        });
    $("#homeTestsTable_filter").before($("#homeTestsTable_info"));

});

function goToExam(selectedExam){
    var idExam = examsTable.row(selectedExam).data()[etci.examID];    
    $("input[name='idExam']").val(idExam);
    $("#form").attr("action", "index.php?page=exam/exams")
              .attr("target", "").submit();
}


function goToTest(selectedTest){
    var idTest = testsTable.row(selectedTest).data()[ttci.testID];
    $("input[name='idTest']").val(idTest);
    if(testsTable.row(selectedTest).data()[ttci.testStatus].trim()=="e"){
        $("#form").attr("action", "index.php?page=exam/correct")
              .attr("target", "_blank").submit();
    }else{
        $("#form").attr("action", "index.php?page=exam/view")
              .attr("target", "_blank").submit();
    }
    
    
}
