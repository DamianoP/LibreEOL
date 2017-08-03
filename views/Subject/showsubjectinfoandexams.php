<?php
/**
 * File: showsubjectinfoandexams.php
 * User: Masterplan
 * Date: 10/07/14
 * Time: 12:48
 * Desc: Show informations abount requested subject with list of all available exams
 */

global $log, $config, $user;

$db = new sqlDB();
if(($db->qSelect('Subjects', 'idSubject', $_POST['idSubject'])) && ($subject = $db->nextRowAssoc())){ ?>

    <label class="b2Space" for="infoName"><?= ttName ?> : </label>
    <input class="readonly" type="text" id="infoName" name="subjectName" size="50" value="<?= $subject['name'] ?>">
    <div class="clearer"></div>

    <label for="infoDesc"><?= ttDescription ?> : </label>
    <textarea class="readonly b2Space rSpace left" id="infoDesc" name="subjectDesc"><?= $subject['description'] ?></textarea>
    <div class="clearer"></div>

    <div id="examsAvailableTableContainer">
        <table id="examsAvailableTable" class="stripe order-column">
            <thead>
                <tr>
                    <th class="eStatus"></th>
                    <th class="eDay"><?= ttDay ?></th>
                    <th class="eTime"><?= ttTime ?></th>
                    <th class="eName"><?= ttExam ?></th>
                    <th class="eRegEnd"><?= ttRegEnd ?></th>
                    <th class="eManage"><?= ttManage ?></th>
                    <th class="eInfo">examInfo</th>
                    <th class="eExamID">examID</th>
                </tr>
            </thead>
            <tbody>
<?php
}else{
    $log->append(__FUNCTION__." : ".$db->getError());
    die("NACK");
}

$statuses = array('w' => array('Waiting', 'Start'),
                  's' => array('Started', 'Stop'),
                  'e' => array('Stopped', 'Start'),
                  'a' => array('Archived', 'Closed'));

$db = new sqlDB();
if(($db->qExamsAvailable($_POST['idSubject'], $user->id)) && ($exams = $db->getResultAssoc('idExam'))){
    foreach($exams as $idExam => $examInfo){
        $name = &$examInfo['name'];
        /*
        $datetime = new DateTime($examInfo['datetime']);
        $day = $datetime->format("d/m/Y");
        $time = $datetime->format("H:i");
        */
        $datetime = strtotime($examInfo['datetime']);
        $day = date('d/m/Y', $datetime);
        $time = date('H:i', $datetime);

        if($examInfo['regEnd'] != ''){
            /*
            $datetime = new DateTime($examInfo['regEnd']);
            $regEnd = $datetime->format("d/m/Y - H:i");
            */
            $datetime = strtotime($examInfo['regEnd']);
            $regEnd = date("d/m/Y - H:i",$datetime);
        }else{
            $regEnd = '';
        }
        $info = ($examInfo['description'] != '') ? $examInfo['description'] : ttNoInfo;
        $action = '<img name="action" src="'.$config['themeImagesDir'].'register.png" onclick="register(new Array(true, this));" title="'.ttRegister.'">';
        if(($db->qCheckRegistration($idExam, $user->id)) && ($db->numResultRows() > 0) && ($testInfo = $db->nextRowAssoc())){
            switch($testInfo['status']){
                // status = w || status = s  =>  do/complete test
                case 'w' :
                case 's' : $action = '<img name="action" src="'.$config['themeImagesDir'].'do.png" onclick="startTest(new Array(true, this));" title="'.ttDo.'">';
                           break;
                // status = e || status = a  =>  test completed
                case 'e' :
                case 'a' : $action = '<img name="action" src="'.$config['themeImagesDir'].'done.png" title="'.ttDone.'">';
                           break;
                // status = b                =>  test blocked
                default : $action = '<img name="action" src="'.$config['themeImagesDir'].'block.png" title="'.ttBlocked.'">';
            }
        }
        ?>
                <tr>
                    <td><img alt="<?= constant('tt'.$statuses[$examInfo['status']][0]) ?>"
                             title="<?= constant('tt'.$statuses[$examInfo['status']][0]) ?>"
                             src="<?= $config['themeImagesDir'].$statuses[$examInfo['status']][0] ?>.png"></td>
                    <td><?= $day ?></td>
                    <td><?= $time ?></td>
                    <td><?= $name ?></td>
                    <td><?= $regEnd ?></td>
                    <td>
                        <img name="action" src="<?= $config['themeImagesDir'] ?>info.png" onclick="showExamInfo(this)" title="<?= ttInfo ?>">
                        <?= $action ?>
                    </td>
                    <td><?= $info ?></td>
                    <td><?= $idExam ?></td>
                </tr>
        <?php
    }
}
?>
            </tbody>
        </table>
    </div>