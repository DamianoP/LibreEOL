<?php
/**
 * File: test.php
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */
global $user, $log, $config;
session_start();


?>
<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}
th {
    background-color: #c5d2e0;
    color: black;
}
tr:nth-child(even){background-color: #e8e8e8}
tr:hover {background-color: #d0e0d5}
tr{
  transition-property: background-color;
  transition-duration: 0.2s;
}
</style>
<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">

<?php
if($user->id>0){
    $db = new sqlDB();
    $db->getVotes($user->id); // die("Error");
    if(mysqli_num_rows($db->result)==0) {
        echo "<div style='text-align:center'>".ttDTTestEmpty."</div>";   
        return;
    }
    $dbEchemtest="echemtest";
    echo "<table class='table'>
    <tr style='font-weight:bold'>
        <th>".ttExam."</th>
        <th>".ttSubject."</th>
        <th>".ttReportDateTaken."</th>
        <th>".ttScoreFinal."</th>";
    if($config['dbName']==$dbEchemtest)
        echo '<th onclick="funzionePopup()">'.ttQuestionNotes.'  <img style="width:10pt" src="themes/default/images/help.png" class="__web-inspector-hide-shortcut__">
    </th>';
    echo "</tr>";
   
    while($row = $db->nextRowAssoc()){
        echo "<tr>";
        echo "<td>".$row["Name"]."</td>";
        if (strpos($row["SubjectName"], ' - ') !== false)
               $subject = trim(substr($row["SubjectName"],0,strpos($row["SubjectName"],'-')-1));
        else
           $subject = $row["SubjectName"];
        echo "<td>".$subject."</td>";
        echo "<td>".$row["regStart"]."</td>";
        //echo "<td>".$row["scoreTest"]."/".$row["scoreType"]."</td>";
        echo "<td>".$row["scoreFinal"]."/".$row["scoreType"]."</td>";
        if($config['dbName']==$dbEchemtest){
            if($row["scoreFinal"]=="") 
                echo "<td>".ttWaiting."..</td>";
            else{
                $percentScore = $row["scoreFinal"] / $row["scoreType"] * 100 ;
                $risultato="";
                if ($percentScore < 30){
                    $risultato='NOTPASS';
                }elseif(($percentScore >= 30) && ($percentScore < 50)){
                    $risultato='PASS';
                }elseif(($percentScore >= 50) && ($percentScore < 70)){
                    $risultato='OPTIMUM';
                }elseif($percentScore >= 70){
                    $risultato='EXCELLENT';
                }
                echo "<td>".$risultato."</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>

<script>
var testoTabella=""+
"<table style='font-size:13px;'>"+
"<tr>"+
 "   <td>0</td>"+
  "  <td>>= <?= ttScoreFinal?> <</td>"+
   " <td>30</td>"+
    "<td>NOTPASS</td>"+
"</tr>"+
"<tr>"+
 "   <td>30</td>"+
  "  <td>>= <?= ttScoreFinal?> <</td>"+
   " <td>50</td>"+
    "<td>PASS</td>"+
"</tr>"+
"<tr>"+
 "   <td>50</td>"+
  "  <td>>= <?= ttScoreFinal?> <</td>"+
   " <td>70</td>"+
    "<td>OPTIMUM</td>"+
"</tr>"+
"<tr>"+
 "   <td>70</td>"+
  "  <td>>= <?= ttScoreFinal?> <=</td>"+
   " <td>100</td>"+
    "<td>EXCELLENT</td>"+
"</tr>"+
"</table>";
/*
    '<?= ttScoreFinal?> <30 = NOTPASS<br>'+
    '<?= ttScoreFinal?> <50 = PASS<br>'+
    '<?= ttScoreFinal?> <70 = OPTIMUM<br>'+
    '<?= ttScoreFinal?> >=70 = EXCELLENT<br>'
*/
function funzionePopup() {
    $("#dialogError p").html(testoTabella);
    $("#dialogError").dialog( "option", "title", ttHelpDefault )
                     .dialog("open");
    $(".ui-dialog").css("background", "url('"+imageDir+"helpDialog.png')");
}
</script>
</div>

