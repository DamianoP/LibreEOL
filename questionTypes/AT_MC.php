<?php
/**
 * File: AT_MC.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: * Desc: Class for answer in Multiple Choice questions
 */

class AT_MC extends Answer {

    public function printAnswerEditForm($action){
        global $config;

        $db = new sqlDB();
        if(!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))){
            die($db->getError());
        }

        $this->printAnswerTabsAndTextareas();
        ?>

        <div class="clearer bSpace"></div>

        <!-- Print all other answer's info -->
        <?php $this->printAnswerInfoEditForm($action) ?>

        <!-- Print buttons for answer panel -->
        <?php $this->printAnswerEditButtons($action); ?>

        <div class="clearer"></div>

        <?php $this->printAnswerTypeLibrary(); ?>

    <?php
    }

    public function printAnswerInfoEditForm($action){
        global $log;

        $score = array(0 => ttFalse, 1 => ttTrue);
        ?>

        <dl class="dropdownInfo scoreMC tSpace right" id="answerScore">
            <dt class="tSpace writable">
                <span><?= $score[$this->get('score')] ?><span class="value"><?= $this->get('score') ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li><?= ttTrue ?><span class="value">1</span></li>
                    <li><?= ttFalse ?><span class="value">0</span></li>
                </ol>
            </dd>
        </dl>

        <label class="right tSpace"><?= ttScore ?> :</label><div class="clearer"></div>
        <div class="clearer bSpace"></div>

    <?php
    }

    public function getAnswerRowInTable(){
        $score = array(0 => ttFalse, 1 => ttTrue);

        return array($score[$this->get('score')], $this->get('translation'), $this->get('idAnswer'));
    }

    public function getAnswerScore(){
        return $this->get('score');
    }
    public function getScoreFromGivenAnswer(){
        
    }
}