<?php
/**
 * File: QT_MC.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Multiple Choice questions
 */

class QT_MC extends Question {

    public function createNewQuestion(){
        $idQuestion = parent::createNewQuestion();
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

            <!-- Print hidden field for question's type (MC) -->
            <input type="hidden" id="questionType" value="MC">

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
                <div class="smallButtons">
                    <div id="newAnswer_MC">
                        <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
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
        echo '<script> initialize_MC(); </script>';
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
            $score = array(0 => ttFalse, 1 => ttTrue);

            $db = new sqlDB();
            if($db->qAnswerSet($idQuestion, null, $idSubject)){
                while($answer = $db->nextRowAssoc()){
                    echo '<tr>
                              <td>'.$score[$answer['score']].'</td>
                              <td>'.strip_tags($answer['translation']).'</td>
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
        
        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())){

            $questionAnswers = '';
            foreach($answerSet as $answer){
                $class = '';
                if($answer['fkLanguage'] != $this->get('fkLanguage'))
                    $class = 'mainLang';
                $questionAnswers .= '<div class="'.$class.'">
                                         <input class="hidden" type="radio" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'"/>
                                         <span value="'.$answer['idAnswer'].'"></span>
                                         <label>'.$answer['translation'].'</label>
                                     </div>';

            }

            // -------  Add extra buttons  ------- //
            $extra = '';
            if(strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
            if(strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="MC">
                <div class="questionText"><?= $this->get('translation').$extra ?></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>

        <?php
        }
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;

        $db = new sqlDB();
        
        if(($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $idSubject)) && ($answerSet = $db->getResultAssoc())){

            $questionAnswers = '';
            shuffle($answerSet);

            foreach($answerSet as $answer){
                $checked = (in_array($answer['idAnswer'], $answered)) ? 'checked' : '';
                $questionAnswers .= '<div>
                                         <input class="hidden" type="radio" name="'.$this->get('idQuestion').'" value="'.$answer['idAnswer'].'" '.$checked.'/>
                                         <span value="'.$answer['idAnswer'].'"></span>
                                         <label>'.$answer['translation'].'</label>
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

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="MC">
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

        $questionAnswers = '';
        $questionScore = 0;
        $questionClass = 'emptyQuestion';

        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))){

            foreach($answerSet as $idAnswer => $answer){
                $answerdClass = "";
                $right_wrongClass = ($answer['score'] > 0) ? 'rightAnswer' : 'wrongAnswer';
                if(in_array($idAnswer, $answered)){
                    $questionScore += round(($answer['score'] * $scale), 2);
                    $answerdClass = 'answered';
                }
                $questionAnswers .= '<div class="'.$answerdClass.'">
                                         <span value="'.$idAnswer.'" class="responseMC '.$right_wrongClass.'"></span>
                                         <label>'.$answer['translation'].'</label>
                                         <label class="score">'.number_format(round($answer['score'] * $scale, 2), 2).'</label>
                                     </div>';
            }
            $questionAnswers .= '<label class="questionScore">'.number_format($questionScore, 2).'</label>
                                 <div class="clearer"></div>';

            if(count($answered) != 0)
                $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';

            $responseScore = ($questionScore > 0)? $questionScore : 0;
            ?>

            <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="MC">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>
                    <?= $this->get('translation') ?>
                    <span class="responseScore"><?= number_format($responseScore, 2); ?></span>
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
                $score = $result['score'];
            }else die($db->getError());
        }

        return $score;
    }
}