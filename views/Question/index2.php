<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 4/4/13
 * Time: 8:50 PM
 * Desc: Show topics and questions about requested subject
 */

global $config, $user;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">

    <?php
    $subjectInfo = null;
    $db = new sqlDB();
    if(($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('idLanguage')) && ($db->qSelect('Subjects', 'idSubject', $_SESSION['idSubject']))){
        $subjectInfo = $db->nextRowAssoc();
        openBox(ttTopics, 'left-18%', 'topicList', array('new-newTopic'));
        if($db->qSelect('Topics', 'fkSubject', $subjectInfo['idSubject'], 'name')){
            echo '<div class="list">
                    <ul>
                        <li><a class="filterQuestion selected" value="-1" onclick="filterQuestionsByTopic(this);">'.$subjectInfo['name'].'</a></li>';
            while($topic = $db->nextRowAssoc()){
                echo '<li class="lPad"><a class="filterQuestion" value="'.$topic['idTopic'].'"
                                            onclick="filterQuestionsByTopic(this);"
                                            ondblclick="showTopicInfo(this);">'.$topic['name'].'</a></li>';
            }
            echo '</ul></div>';
        }else{
            echo ttEDatabase;
        }
        closeBox();
    }else{
        echo ttEDatabase;
    }
    ?>

    <div class="bSpace" id="questionsTableContainer">
        <div class="smallButtons">
            <!--
            <div id="newQuestion">
                <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
                <?= ttNew ?>
            <-->
        </div>
        <table id="questionsTable" class="stripe hover order-column">
            <thead>
                <tr>
                    <th class="qStatus"></th>
                    <th class="qText"><?= ttText ?></th>
                    <th class="qLanguages"><?= ttLanguages ?></th>
                    <th class="qTopic"><?= ttTopic ?></th>
                    <th class="qType"><?= ttType ?></th>
                    <th class="qDifficulty"><?= ttDifficulty ?></th>
                    <th class="qQuestionID">questionID</th>
                    <th class="qTopicID">topicID</th>
                    <th class="qTypeID">typeID</th>
                    <th class="qLanguageID">languageID</th>
                </tr>
            </thead>
            <tbody>

    <?php
    $statuses = array('a' => 'Active',
                      'i' => 'Inactive',
                      'e' => 'Error');
    $maxDifficulty = getMaxQuestionDifficulty();
    $questionTypes = getQuestionTypes();
    if($db->qQuestions($_SESSION['idSubject'], '-1')){
        $idQuestion = "first";
        $status = '';
        $text = '';
        $languages = '';
        $topic = '';
        $idTopic = '';
        $typeID = '';
        $type = '';
        $difficulty = '';
        $languageID = $subjectInfo['fkLanguage'];
        while($question = $db->nextRowAssoc()){
            if($idQuestion == $question['idQuestion']){
                if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                    $text = $question['shortText'];

                $languages .= '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                    class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                    src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
            }else{
                if($idQuestion == 'first'){
                    $idQuestion = $question['idQuestion'];
                    $status = '<img title="'.constant('tt'.$statuses[$question['status']]).'"
                                    value="'.$question['status'].'" alt="'.$statuses[$question['status']].'"
                                    src="'.$config['themeImagesDir'].$statuses[$question['status']].'.png">';
                    if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                        $text = $question['shortText'];
                    $languages = '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                       class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                       src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
                    $topic = $question['name'];
                    $idTopic = $question['idTopic'];
                    $typeID = $question['type'];
                    $type = constant('ttQT'.$question['type']);
                    $difficulty = constant('ttD'.$question['difficulty']);
                }else{
                    echo '<tr>
                              <td>'.$status.'</td>
                              <td>'.$text.'</td>
                              <td>'.$languages.'</td>
                              <td>'.$topic.'</td>
                              <td>'.$type.'</td>
                              <td>'.$difficulty.'</td>
                              <td>'.$idQuestion.'</td>
                              <td>'.$idTopic.'</td>
                              <td>'.$typeID.'</td>
                              <td>'.$languageID.'</td>
                          </tr>';
                    $idQuestion = $question['idQuestion'];
                    $status = '<img title="'.constant('tt'.$statuses[$question['status']]).'"
                                    value="'.$question['status'].'" alt="'.$statuses[$question['status']].'"
                                    src="'.$config['themeImagesDir'].$statuses[$question['status']].'.png">';
                    if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                        $text = $question['shortText'];
                    $languages = '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                       class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                       src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
                    $topic = $question['name'];
                    $idTopic = $question['idTopic'];
                    $typeID = $question['type'];
                    $type = constant('ttQT'.$question['type']);
                    $difficulty = constant('ttD'.$question['difficulty']);
                }

            }
        }
        if($idQuestion != 'first'){
            echo '<tr>
                      <td>'.$status.'</td>
                      <td>'.$text.'</td>
                      <td>'.$languages.'</td>
                      <td>'.$topic.'</td>
                      <td>'.$type.'</td>
                      <td>'.$difficulty.'</td>
                      <td>'.$idQuestion.'</td>
                      <td>'.$idTopic.'</td>
                      <td>'.$typeID.'</td>
                      <td>'.$languageID.'</td>
                  </tr>';
        }
    }
    ?>
            </tbody>
        </table>
    </div>

    <div class="clearer"></div>
    <?php

    openBox(ttLanguages, 'left-15%', 'languageList');
    closeBox();

    openBox(ttQuestionPreview, 'right-85%', 'questionPreview');
    closeBox();

    ?>
    <div class="clearer"></div>
</div>

<?php

// Create a new hidden panel for New Question action

openBox(ttNewQuestion, 'normal-690px', 'newQuestionTypeSelect');
$types = getQuestionTypes();

$options = "";
$descriptions = "";
foreach ($types as $type){
    $options .= '<option value="'.$type.'">'.constant('ttQT'.$type).'</option>';
    $descriptions .= '<div id="QT'.$type.'" class="QTDescription hidden">
                          <p class="bold underline">'.constant('ttQT'.$type).'</p><br/>'.
                          constant('ttQT'.$type.'Description').
                      '</div>';
}

?>

<select id="newQuestionType" size="2" onChange="updateQuestionTypeDescription();">
    <?= $options ?>
</select>

<div class="QTDescription"></div>
<?= $descriptions ?>
<div class="clearer"></div>

<a class="button normal left rSpace tSpace" onclick="closeQuestionTypeSelect();"><?= ttCancel ?></a>
<a class="button blue right tSpace" onclick="newEmptyQuestion();"><?= ttNext ?></a>

<div class="clearer"></div>

<?php closeBox(); ?>

<script>
    var userLang = "<?= $user->lang ?>";
    var allLangs = new Array();
    <?php
    $index = 0;
    foreach($allLangs as $idLanguage => $language){
        echo "allLangs[$index] = JSON.parse('".json_encode($language)."');\n";
        $index++;
    }
    ?>
</script>