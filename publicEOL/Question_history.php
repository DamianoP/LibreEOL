

<?php

require_once('../includes/essential.php');
require_once ('../includes/config.php');
global $config;
$log = new Log($config);



global $user, $log,$qlog;

?>

<link rel="stylesheet" href="<?=$config["themeDir"];?>style.css" media="screen" type="text/css" />

<?php

if(isset($_GET["id"])){
    $db = new sqlDB();
    $db->result = null;
    $db->mysqli = $db->connect();

    $ActualQuestionid = $_GET["id"];

    $queryParentQuestion="SELECT FkQuestionParent FROM Questions_History WHERE FkQuestion = ".$ActualQuestionid;

    $db->execQuery($queryParentQuestion);
    if ($db->numResultRows()>0){
         while ($row = mysqli_fetch_array($db->result)){
             $idQuestionParent=$row['FkQuestionParent'];
         }
    }

    if (!isset($idQuestionParent)){
        $idQuestionParent=$ActualQuestionid;
    }
    echo '<p id="historyTitle">PARENT ID QUESTION</p>';
    echo '<p id="historyBody">'.$idQuestionParent.'</p>';



    if($idQuestionParent!=$ActualQuestionid){

        $queryHistory = "SELECT FkQuestion, Date FROM Questions_History WHERE FkQuestionParent = ".$idQuestionParent;
        $db->execQuery($queryHistory);
        if($db->numResultRows()>0){
            echo '<p id="historyTitle">HISTORY</p>';
            echo '<p id="historyBody">';
            echo '-  '.$idQuestionParent.'<br>';
            while ($row = mysqli_fetch_array($db->result)){
                echo '-  '.$row['FkQuestion'].'&nbsp;&nbsp;('.$row['Date'].')<br>';
            }
            echo '</p>';
        }

    }


    echo '<p id="historyTitle">ACTUAL ID QUESTION</p>';
    echo '<p id="historyBody">'.$ActualQuestionid.'</p>';
}

?>