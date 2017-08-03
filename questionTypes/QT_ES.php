<?php
/**
 * File: QT_ES.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Essay questions
 */

class QT_ES extends Question {

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
        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (ES) -->
            <input type="hidden" id="questionType" value="ES">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php
        $this->printQuestionTypeLibrary();
        echo '<script> initialize_ES(); </script>';

    }

    public function printAnswersTable($idQuestion, $idSubject){
        // Essay question doesn't have an answers set
    }

    public function printQuestionPreview(){
        global $config;

        $questionAnswers = '<textarea class="textareaTest"></textarea>';

        // -------  Add extra buttons  ------- //
        $extra = '';
        if(strpos($this->get('extra'), 'c') !== false)
            $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
        if(strpos($this->get('extra'), 'p') !== false)
            $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
        ?>

        <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="ES">
            <div class="questionText"><?= $this->get('translation').$extra ?></div>
            <div class="questionAnswers"><?= $questionAnswers ?></div>
        </div>

        <?php
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;

        $questionAnswers = '<textarea class="textareaTest">'.$answered[0].'</textarea>';

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

        <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="ES">
            <div class="questionText"><?= $this->get('translation').$extra ?></div>
            <div class="questionAnswers"><?= $questionAnswers ?></div>
        </div>

        <?php
        return $extras;

    }

    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){
        global $config;

        $questionAnswers = '<div class="responseES" value="'.$this->get('idQuestion').'">'.$answered[0].'</div>
                            <dl class="dropdownScore">
                                <dt><span>0.0<span class="value">0.0</span></span></dt>
                                <dd>
                                    <ol>';

        $step = $scale / 10;
        $min = round((-1 * $scale), 2);
        $max = round((0 - $step), 2);
        for($index = $min; $index <= $max; $index += $step){
            $questionAnswers .= '<li>'.number_format($index, 2).'<span class="value">'.number_format($index, 2).'</span></li>';
        }
        $questionAnswers .= '<li>0.00<span class="value">0.00</span></li>';
        $min = round((0.1 * $scale), 2);
        $max = round((1 * $scale), 2);
        for($index = $min; $index <= $max; $index += $step){
            $questionAnswers .= '<li>'.number_format($index, 2).'<span class="value">'.number_format($index, 2).'</span></li>';
        }

        $questionAnswers .= '</ol>
                         </dd>
                     </dl>
                     <label class="score">'.ttScore.' : </label>
                     <div class="clearer"></div>';

        $questionClass = (count($answered) != 0)? 'correctQuestion' : 'emptyQuestion';

        ?>

        <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="ES">
            <div class="questionText" onclick="showHide(this);">
                <span class="responseQuestion"></span>
                <?= $this->get('translation') ?>
                <span class="responseScore">0.0</span>
            </div>
            <div class="questionAnswers"><?= $questionAnswers ?></div>
        </div>

        <?php
    }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion){
        $questionAnswers = '<div class="responseES" value="'.$this->get('idQuestion').'">'.$answered[0].'</div>
                                <label class="score">'.ttScore.' : '.$this->get('score').'</label>
                                <div class="clearer"></div>';
        $questionClass = ($this->get('score') > 0) ? 'rightQuestion' : 'wrongQuestion';

        ?>
        <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>">
            <div class="questionText" onclick="showHide(this);">
                <span class="responseQuestion"></span>
                <?= $this->get('translation') ?>
                <span class="responseScore"><?= number_format($this->get('score'), 2); ?></span>
            </div>
            <div class="questionAnswers hidden"><?= $questionAnswers ?></div>
        </div>
        <?php
    }

    public function getScoreFromGivenAnswer(){
        return 0;
    }
}