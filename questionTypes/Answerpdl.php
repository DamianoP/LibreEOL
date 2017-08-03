<?php
/**
 * File: Answer.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:40
 * Desc: Answer abstract main class
 */

abstract class Answer {

    protected $allLangs;
    private $info = null;

    /**
     * @param   $aType
     * @param   $answerInfo
     * @return  Answer
     */
    public static function newAnswer($aType, $answerInfo){
        global $config;

        $answerSubClass = 'AT_'.$aType;
        if(file_exists($config['systemQuestionTypesClassDir'].$answerSubClass.'.php')){        // Check if question's type exists
            require_once($config['systemQuestionTypesClassDir'].$answerSubClass.'.php');
            $answer = (object) new $answerSubClass();
            $answer->setInfo($answerInfo);
            $answer->set('type', $aType);
            return $answer;
        }
        return (object) null;
    }

    public function getInfo(){
        return $this->info;
    }

    public function setInfo($info){
        $this->info = $info;
    }

    public function get($var){
        if(isset($this->info[$var])){
            return $this->info[$var];
        }else{
            return "Var '$var' not exists";
        }
    }

    public function set($var, $value){
        $this->info[$var] = $value;
    }



    public function printAnswerTypeLibrary(){
        global $config;
        echo '<script type="text/javascript" src="'.$config['systemQuestionTypesLibDir'].'AT_'.$this->get('type').'.js"></script>';
    }


//------------  Functions used in Answer edit forms  ------------//
    public abstract function printAnswerEditForm($action);

    public function printAnswerTabsAndTextareas(){
        $mainLang = $this->get('fkLanguage');
        $temp = $this->newAnswerTabAndTextarea($mainLang, $active=true);
        $tabs = $temp[0];
        $textareas = $temp[1];
        foreach($this->allLangs as $idLanguage => $language)
            if($idLanguage != $mainLang){
                $temp = $this->newAnswerTabAndTextarea($idLanguage);
                $tabs .= $temp[0];
                $textareas .= $temp[1];
            }

        // Print answer languages tabs
        echo '<div id="aLangsTabs">'.$tabs.'</div>';

        // Print answer translations textareas used by ckeditor
        echo '<div id="aEditor">'.$textareas.'</div>';
    }
    public function printSubTabsAndTextareas(){
        $mainLang = $this->get('fkLanguage');
        $temp = $this->newSubTabAndTextarea($mainLang, $active=true);
        $tabs = $temp[0];
        $textareas = $temp[1];
        foreach($this->allLangs as $idLanguage => $language)
            if($idLanguage != $mainLang){
                $temp = $this->newSubTabAndTextarea($idLanguage);
                $tabs .= $temp[0];
                $textareas .= $temp[1];
            }

        // Print answer languages tabs
        echo '<div id="aLangsTabs">'.$tabs.'</div>';

        // Print answer translations textareas used by ckeditor
        echo '<div id="aEditor">'.$textareas.'</div>';
    }

    private function newAnswerTabAndTextarea($idLanguage, $active=false){
        global $config;

        $active = ($active)? 'active' : '';
        $hidden = ($active)? '' : 'hidden';
        if(array_key_exists($idLanguage, $this->get('ATranslations'))){
            $translation = $this->get('ATranslations');
            $translation = $translation[$idLanguage]['translation'];
        }else
            $translation = '';
        $alias =  $this->allLangs[$idLanguage]['alias'];
        $description = $this->allLangs[$idLanguage]['description'];

        $tab = '<a class="tab '.$active.'" value="'.$idLanguage.'">
                    <img title="'.$description.'" class="flag" src="'.$config['themeFlagsDir'].$alias.'.gif">'
            .strtoupper($alias).'
                </a>';
        $textarea = '<textarea class="ckeditor hidden" name="at'.$idLanguage.'"
                          id="at'.$idLanguage.'">'.$translation.'</textarea>';
        return array($tab, $textarea);
    }

    private function newSubTabAndTextarea($idLanguage, $active=false){
        global $config;

        $active = ($active)? 'active' : '';
        $hidden = ($active)? '' : 'hidden';
        if(array_key_exists($idLanguage, $this->get('text'))){
            $translation = $this->get('text');
            $translation = $translation[$idLanguage]['text'];
        }else
            $translation = '';
        $alias =  $this->allLangs[$idLanguage]['alias'];
        $description = $this->allLangs[$idLanguage]['description'];

        $tab = '<a class="tab '.$active.'" value="'.$idLanguage.'">
                    <img title="'.$description.'" class="flag" src="'.$config['themeFlagsDir'].$alias.'.gif">'
            .strtoupper($alias).'
                </a>';
        $textarea = '<textarea class="ckeditor hidden" name="at'.$idLanguage.'"
                          id="at'.$idLanguage.'">'.$translation.'</textarea>';
        return array($tab, $textarea);
    }

 public abstract function printAnswerInfoEditForm($action);
    public abstract function printSubInfoEditForm($action);

    public function printAnswerEditButtons($action){
        if($action == 'show'){ ?>
            <a class="button normal left rSpace tSpace" onclick="closeAnswerInfo(answerEditing);"><?= ttExit ?></a>
            <a class="button blue right lSpace tSpace" onclick="saveAnswerInfo_<?= $this->get('type') ?>(closePanel = true);"><?= ttSave ?></a>
            <a class="button red right left tSpace" onclick="deleteAnswer_<?= $this->get('type') ?>(askConfirmation = true);" id="deleteAnswer"><?= ttDelete ?></a>
        <?php }else{ ?>
            <a class="button blue right lSpace tSpace" onclick="createNewAnswer_<?= $this->get('type') ?>(closePanel = true);" id="createNewAnswer"><?= ttCreate ?></a>
            <a class="button normal left tSpace" onclick="cancelNewAnswer(askConfirmation = true);" id="cancelNewAnswer"><?= ttCancel ?></a>
        <?php
        }
    }
    public function printAnswerEditButtonsPL($action){
        if($action == 'show'){ ?>
            <a class="button normal left rSpace tSpace" onclick="closeAnswerInfo(answerEditing);"><?= ttExit ?></a>

            <a class="button blue right lSpace tSpace" onclick="saveAnswerInfo_<?= $this->get('type') ?>(closePanel = true);"><?= ttSave ?></a>
            <a class="button red right left tSpace" onclick="deleteAnswer_<?= $this->get('type') ?>(askConfirmation = true);" id="deleteAnswer"><?= ttDelete ?></a>
        <?php }else{ ?>
            <a class="button blue right lSpace tSpace" onclick="createNewSubQuestion_<?= $this->get('type') ?>(closePanel = true);" id="createNewAnswer"><?= ttCreate ?></a>
            <a class="button normal left tSpace" onclick="cancelNewAnswer(askConfirmation = true);" id="cancelNewAnswer"><?= ttCancel ?></a>
        <?php
        }
    }

    public abstract function getAnswerRowInTable();

    public abstract function getAnswerScore();

    public abstract function getScoreFromGivenAnswer();

}