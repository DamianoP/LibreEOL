<?php
/**
 * File: QT_NM.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Numeric questions
 */

/** Classe copiata MR */
class QT_NM extends Question
{

    public function createNewQuestion()
    {
        $idQuestion = parent::createNewQuestion();
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly)
    {
        global $config, $user;

        $db = new sqlDB();
        if (!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))) {
            die($db->getError());
        }
        ?>
        <ul xmlns="http://www.w3.org/1999/html">
            <li><a id="questionTab" href="#question-tab"><?= ttQuestion ?></a></li>
            <?php if ($action == 'show') echo '<li><a id="answersTab" href="#answers">' . ttAnswers . '</a></li>'; ?>
        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (NM) -->
            <input type="hidden" id="questionType" value="NM">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php if ($action == 'show') { ?>

        <div id="answers">

            <div class="bSpace" id="answersTableContainer">
                <div class="smallButtons">
                    <div id="newAnswer_NM">
                        <img class="icon" src="<?= $config['themeImagesDir'] . 'new.png' ?>"/><br/>
                        <?= ttNew ?>
                    </div>
                </div>

                <?php $this->printAnswersTable($this->get('idQuestion'), $_SESSION['idSubject']) ?>

            </div>

            <div class="clearer"></div>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>

            <div class="clearer"></div>
        </div>

    <?php
    }
        $this->printQuestionTypeLibrary();
        echo '<script> initialize_NM(); </script>';
    }

    public function printAnswersTable($idQuestion, $idSubject)
    {
        ?>

        <table id="answersTable" class="stripe hover">
            <thead>
            <tr>
                <th class="aScore"><?= ttScore ?></th>
                <th class="aText"><?= ttText ?></th>
                <th class="aAnswerID">answerID</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new sqlDB();
            if ($db->qAnswerSet($idQuestion, null, $idSubject)) {
                while ($answer = $db->nextRowAssoc()) {
                    echo '<tr>
                              <td>' . $answer['score'] . '</td>
                              <td>' . strip_tags($answer['translation']) . '</td>
                              <td>' . $answer['idAnswer'] . '</td>
                          </tr>';
                }
            }
            ?>
            </tbody>
        </table>

    <?php
    }

    public function printQuestionPreview()
    {
        global $config;

        $db = new sqlDB();
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())) {

            $questionAnswers = '<div>
                                        <input id="inputNumber" type="text" onfocusout="controlNum()">
                                </div> ';
            /**
             * foreach($answerSet as $answer){
             * $class = '';
             * if($answer['fkLanguage'] != $this->get('fkLanguage'))
             * $class = 'mainLang';
             * $questionAnswers .= '<div class="'.$class.'">
             * <input class="hidden" type="checkbox" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'"/>
             * <span value="'.$answer['idAnswer'].'"></span>
             * <label>'.$answer['translation'].'</label>
             * </div>';
             * } */

            // -------  Add extra buttons  ------- //
            $extra = '';
            if (strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
            if (strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="NM">
                <div class="questionText"><?= $this->get('translation') . $extra ?></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>

        <?php
        }
    }

    public function printQuestionInTest($idSubject, $answered, $extras)
    {
        global $config;

        $db = new sqlDB();
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $idSubject)) && ($answerSet = $db->getResultAssoc())) {

            $questionAnswers = '';
            shuffle($answerSet);
            $questionAnswers .= '<div>
                                        <input id="inputNumber" type="text" onfocusout="controlNum()" onchange="this.style.background=myFunction(this.value);">
                                        <!-- <input id="inputNumber" type="text" onblur="controlNum()"> -->
                                </div> ';
            /**foreach($answerSet as $answer){
             * $checked = (in_array($answer['idAnswer'], $answered)) ? 'checked' : '';
             * $questionAnswers .= '<div>
             * <input class="hidden" type="checkbox" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'" '.$checked.'/>
             * <span value="'.$answer['idAnswer'].'"></span>
             * <label>'.$answer['translation'].'</label>
             * </div>';
             * } */

            // -------  Add extra buttons  ------- //
            $extra = '';
            if (strpos($this->get('extra'), 'c') !== false) {
                $extras['calculator'] = true;
                $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
            }
            if (strpos($this->get('extra'), 'p') !== false) {
                $extras['periodicTable'] = true;
                $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
            }
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="NM">
                <div class="questionText"><?= $this->get('translation') . $extra ?></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>

            <?php
            return $extras;
        } else {
            die($db->getError());
        }
    }

    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion)
    {
        global $config, $log;

        $questionAnswers = '';
        $questionScore = 0;
        $questionClass = 'emptyQuestion';

        $db = new sqlDB();

        //var_dump($this->getInfo());

        if (($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))) {

            $questionAnswers = '<div class="responseNM" value="' . $this->get('idQuestion') . '"> <b> Provided response: </b> ' . $answered[0] . '</div>';

            //echo $answerSet['score'];
            foreach ($answerSet as $idAnswer => $answer) {
                $answerdClass = "";
                $right_wrongClass ='wrongAnswer';
                //$right_wrongClass ='rightAnswer';

		$upperBound=$answer['translation']+($answer['translation']/100);
                $lowerBound=$answer['translation']-($answer['translation']/100);
                if(($answerr['0']>=$lowerBound)&&($answerr['0']<=$upperBound)) {
		    $right_wrongClass =  'rightAnswer';
                    $questionScore += round(($answer['score'] * $scale), 1);
                    $answerdClass = 'answered';
                }
                $questionAnswers .= '<div class="' . $answerdClass . '">
                                         <span value="' . $idAnswer . '" class="responseNM ' . $right_wrongClass . '"></span>
                                         <label>' . $answer['translation'] . '</label>
                                         <label class="score">' . round($answer['score'] * $scale, 1) . '</label>
                                     </div>';
            }
            $questionAnswers .= '<label class="questionScore">' . $questionScore . '</label>
                                 <div class="clearer"></div>';

            //if (count($answered) != 0)
$questionClass =$right_wrongClass;
               // $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';
            ?>

            <div class="questionTest <?= $questionClass . ' ' . $lastQuestion ?>"
                 value="<?= $this->get('idQuestion') ?>" type="NM">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>
                    <?= $this->get('translation') ?>
                    <span class="responseScore"><?= number_format($questionScore, 1); ?></span>
                </div>
                <div class="questionAnswers hidden"><?= $questionAnswers ?></div>
            </div>

        <?php
        } else {
            die(ttEAnswers);
        }
    }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion)
    {
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }

    public function getScoreFromGivenAnswer()
    {
        global $log;
        $score = 0;
        $answerr = $this->get('answer'); //24
        $idAns=$this->get('idQuestion');  // 286
        $answerr = json_decode(stripslashes($this->get('answer')), true);
        //$log->append(var_export($answerr, true));
        //$log->append(var_export($answerr[0], true));
        //$log->append(var_export(json_decode(stripslashes($this->get('answer')), true), true));
        //$db->qSelect('answers', 'fkQuestion', $answerr);

        //if(count($answerr) > 0){
            $db = new sqlDB();
            $db2 = new sqlDB();
            if ($pippo=$db->qSelect('Answers', 'fkQuestion', $idAns)) {
                //$aaa=$db->getResultAssoc('idAnswer');
                //$log->append(var_export($aaa, true));
                //$log->append(var_export('pippo=', true));
                //$log->append(var_export($pippo, true));
                //$result = $db->nextRowAssoc();
                //$log->append(var_export($result, true));
                while ($result = $db->nextRowAssoc() ) {
			$find=false;
                    //$log->append(var_export('result1', true));
                    //$log->append(var_export($result, true));
                    //$log->append(var_export($result['idAnswer'], true));
                    $id=$result['idAnswer'];
                    //$log->append(var_export($id, true));
                    if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $id)) {
                        //$traslation = $db->nextRowAssoc();
                        //$log->append(var_export($traslation, true));
                        while ($traslation = $db2->nextRowAssoc() ) {
                            //$log->append(var_export('secondo while', true));
                            //$log->append(var_export($traslation, true));
                            //$log->append(var_export($traslation['translation'], true));
                            //$log->append(var_export($result['score'], true));
                            //$log->append(var_export($traslation['translation'], true));
                            //$log->append(var_export($answerr, true));
 			    $upperBound=$traslation['translation']+($traslation['translation']/100);
			    $lowerBound=$traslation['translation']-($traslation['translation']/100);
                            if(($answerr['0']>=$lowerBound)&&($answerr['0']<=$upperBound)) {
                                $score += $result['score'];
				$find=true;
                                }
                            }
                        }else die($db->getError());
                    }
                }else die($db->getError());
            //}
            return $score;
        }
}











