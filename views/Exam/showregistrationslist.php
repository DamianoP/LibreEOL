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
    openBox(ttRegistrations, 'normal-90%', 'registrationsList');
   $_POST["sortingElem"]=1;
   $_POST["sortingOrder"]=1;
//   openBox($_POST['nameExam'], 'normal-80.1%', 'registrationsList');
    ?>  <script>
            var sortDirection=1;
            var elementSorted=1;
            var sortingDirUsed="asc";
        </script><?php
}else{
	if($_POST["sortingOrder"]==""){
		$_POST["sortingOrder"]=1;
		$_POST["sortingElem"]=1;
	}
    ?>  <script>
            var sortDirection=<?= $_POST["sortingOrder"] ?>;
            var elementSorted=<?= $_POST["sortingElem"] ?>;
            if(sortDirection==1)
                sortingDirUsed="asc";
            else
                sortingDirUsed="desc";
        </script>
    <?php

}

?>
<style>
.ui-state-default{
  background: initial!important;
  font-weight:bold!important;
}
</style>
<script>
function printTableStudentList(){
$(function(){
    if(refreshTable==false){
        registrationsTable = $("#registrationsTable").DataTable({
            "paging":true,
            "pageLength": 30,
            "processing": true,
            deferRender:    true,
            "lengthChange": false,
            data:dataset3,
            scrollY:        200,
            scrollCollapse: false,
            jQueryUI:       true,
            order: [elementSorted, sortingDirUsed],
            columns : [
                { className: "uStatus", searchable : false, type: "alt-string", width : "10px",sortable : false },
                { className: "uName" },
                { className: "uEmail" },
                { className: "uTimeStart"},
                { className: "uTimeEnd"},
                { className: "uTimeUsed"},
                { className: "uScoreTest"},
                { className: "uScoreFinal"},
                { className: "uManage", width : "30px" , searchable : false, sortable : false },
                { className: "uStudentID", visible : false,searchable : false,sortable : false },
                { className: "uTestID", visible : false ,searchable : false,sortable : false}
            ],
            language : {
                info: ttDTRegisteredStudentInfo,
                infoFiltered: ttDTRegisteredStudentFiltered,
                infoEmpty: ttDTRegisteredStudentEmpty
            }
        });
    }
    $("#registrationsTable_filter").css("margin-right", "50px")
                                   .after($("#addStudents").parent())
                                   .before($("#registrationsTable_info"));
    });
}
</script>
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
                <th class="uStatus"     id="0"    onclick="sorting(this)"></th>
                <th class="uName"     	id="1"    onclick="sorting(this)"><?= ttName ?></th>
                <th class="uEmail"     	id="2"    onclick="sorting(this)"><?= ttEmail ?></th>
                <th class="uTimeStart"  id="3"    onclick="sorting(this)"><?= ttTimeStart ?></th>
                <th class="uTimeEnd"    id="4"    onclick="sorting(this)"><?= ttTimeEnd ?></th>
                <th class="uTimeUsed"   id="5"    onclick="sorting(this)"><?= ttTimeUsed ?></th>
                <th class="uScoreTest"  id="6"    onclick="sorting(this)"><?= ttScoreTest ?></th>
                <th class="uScoreFinal" id="7"    onclick="sorting(this)"><?= ttScoreFinal ?></th>
                <th class="uManage"     id="8"    onclick="sorting(this)"><?= ttManage ?></th>
                <th class="uStudentID"  id="9"    onclick="sorting(this)">studentID</th>
                <th class="uTestID"	id="10"    onclick="sorting(this)">testID</th>
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
                    $script= "";
                    $i=0;
                    while ($registration = $db->nextRowAssoc()) {
                        if($i>0)
                            $script.=",";
                        else
                            $i++;
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
                                $end = date('Y-m-d H:i:s', $end);
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
                            $start = date('Y-m-d H:i:s',$start);

                        }
                        $status = $statuses[$registration['status']];
                        $manage = '';
                        $scoreFinal = ($registration['scoreFinal'] > $testsettingInfo['scoreType'])? $testsettingInfo['scoreType'].' '.ttCumLaudae : $registration['scoreFinal'];
                        $imageString="<img src='".$config['themeImagesDir'].$status['imageTitle'].".png' title='".$status['imageTitle']."' alt='".$status['imageTitle']."'/>";
                        $manageString="";
                        if (($examInfo['status'] != 'a') || ($registration['status'] == 'a')){
                            $manageString.="<span class='manageButton ".$status['action']."' style='width:50px;'><img src='".$config['themeImagesDir'].$status['actionIcon'].".png' title='".$status['actionTitle']."' alt='".$status['actionTitle']."' onclick='".$status['actionFunction']."'>";
                            if($registration['status']=='a') {
                                $percentScore = $scoreFinal / $testsettingInfo['scoreType'] * 100 ;
                                    if($percentScore>=30 && $config['dbName']=="echemtest"){
                                        $manageString.="<img style='float:right;' src='".$config['themeImagesDir']."emailCert2.png?ver=2' title='".ttSendCertificate."' alt='".ttSendCertificate."' onclick='sendCertificate(new Array(true, this))'>";
                                    }
                            }
                        }
                        $script.='["'.$imageString.'"
                              ,"'.$registration['surname'] . ' ' . $registration['name'].'"
                              ,"'.$registration['email'].'"
                              ,"'.$start.'"
                              ,"'.$end.'"
                              ,"'.$time.'"
                              ,"'.$registration['scoreTest'].'/'.$testsettingInfo['scoreType'].'"
                              ,"'.$scoreFinal.'/'.$testsettingInfo['scoreType'].'"
                              ,"'.$manageString.'"
                              ,"'.$registration['fkUser'].'"
                              ,"'.$registration['idTest'].'"
                              ]';
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
        echo "<script>var dataset3=[".$script."];</script>";
        ?>

        </tbody>
    </table>
</div>

<form action="" method="post" id="idTestForm" target="_blank">
    <input type="hidden" id="idTest" name="idTest" value="">
</form>

<input type="hidden" id="idExam" value="<?= $_POST['idExam'] ?>"/>
<a class="normal button right lSpace tSpace" onclick="localrefreshStudentsList();"> <?= ttRefresh ?></a>
<a class="normal button right lSpace tSpace" onclick="resultStudent(1)" > <?= ttReport ?> </a>
<a class="normal button tSpace" onclick="closeStudentsList();"> <?= ttClose ?></a>
<div class="clearer"></div>

<?php

if(!(isset($_POST['action'])) || ($_POST['action'] != 'refresh')){
    closeBox();
}
?>
<script>
// aggiunta di Damiano 26 settembre 2017, rende la schermata piu lunga, larga e leggibile
printTableStudentList();
var altezzaStudentList = $(window).height()-350;
if(altezzaStudentList<250){
    altezzaStudentList=250;
}
try{
    document.getElementsByClassName('dataTables_scrollBody')[1].style.height=altezzaStudentList+"px";
}catch(exception){
    console.log("Error: "+exception.message);
}
var firstClick=true;
function sorting(el){
    if(elementSorted!=el.id){
        sortDirection=1;
	firstClick=false;
    }
    else
        if(firstClick==false)
            sortDirection*=-1;
        else 
            firstClick=false;
    elementSorted=el.id;
}
function localrefreshStudentsList(){
    refreshStudentsList(elementSorted,sortDirection);
}

</script>
