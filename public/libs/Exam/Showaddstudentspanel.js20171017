/**
 * File: Showaddstudentpanel.js
 * User: Masterplan
 * Date: 14/07/14
 * Time: 18:44
 * Desc: Show add student panel to register students to exam
 */

var studentsTable = null;

var stci = {
    checkbox : 0,
    surname : 1,
    name : 2,
    email : 3,
    studentID : 4
};

$(function(){

    $("#addNewStudent").hide();
    $("#newStudent").on("click", newStudent);

    studentsTable = $("#studentsTable").DataTable({
        scrollY:        200,
        scrollCollapse: false,
        jQueryUI:       true,
        paging:         false,
        order: [[stci.surname, "asc"], [stci.name, "asc"]],
        columns : [
            { className: "sCheckbox", searchable : false, "orderDataType": "dom-checkbox", width : "10px" },
            { className: "sSurname" },
            { className: "sName" },
            { className: "sEmail" },
            { className: "sStudentID", visible : false }
        ],
        language : {
            info: ttDTStudentsInfo,
            infoFiltered: ttDTStudentsFiltered,
            infoEmpty: ttDTStudentsEmpty
        }
    });

    $("#studentsTable_filter").css("margin-right", "50px")
                              .after($("#newStudent").parent())
                              .before($("#studentsTable_info"));

    $("#registrationsList").hide({duration: 400});
});

/**
 *  @name   registerStudents
 *  @param  askConfirmation          Array       if askConfirmation[0] is true show confirm dialog
 *  @descr  Adds students to requested exam
 */
function registerStudents(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCRegisterStudents, registerStudents, new Array(false)))){
        var students = studentsTable.$(".sCheckbox input").serialize().replace(/student=/g, "");
        if(students != ""){
            $.ajax({
                url     : "index.php?page=exam/registerstudents",
                type    : "post",
                data    : {
                    idExam  :   $("#idExam").val(),
                    students:   studentsTable.$(".sCheckbox input").serialize().replace(/student=/g, "")
                },
                success : function (data){
                    if(data == "ACK"){
                        showSuccessMessage(ttMStudentsRegistered);
                        closeAddStudentsPanel();
                    }else{
                        showErrorMessage(data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttENoStudentsToRegister);
        }
    }
}

/**
 *  @name   newStudent
 *  @descr  Shows form to add new student
 */
function newStudent(){
    resetNewStudent();
    $("#addNewStudent").slideDown();
    $("#studentsTableContainer").slideUp();
    $("#addStudentsButtons").slideUp();
}

/**
 *  @name   createStudent
 *  @descr  Creates student from added informations
 */
function createStudent(){
    var name = $("#userName").val().trim();
    var surname = $("#userSurname").val().trim();
    var email = $("#userEmail").val().trim();
    var email2 = $("#userEmail2").val().trim();
    var group = $("#group").val().trim();
    var subgroup = $("#subgroup").val().trim();
    if((name != '') && (surname != '') && (email != '') && (email2 != '')){
        if(email == email2){
            if(isValidEmailAddress(email)){
                $.ajax({
                    url     : "index.php?page=admin/newstudent",
                    type    : "post",
                    data    : {
                        name        :  name,
                        surname     :  surname,
                        email       :  email,
                        password    :  "_",
                        group       :  group,
                        subgroup    :  subgroup
                    },
                    success : function (data) {
                        data = data.split(ajaxSeparator);
                        if(data[0] == "ACK"){
//                            alert(data);
                            showSuccessMessage(ttMUserCreated);
                            studentsTable.row.add([
                                '<input type="checkbox" value="'+data[1]+'" name="student" checked="checked"/>',
                                surname,
                                name,
                                email,
                                data[1]
                            ]).draw();
                            closeNewStudent();
                        }else showErrorMessage(data);
                    },
                    error : function (request, status, error) {
                        alert("jQuery AJAX request error:".error);
                    }
                });
            }else showErrorMessage(ttEEmailNotValid);
        }else showErrorMessage(ttEEmailsNotMatch);
    }else showErrorMessage(ttEEmptyFields)
}

/**
 *  @name   resetNewStudent
 *  @descr  Resets new student form
 */
function resetNewStudent(){
    $("#userName, #userSurname, #userEmail, #userEmail2").val("");
}

/**
 *  @name   closeNewStudent
 *  @descr  Closes new student panel
 */
function closeNewStudent(){
    $("#addNewStudent").slideUp();
    $("#studentsTableContainer").slideDown();
    $("#addStudentsButtons").slideDown();
}

/**
 *  @name   closeAddStudentsPanel
 *  @descr  Closes add students panel
 */
function closeAddStudentsPanel(){
    closeStudentsList();
    closeLightbox($("#addStudentsPanel"));
    setTimeout(function(){showStudentsList(null)}, 500);
}