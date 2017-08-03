<?php
/**
 * File: showaddstudentpanel.php
 * User: Masterplan
 * Date: 14/07/14
 * Time: 18:44
 * Desc: Shows add student panel to register students to exam
 */

global $user, $config, $log;

openBox(ttStudents, 'normal-50%', 'addStudentsPanel');

?>

    <div id="studentsTableContainer">
        <div class="smallButtons">
            <div id="newStudent">
                <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
                <?= ttNew ?>
            </div>
        </div>
        <table id="studentsTable" class="stripe hover order-column" width="50%">
            <thead>
            <tr>
                <th class="sCheckbox"></th>
                <th class="sSurname"><?= ttSurname ?></th>
                <th class="sName"><?= ttName ?></th>
                <th class="sEmail"><?= ttEmail ?></th>
                <th class="sStudentID">studentID</th>
            </tr>
            </thead>
            <tbody>

            <?php

            $db = new sqlDB();
            if($db->qStudentsNotRegistered($_POST['idExam'],$user->id)){
                while($student = $db->nextRowAssoc()){
                    ?>
                    <tr>
                        <td><input type="checkbox" value="<?= $student['idUser'] ?>" name="student" /></td>
                        <td><?= $student['surname'] ?></td>
                        <td><?= $student['name'] ?></td>
                        <td><?= $student['email'] ?></td>
                        <td><?= $student['idUser'] ?></td>
                    </tr>
                <?php
                }
            }else{
                $log->append($db->getError());
            }

            ?>

            </tbody>
        </table>
    </div>

    <div id="addNewStudent" style="display:none;">
        <form class="infoEdit" onsubmit="return false;">
            <label class="b2Space"><?= ttName ?> : </label>
            <input class="writable" type="text" id="userName" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttSurname ?> : </label>
            <input class="writable" type="text" id="userSurname" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttEmail ?> : </label>
            <input class="writable" type="text" id="userEmail" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttConfirmEmail ?> : </label>
            <input class="writable" type="text" id="userEmail2" size="75%" value="">
            <div class="clearer"></div>
            <input type="hidden" id="group" value= 
                <?php
                    if($db->qGetGroup($user->id)){
                        $group = $db->nextRowAssoc();
                        echo $group['group'];
                    }else{
                        $log->append($db->getError());
                    }
                ?>           
            >
            <input type="hidden" id="subgroup" value=
                <?php
                    if($db->qGetSubGroup($user->id)){
                        $group = $db->nextRowAssoc();
                        echo $group['subgroup'];
                    }else{
                        $log->append($db->getError());
                    }
                ?>     
            >
            <a class="normal button tSpace" onclick="closeNewStudent();"> <?= ttCancel ?></a>
            <a class="ok button right lSpace tSpace" onclick="createStudent();"> <?= ttCreate ?></a>
            <a class="red button right lSpace tSpace" onclick="resetNewStudent();"> <?= ttReset ?></a>
            <div class="clearer"></div>
        </form>
    </div>

    <div id="addStudentsButtons">
        <a class="normal button tSpace" onclick="closeAddStudentsPanel();"> <?= ttClose ?></a>
        <a class="ok button right lSpace tSpace" onclick="registerStudents(new Array(true));"> <?= ttAddSelectedStudents ?></a>
        <div class="clearer"></div>
    </div>

<?php closeBox() ?>