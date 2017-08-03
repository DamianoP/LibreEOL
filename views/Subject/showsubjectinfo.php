<?php
/**
 * File: showsubjectinfo.php
 * User: Masterplan
 * Date: 04/06/14
 * Time: 14:29
 * Desc: Shows subject info panel or show empty infos fot new subject
 */

global $log, $config, $user;

$idSubject = '';
$subjectName = ttNewSubject;
$subjectVers='';
$subjectDesc = '';
$subjectLang = 1;       // English
$class = 'writable';
$dropdownClass = '';
$dropdownDTClass = 'writable';

$db = new sqlDB();
if($_POST['action'] == 'show'){
    if(($db->qSelect('Subjects', 'idSubject', $_POST['idSubject'])) && ($subject = $db->nextRowAssoc())){
        $idSubject = $subject['idSubject'];
        $subjectName = $subject['name'];
        $subjectDesc = $subject['description'];
        $subjectLang = $subject['fkLanguage'];
        $subjectVers = $subject['version']==-1 ? "-" : $subject['version'];
        $class = 'readonly';
        $dropdownClass = 'notChange';
        $dropdownDTClass = '';
    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }
}

?>

<label class="b2Space" for="infoName"><?= ttName ?> : </label>
<input class="<?= $class ?>" type="text" id="infoName" name="subjectName" value="<?= $subjectName ?>">
<a id="infoNameChars" class="charsCounter hidden"><?= strlen($subjectName) ?></a>
<div class="clearer"></div>

<label class="b2Space" for="infoVers"><?= ttSbjVers ?> : </label>
<input class="<?= $class ?>" type="text" id="infoVers" name="subjectVers"  value="<?= $subjectVers ?>">

<div class="clearer"></div>

<label for="infoDesc"><?= ttDescription ?> : </label>
<textarea class="<?= $class ?> b2Space left" id="infoDesc" name="subjectDesc"><?= $subjectDesc ?></textarea>
<a id="infoDescChars" class="charsCounter hidden"><?= strlen($subjectDesc) ?></a>
<div class="clearer"></div>

<label class="b2Space" for="infoLanguage"><?= ttMainLanguage ?> : </label>
<dl id="infoLanguage" class="dropdownTranslation <?= $dropdownClass ?>">
    <?php
    if(($db->qSelect("Languages")) && ($langs = $db->getResultAssoc("idLanguage"))){
        echo '<dt class="'.$dropdownDTClass.'"><span><img class="flag" src="'.$config['themeFlagsDir'].$langs[$subjectLang]['alias'].'.gif">'.$langs[$subjectLang]['description'].'<span class="value">1</span></span></dt><dd><ol>';
        foreach($langs as $idLanguage => $lang){
            echo '<li><img class="flag" src="'.$config['themeFlagsDir'].$langs[$idLanguage]['alias'].'.gif">'.$langs[$idLanguage]['description'].'<span class="value">'.$idLanguage.'</span></li>';
        }
    }
    ?>
        </ol>
    </dd>
</dl>
<div class="clearer"></div>

<?php
$script = 'var oldAssignedTeachers = new Array(';
if($_POST['action'] == 'show'){
    if(($db->qTeachers($idSubject)) && ($db->numResultRows()) > 0 && ($teacher = $db->nextRowAssoc())) {
        $script .= "'".$teacher['idUser']."'";
        while($teacher = $db->nextRowAssoc()){
            $script .= ", '".$teacher['idUser']."'";
        }
    }
}
$script .= ');';
?>
<script>
    <?= $script ?>
</script>
<div class="clearer"></div>
