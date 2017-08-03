<?php
/**
 * File: showstudentslist.php
 * User: Masterplan
 * Date: 4/25/13
 * Time: 9:06 AM
 * Desc: Show students registered to requeste exam
 */

global $user, $config, $log;

if(!(isset($_POST['action'])) || ($_POST['action'] != 'refresh')){
    openBox(ttRegistrations, 'normal-70.1%', 'registrationsList');
}

?>

<div id="registrationsTableContainer">
    <div class="smallButtons">
        <div id="addStudents">
            <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
            <?= ttAdd ?>
        </div>
    </div>
    <table id="registrationsTable" class="stripe hover order-column" style="text-align:center;">
        <thead>
            <tr>
                <th class="uStatus"></th>
                <th class="uName"><?= ttName ?></th>
                <th class="uEmail"><?= ttEmail ?></th>
                <th class="uTimeStart"><?= ttTimeStart ?></th>
                <th class="uTimeEnd"><?= ttTimeEnd ?></th>
                <th class="uTimeUsed"><?= ttTimeUsed ?></th>
                <th class="uScoreTest"><?= ttScoreTest ?></th>
                <th class="uScoreFinal"><?= ttScoreFinal ?></th>
                <th class="uManage"><?= ttManage ?></th>
                <th class="uStudentID">studentID</th>
                <th class="uTestID">testID</th>
            </tr>
        </thead>
        <tbody>

        <?php

        $db = new sqlDB();
        if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($examInfo = $db->nextRowAssoc())){
            if(($db->qSelect('TestSettings', 'idTestSetting', $examInfo['fkTestSetting'])) && ($testsettingInfo = $db->nextRowAssoc())){
                if ($db->qExamRegistrationsList($_POST['idExam'])) {
                    /*
                    's' => array('imageTitle' => 'Started',
                            'action' => 'block',
                            'actionIcon' => 'block',
                            'actionTitle' => ttBlock,
                            'actionFunction' => "toggleBlockTest(new Array(true, this));"),
                    */
                    $statuses = array('w' => array('imageTitle' => 'Waiting',
                        'action' => 'block',
                        'actionIcon' => 'block',
                        'actionTitle' => ttBlock,
                        'actionFunction' => "toggleBlockTest(new Array(true, this));"),
                        's' => array('imageTitle' => 'Started',
                            'action' => 'block',
                            'actionIcon' => 'working',
                            'actionTitle' => ttBlock,
                            'actionFunction' => "saveStudentExamProblem(new Array(true, this));"),
                        'e' => array('imageTitle' => 'Ended',
                            'action' => 'correct',
                            'actionIcon' => 'correct',
                            'actionTitle' => ttCorrect,
                            'actionFunction' => "correctTest(this)"),
                        'a' => array('imageTitle' => 'Archived',
                            'action' => 'view',
                            'actionIcon' => 'view',
                            'actionTitle' => ttView,
                            'actionFunction' => "viewTest(this)"),
                        'b' => array('imageTitle' => 'blocked',
                            'action' => 'unblock',
                            'actionIcon' => 'unblock',
                            'actionTitle' => ttUnblock,
                            'actionFunction' => "toggleBlockTest(new Array(true, this));"));
                    while ($registration = $db->nextRowAssoc()) {
                        $start = $end = $time = "";
                        if ($registration['timeStart'] != null) {
                            //$start = new DateTime($registration['timeStart']);
                            $start = strtotime($registration['timeStart']);

                            if ($registration['timeEnd'] != null) {
                                /*
                                $end = new DateTime($registration['timeEnd']);
                                $diff = $start->diff($end);
                                $end = $end->format('H:i:s');
                                */
                                $end = strtotime($registration['timeEnd']);
                                $diff = $end - $start;
                                $end = date('H:i:s', $end);

                            } else {
                                /*
                                $end = new DateTime(date('Y-m-d H:i:s'));
                                $diff = $start->diff($end);
                                $end = '';
                                */
                                $end = strtotime(date('Y-m-d H:i:s'));
                                $diff = $end - $start;
                                $end = '';

                            }

                            $arr['days']=floor($diff/(60*60*24));
                            $diff=$diff-(60*60*24*$arr['days']);
                            $arr['hours']=floor($diff/(60*60));
                            $diff=$diff-(60*60*$arr['hours']);
                            $arr['minutes']=floor($diff/60);
                            $diff=$diff-(60*$arr['minutes']);
                            $arr['seconds']=$diff;

                            //if (date('d',$diff)> 0) {
                            if ($arr['days']> 0) {
                                $time = '> 24 h';
                            } else {
                                //$time = $diff->format("%H:%I:%S");
                                $time = date("H:i:s",mktime($arr['hours'],$arr['minutes'],$arr['seconds']));
                            }
                            //$start = $start->format('H:i:s');
                            $start = date('H:i:s',$start);

                        }
                        $status = $statuses[$registration['status']];
                        $manage = '';

                        $scoreFinal = ($registration['scoreFinal'] > $testsettingInfo['scoreType'])? $testsettingInfo['scoreType'].' '.ttCumLaudae : $registration['scoreFinal'];

                        ?>

                        <tr>
                            <td><img src="<?= $config['themeImagesDir'] . $status['imageTitle'] ?>.png"
                                     title="<?= $status['imageTitle'] ?>" alt="<?= $status['imageTitle'] ?>"/></td>
                            <td><?= $registration['surname'] . ' ' . $registration['name'] ?></td>
                            <td><?= $registration['email'] ?></td>
                            <td><?= $start ?></td>
                            <td><?= $end ?></td>
                            <td><?= $time ?></td>
                            <td><?= $registration['scoreTest'] ?>/<?=$testsettingInfo['scoreType']?></td>
                            <td><?= $scoreFinal ?>/<?=$testsettingInfo['scoreType']?></td>
                            <td>
                                <?php if (($examInfo['status'] != 'a') || ($registration['status'] == 'a')) { ?>
                                    <span class="manageButton <?= $status['action'] ?>">
                                    <img src="<?= $config['themeImagesDir'] . $status['actionIcon'] ?>.png"
                                         title="<?= $status['actionTitle'] ?>" alt="<?= $status['actionTitle'] ?>"
                                         onclick="<?= $status['actionFunction'] ?>">
                                </span>
                                <?php } ?>
                            </td>
                            <td><?= $registration['fkUser'] ?></td>
                            <td><?= $registration['idTest'] ?></td>
                        </tr>
                    <?php
                    }
                }else{
                    $log->append($db->getError());
                }
            }else{
                $log->append($db->getError());
            }
        }else{
            die($db->getError());
        }

        ?>

        </tbody>
    </table>
</div>

<form action="" method="post" id="idTestForm" target="_blank">
    <input type="hidden" id="idTest" name="idTest" value="">
</form>

<input type="hidden" id="idExam" value="<?= $_POST['idExam'] ?>"/>
<a class="normal button right lSpace tSpace" onclick="refreshStudentsList();"> <?= ttRefresh ?></a>
<a class="normal button right lSpace tSpace" onclick="resultStudent()" > <?= ttReport ?> </a>
<a class="normal button tSpace" onclick="closeStudentsList();"> <?= ttClose ?></a>
<div class="clearer"></div>

<?php
if(!(isset($_POST['action'])) || ($_POST['action'] != 'refresh')){
    closeBox();
}
?>