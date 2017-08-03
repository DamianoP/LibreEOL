<?php
/**
 * File: QT_YN.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Yes/No questions
 */

class QT_YN extends Question {

    public function createNewQuestion(){
        $idQuestion = parent::createNewQuestion();
        $db = new sqlDB();
        if(!($db->qNewAnswer($idQuestion, 'Y*0', array())) || !($db->qNewAnswer($idQuestion, 'N*0', array()))){
            die($db->getError());
        }
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly){
        global $config, $user;

        $db = new sqlDB();
        if(!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))){
            die($db->getError());
        }
    ?>
        <ul xmlns="http://www.w3.org/1999/html">
            <li><a id="questionTab" href="#question-tab"><?= ttQuestion ?></a></li>
            <?php if($action == 'show') echo '<li><a id="answersTab" href="#answers">'.ttAnswers.'</a></li>'; ?>
        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (YN) -->
            <input type="hidden" id="questionType" value="YN">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php if($action == 'show'){ ?>

        <div id="answers">

            <div class="bSpace" id="answersTableContainer">

                <?php $this->printAnswersTable($this->get('idQuestion'), $_SESSION['idSubject']) ?>

            </div>

            <div class="clearer"></div>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
            <div class="clearer"></div>
        </div>

        <?php
        }
        $this->printQuestionTypeLibrary();
        echo '<script> initialize_YN(); </script>';
    }

    public function printAnswersTable($idQuestion, $idSubject){ ?>

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

            $translation = array('Y' => ttYes, 'N' => ttNo);

            $db = new sqlDB();
            if($db->qSelect('Answers', 'fkQuestion', $idQuestion)){
                while($answer = $db->nextRowAssoc()){
                    $score = explode('*', $answer['score']);           // e.g. 'Y*0'
                    echo '<tr>
                              <td>'.$score[1].'</td>
                              <td>'.$translation[$score[0]].'</td>
                              <td>'.$answer['idAnswer'].'</td>
                          </tr>';
                }
            }
            ?>
            </tbody>
        </table>

    <?php
    }

    public function printQuestionPreview(){
        global $config;

        $translation = array('Y' => ttYes, 'N' => ttNo);

        $db = new sqlDB();
        if(($db->qSelect('Answers', 'fkQuestion', $this->get('idQuestion'))) && ($answerSet = $db->getResultAssoc())){

            $questionAnswers = '';
            foreach($answerSet as $answer){
                $score = explode('*', $answer['score']);           // e.g. 'Y*0'
                $questionAnswers .= '<div>
                                         <input class="hidden" type="radio" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'"/>
                                         <span value="'.$answer['idAnswer'].'"></span>
                                         <label>'.$translation[$score[0]].'</label>
                                     </div>';

            }

            // -------  Add extra buttons  ------- //
            $extra = '';
            if(strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
            if(strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="YN">
                <div class="questionText"><?= $this->get('translation').$extra ?></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>
        <?php
        }
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;

        $translation = array('Y' => ttYes, 'N' => ttNo);

        $db = new sqlDB();
        if(($db->qSelect('Answers', 'fkQuestion', $this->get('idQuestion'))) && ($answerSet = $db->getResultAssoc())){

            $questionAnswers = '';
            shuffle($answerSet);
            foreach($answerSet as $answer){
                $score = explode('*', $answer['score']);           // e.g. 'Y*0'
                $checked = (in_array($answer['idAnswer'], $answered)) ? 'checked' : '';
                $questionAnswers .= '<div>
                                         <input class="hidden" type="radio" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'" '.$checked.'/>
                                         <span value="'.$answer['idAnswer'].'"></span>
                                         <label>'.$translation[$score[0]].'</label>
                                     </div>';

            }

            // -------  Add extra buttons  ------- //
            $extra = '';
            if(strpos($this->get('extra'), 'c') !== false){
                $extras['calculator'] = true;
                $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
            }
            if(strpos($this->get('extra'), 'p') !== false){
                $extras['periodicTable'] = true;
                $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
            }
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="YN">
                <div class="questionText"><?= $this->get('translation').$extra ?></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>

            <?php
            return $extras;
        }else{
            die($db->getError());
        }
    }

    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){
        global $config;

        $translation = array('Y' => ttYes, 'N' => ttNo);

        $questionAnswers = '';
        $questionScore = 0;
        $questionClass = 'emptyQuestion';

        $db = new sqlDB();
        if(($db->qSelect('Answers', 'fkQuestion', $this->get('idQuestion'))) && ($answerSet = $db->getResultAssoc('idAnswer'))){

            $questionAnswers = '';
            foreach($answerSet as $idAnswer => $answer){
                $answerdClass = '';
                $score = explode('*', $answer['score']);           // e.g. 'Y*0'
                $right_wrongClass = ($score[1] > 0) ? 'rightAnswer' : 'wrongAnswer';

                if(in_array($idAnswer, $answered)){
                    $questionScore += round(($score[1] * $scale), 2);
                    $answerdClass = 'answered';
                }

                $questionAnswers .= '<div class="'.$answerdClass.'">
                                         <span value="'.$idAnswer.'" class="responseYN '.$right_wrongClass.'"></span>
                                         <label>'.$translation[$score[0]].'</label>
                                         <label class="score">'.number_format(round($score[1] * $scale, 2), 2).'</label>
                                     </div>';
            }
            $questionAnswers .= '<label class="questionScore">'.number_format($questionScore, 2).'</label>
                                 <div class="clearer"></div>';

            if(count($answered) != 0)
                $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';

            ?>

            <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="YN">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>
                    <?= $this->get('translation') ?>
                    <span class="responseScore"><?= number_format($questionScore, 2); ?></span>
                </div>
                <div class="questionAnswers hidden"><?= $questionAnswers ?></div>
            </div>

            <?php
        }else{
            die(ttEAnswers);
        }
    }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion){
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }

    public function getScoreFromGivenAnswer(){
        $score = 0;

        $answer = json_decode(stripslashes($this->get('answer')), true);
        if(count($answer) > 0){
            $db = new sqlDB();
            if(($db->qSelect('Answers', 'idAnswer', $answer[0])) && ($result = $db->nextRowAssoc())){
                $scores = explode('*', $result['score']);       // e.g. 'Y*0'
                $score = $scores[1];
            }else die($db->getError());
        }

        return $score;
    }
}