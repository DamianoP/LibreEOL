<?php
/**
 * File: exams.php
 * User: Masterplan
 * Date: 4/22/13
 * Time: 5:08 PM
 * Desc: Shows exams list
 */
global $log, $config;

$examToOpen = '-1';
if(isset($_POST['idExam'])){
    $examToOpen = $_POST['idExam'];
}

?>
<style>
.ui-state-default{
  background: initial!important;
  font-weight:bold!important;
}
</style>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="loader" class="loader" onclick=""></div>
<div id="main">

    <!--  -->
    
    <div>
        <div id="examsTableContainer" style="visibility: hidden; text-align:center;">
            <div class="smallButtons">
                <button class="button" onclick="resultExams()"><?= ttReport ?></button>
                <div id="newExam">
                    <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
                    <?= ttNew ?>
                </div>
            </div>
            <?php
            $statuses = array('w' => array('Waiting', 'Start'),
                              's' => array('Started', 'Stop'),
                              'e' => array('Stopped', 'Start'),
                              'a' => array('Archived', 'Closed'));
            $db = new sqlDB();
            if($db->qExams()){
            ?>
                <table id="examsTable" class="hover stripe order-column">
                    <thead>
                        <tr>
                            <th class="eStatus"></th>
                            <th class="eDay"><?= ttDay ?></th>
                            <th class="eTime"><?= ttTime ?></th>
                            <th class="eName"><?= ttExam ?></th>
                            <th class="eSubject"><?= ttSubject ?></th>
                            <th class="eSettings"><?= ttSettings ?></th>

                            <th class="eNumTests"><?= ttStudents ?></th>
                            <th class="eGroups"><?=ttGroup?></th>

                            <th class="ePassword"><?= ttPassword ?></th>
                            <th class="eManage"><?= ttManage ?></th>
                            <th class="eExamID">examID</th>
                            <th class="eSubjectID">subjectID</th>
                            <th class="eSettingsID">settingsID</th>
                            <th class="eStatusID">statusID</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                $script= "";
                $i=0;
                while($exam = $db->nextRowAssoc()){
                    if($idExam == $examToOpen)
                        $status = "<img class='selected' alt='".constant('tt'.$statuses[$exam['status']][0])." title='".constant('tt'.$statuses[$exam['status']][0])."' src='".$config['themeImagesDir'].$statuses[$exam['status']][0].".png'>";
                    else
                        $status = "<img alt='".constant('tt'.$statuses[$exam['status']][0])." title='".constant('tt'.$statuses[$exam['status']][0])."' src='".$config['themeImagesDir'].$statuses[$exam['status']][0].".png'>";
                    $name = $exam['exam'];
                    $subject = $exam['subject'];
                    $settings = $exam['settings'];


                    $db2 = new sqlDB();
                    $subGroupDescription="";
                    if($exam['EsubG']!=null){
                        if($db2->qGetSubGroupName($exam['EsubG'])){
                            $subGroupDescription=$db2->nextRowAssoc()["NameSubGroup"];
                        }
                    }
                    $numberOfStudents=0;
                    if($exam['idExam']!=null){
                        if($db2->qCountStudentForExam($exam['idExam'])){
                            $numberOfStudents=$db2->nextRowAssoc()["numberOfStudents"];
                            if($numberOfStudents=="")
                                $numberOfStudents=0;
                        }
                    }
                    /*
                    $datetime = new DateTime($exam['datetime']);
                    $day = $datetime->format("d/m/Y");
                    $time = $datetime->format("H:i");
                    */
                    $datetime = strtotime($exam['datetime']);
                    $day = date('d/m/Y', $datetime);
                    $time = date('H:i', $datetime);

                    $password = $exam['password'];
                    $idExam = $exam['idExam'];
                    $idSubject = $exam['idSubject'];
                    $idTestSetting = $exam['idTestSetting'];
                    $statusID = $exam['status'];
                    if($i>0)
                      $script.=",";
                    else
                      $i++;
                    $lastElement="<span class='manageButton edit'><img name='edit' src='".$config['themeImagesDir']."edit.png' title='".ttEdit."' onclick='showExamInfo(this);'></span><span class='manageButton students'><img name='students' src='".$config['themeImagesDir']."users.png' title='".ttStudents."'onclick='showStudentsList(this);'></span><span class='manageButton action'>";
                    if($statusID != 'a'){
                        $lastElement.="<img name='action' src='".$config['themeImagesDir'].$statuses[$exam['status']][1].".png' title='".constant('tt'.$statuses[$exam['status']][1])."' onclick='changeExamStatus(new Array(true, this));'></span><span class='manageButton archive'><img name='archive' src='".$config['themeImagesDir']."Archive.png' title='".ttArchive."'' onclick='archiveExam(new Array(true, this));'></span>";
                    }else{
                        $lastElement.="</span><span class='manageButton archive'></span>";
                    }

                    $script.='["'.$status.'"
                              ,"'.$day.'"
                              ,"'.$time.'"
                              ,"'.$subject.'"
                              ,"'.$name.'"
                              ,"'.$settings.'"
                              ,"'.$numberOfStudents.'"
                              ,"'.$subGroupDescription.'"
                              ,"'.$password.'"
                              ,"'.$lastElement.'"
                              ,"'.$idExam.'"
                              ,"'.$idSubject.'"
                              ,"'.$idTestSetting.'"
                              ,"'.$statusID.'"
                              ]';

                ?>
<?php /*  			
                            <span class="manageButton delete">
                               <img name="delete" src="<?= $config['themeImagesDir'] ?>delete.png" title="<?= ttDelete ?>" onclick="deleteExam(new Array(true, this));">
                            </span>
*/ 
?><?php
                }
            }
            ?>
                    </tbody>
                </table>
            <div class="clearer"></div>
        </div>

    </div>
</div>

<?php if(isset($_POST['idExam'])){ ?>

    <script>
        $(function(){
            showStudentsList(null,<?= $_POST['idExam'] ?>);
        });
    </script>

<?php } echo "<script>var dataset=[".$script."];</script>";?>

<script>
$(document).ready(function() {
    $('#examsTableContainer').css("visibility","visible");
    $('#loader').hide();
});
</script>
