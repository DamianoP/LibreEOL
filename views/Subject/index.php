<?php
/**
 * File: subjects.php
 * User: Masterplan
 * Date: 3/27/13
 * Time: 12:19 PM
 * Desc: Subject main page
 */

global $user, $config;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php

        $request = '1';

        if(isset($_GET['r'])){
            $request = $_GET['r'];
            if($request == 'qstn')
                echo '<div class="msgCenter"> '.ttMSubjectQuestions.'</div>';       // Select subject and go to topics and questions editor
            elseif($request == 'set')
                echo '<div class="msgCenter"> '.ttMSubjectSettings.'</div>';        // Select subject and go to test settings editor
        }

        openBox(ttSelectSubject, 'left', 'subjectsList', array('new'));
        $db = new sqlDB();
        if($db->qSubjects($user->id, $user->role)){
            echo '<div class="list"><ul>';
            while($subject = $db->nextRowAssoc()){
                echo '<li><a class="showSubjectInfo" value="'.$subject['idSubject'].'" onclick="showSubjectInfo(new Array(subjectEditing, this));">'.$subject['name'].'</a></li>';
            }
            echo '</ul></div>';
        }else{
            echo ttEDatabase;
        }
        closeBox();

        openBox(ttInfo, 'right', 'subjectInfo'); ?>

        <form class="infoEdit" onsubmit="return false;"></form>

        <div id="teachersTableContainer" class="b2Space">
            <table id="teachersTable" class="stripe hover order-column">
                <thead>
                    <tr>
                        <th class="tCheckbox"></th>
                        <th class="tSurname"><?= ttSurname ?></th>
                        <th class="tName"><?= ttName ?></th>
                        <th class="tEmail"><?= ttEmail ?></th>
                        <th class="tUserID">userID</th>
                        <th class="tSelected">selected</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                if($db->qTeachers()){
                    while($teacher = $db->nextRowAssoc()){
                        echo '<tr>
                                  <td><input type="checkbox" value="'.$teacher['idUser'].'" name="teacher"></input></td>
                                  <td>'.$teacher['surname'].'</td>
                                  <td>'.$teacher['name'].'</td>
                                  <td>'.$teacher['email'].'</td>
                                  <td>'.$teacher['idUser'].'</td>
                                  <td></td>
                              </tr>';
                    }
                }else{
                    echo($db->getError());
                }
                ?>

                </tbody>
            </table>
        </div>

        <div id="selectPanel" class="hidden">
            <?php
            if($user->role == 'a'){
                echo '<a class="right normal button" onclick="editSubjectInfo();">'.ttEdit.'</a>';
            }else{
                echo '<a class="ok button right lSpace" onclick="selectSubject();">'.ttSelectSubject.'</a>
                      <a class="normal button" onclick="editSubjectInfo();">'.ttEdit.'</a>';
            }
            ?>
        </div>
        <div id="editPanel" class="hidden">
            <a class="ok button right lSpace" id="saveEdit" onclick="saveEdit();"><?= ttSave ?></a>
            <a class="normal button right" id="cancelEdit" onclick="cancelEdit(true);"><?= ttCancel ?></a>
            <a class="delete button left lSpace" id="deleteSubject" onclick="deleteSubject(true);"><?= ttDelete ?></a>
        </div>
        <div id="createPanel" class="hidden">
            <a class="ok button right lSpace" id="createNew" onclick="createNewSubject();"><?= ttCreate ?></a>
            <a class="normal button" id="cancelNew" onclick="cancelNew(true)"><?= ttCancel ?></a>
        </div>
        <div class="clearer"></div>


        <form action="" method="post" id="idSubjectForm">
            <input type="hidden" name="idSubject" value="">
            <input type="hidden" name="request" value="<?= $request ?>">
        </form>

        <?php closeBox(); ?>

        <div class="clearer"></div>
    </div>
</div>