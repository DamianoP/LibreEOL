<?php
/**
 * File: showtopicinfo.php
 * User: Masterplan
 * Date: 5/14/14
 * Time: 16:37 PM
 * Desc: Show topic info panel or show empty infos for new topic
 */

global $log;

$topicName = ttNewTopic;
$topicDesc = '';
$class = 'writable';
$buttons = '';

if($_POST['action'] == 'show'){
    $db = new sqlDB();
    if($db->qSelect('Topics', 'idTopic', $_POST['idTopic'])){
        if($row = $db->nextRowAssoc()){
            $topicName = $row['name'];
            $topicDesc = $row['description'];
            $class = 'readonly';
            $buttons = '<a class="normal button right" onclick="editTopicInfo();" id="editTopicInfo">'.ttEdit.'</a>
                        <a class="blue button right lSpace" onclick="saveTopicInfo();" id="saveTopicInfo" style="display: none;">'.ttSave.'</a>
                        <a class="red button right" onclick="deleteTopic();" id="deleteTopic" style="display: none;">'.ttDelete.'</a>';
        }
    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }
}elseif($_POST['action'] == 'new'){
    $buttons = '<a class="blue button right lSpace" onclick="createTopic();" id="createTopic">'.ttCreate.'</a>';
}

openBox(ttInfo.": ".$topicName, 'normal-43%', 'topicInfo');

?>

<form id="topicInfoForm" class="infoEdit">
    <p>
        <label for="infoName"><?= ttName ?> : </label>
        <input class="<?= $class ?>" type="text" id="infoName" name="topicName" size="50" value="<?= $topicName ?>">
        <a id="infoNameChars" class="charsCounter hidden"><?= strlen($topicName) ?></a>
    <div class="clearer"></div>
    <p>
        <label for="infoDesc"><?= ttDescription ?> : </label>
        <textarea class="<?= $class ?>" id="infoDesc" name="topicDesc" cols="36" rows="4"><?= $topicDesc ?></textarea>
        <a id="infoDescChars" class="charsCounter hidden"><?= strlen($topicDesc) ?></a>
    <div class="clearer"></div>
    <input type="hidden" name="idTopic" value="<?= $_POST['idTopic'] ?>">
    <input type="hidden" name="idSubject" value="<?= $_SESSION['idSubject'] ?>">
</form>
<div class="clearer"></div>

<?= $buttons ?>
<a class="normal button" id="cancel" onclick="closeTopicInfo(topicEditing);"><?= ttCancel ?></a>

<div class="clearer"></div>

<?php
closeBox();
?>