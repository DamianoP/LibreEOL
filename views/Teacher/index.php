<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Teacher Homepage
 */

global $config, $user;

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

<div id="main">
    <div id="loader" class="loader"></div>
    <div id="examsTableMinContainer" style="visibility: hidden">
        <?php
        $db = new sqlDB();
        if($db->qExamsInProgress(1,$user->id)){
            echo '<table id="homeExamsTable" class="hover stripe order-column" style="text-align:center;">
                      <thead>
                          <tr>
                              <th class="eStatus"></th>
                              <th class="eName">'.ttName.'</th>
                              <th class="eSubject">'.ttSubject.'</th>
                              <th class="eDay">'.ttDay.'</th>
                              <th class="eTime">'.ttTime.'</th>
                              <th class="eExamID"></th>
                          </tr>
                      </thead>
                      <tbody>';
            $statuses = array('w' => 'Waiting',
                              's' => 'Started',
                              'e' => 'Stopped');
            $i=0;
            $script= "";
            while($examInfo = $db->nextRowAssoc()){
                $status = "<img alt='".$examInfo['status']."' src='".$config['themeImagesDir'].$statuses[$examInfo['status']].".png' title='".constant('tt'.$statuses[$examInfo['status']])."'/>";
                if($i>0)
                      $script.=",";
                else
                      $i++;
                $exam = $examInfo['examName'];
                $subject = $examInfo['subjectName'];

                /*
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");

                */

                $datetime = strtotime($examInfo['datetime']);
                $day = date('d/m/Y', $datetime);
                $time = date('H:i', $datetime);

                $idExam = $examInfo['idExam'];
		$script.='["'.$status.'"
                          ,"'.$exam.'"
                          ,"'.$subject.'"
                          ,"'.$day.'"
                          ,"'.$time.'"
                          ,"'.$idExam.'"
                          ]';
            }
            echo "<script>var dataset1=[".$script."];</script>";
            echo '</tbody>
              </table>';
        }else{
            echo $db->getError();
        }
        ?>
    </div>
    <div id="testsTableContainer" style="visibility: hidden">
        <?php
        $db = new sqlDB();
        $db2 = new sqlDB();
        if($db->qTestsList($user->id)){
            echo '<table id="homeTestsTable" class="hover stripe order-column" style="text-align:center;">
                      <thead>
                          <tr>
                              <th class="tName">'.ttName.'</th>
                              <th class="tSubject">'.ttSubject.'</th>
                              <th class="tTime">'.ttDay.'</th>
                              <th class="tScore">'.ttScoreTest.'</th>
                              <th class="tTestID"></th>
                              <th class="tTestStatus"></th>
                          </tr>
                      </thead>
                      <tbody>';
            $i=0;
            $script= "";
            while($test = $db->nextRowAssoc()){
                if($test['status'] == 'e' || $test['status'] == 'a' ){

                    $subject = $test['subName'];
                    $idTest = $test['idTest'];
                    $testStatus = $test['status'];
                    $score = $test['scoreTest'];
		    if(!isset($test['timeStart'])) 
                      $start="0000-00-00 00:00:00";
                    else
                      $start=$test['timeStart'];
                    if($i>0)
                      $script.=",";
                    else
                      $i++;
                    $script.='["'.$test['surname'].' '.$test['name'].'"
                              ,"'.$subject.'"
                              ,"'.$start.'"
                              ,"'.$score.'"
                              ,"'.$idTest.'"
                              ,"'.$testStatus.'"
                              ]';
/*
                    echo '<tr>
                              <td>'.$test['surname'].' '.$test['name'].'</td>
                              <td>'.$subject.'</td>
                              <td>'.$start.'</td>
                              <td>'.$score.'</td>
                              <td>'.$idTest.'</td>
                              <td>'.$testStatus.'</td>
                          </tr>';
*/
                }
            }
            echo "<script>var dataset=[".$script."];</script>";
            echo '</tbody>
             </table>';
        }
        ?>
    </div>
    <div class="clearer"></div>
</div>

<form action="" method="post" id="form" target="_blank">
    <input type="hidden" name="idExam" value="">
    <input type="hidden" name="idTest" value="">
</form>

<script>
$(document).ready(function() {
    $('#examsTableMinContainer').css("visibility","visible");
    $('#testsTableContainer').css("visibility","visible");
    $('#loader').hide();
});
</script>
