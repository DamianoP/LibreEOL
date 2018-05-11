<?php
/**
 * File: Question.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:40
 * Desc: Question abstract main class
 */

abstract class Question {

    protected $allLangs;
    private $info = null;

    /**
     * @param   $qType
     * @param   $questionInfo
     * @return  Question
     */
    public static function newQuestion($qType, $questionInfo=array()){

        global $config;
        global $log;
        $questionSubClass = 'QT_'.$qType;
        if(file_exists($config['systemQuestionTypesClassDir'].$questionSubClass.'.php')){        // Check if question's type exists
           require_once($config['systemQuestionTypesClassDir'].$questionSubClass.'.php');
            $question = (object) new $questionSubClass();
            $question->setInfo($questionInfo);
            $question->set('type', $qType);
            return $question;
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
    public function getRAsspc($i,$var)
    {
        global $log;
        if(isset($this->info[$i][$var])){
            return $this->info[$i][$var];
        }else{
            return "Var '$var' not exists";
        }


    }

    public function getRA($var)
    {
        global $log;


        for ($i=0;$i<=5;$i++)
            if(isset($this->info[$i][$var])){

                return $this->info[$i][$var];

            }else{
                return "Var '$var' not exists";
            }


    }
    public function set($var, $value){
        $this->info[$var] = $value;
    }



    public function printQuestionTypeLibrary(){
        global $config;
        echo '<script type="text/javascript" src="'.$config['systemQuestionTypesLibDir'].'QT_'.$this->get('type').'.js"></script>';
    }



    public function createNewQuestion(){
        global $log;
        $db = new sqlDB();
        $db2 = new sqlDB();
        if($db->qNewQuestion($this->get('idTopic'), $this->get('type'), $this->get('difficulty'), $this->get('extras'), $this->get('shortText'), $this->get('translationsQ'))){
            if(($idQuestion = $db->nextRowEnum()) && ($idQuestion = $idQuestion[0])){
                if($db2->qGetSettingsOnNewQuestion($this->get('idTopic'))){
                    $testSetting = $db2->getResultAssoc('idTestSetting');
                    /*
                    foreach ($testSetting as $idtestSetting => $test){
                        $db2->qInsertQuestionsDistributionOnNewQuestion($idtestSetting,$idQuestion);
                    }
                    */
                }
                $db2->close();
                return $idQuestion;
            }else{
                $log->append("createNewQuestion Error !");
                die($db->getError());
            }
        }else{
            $log->append("createNewQuestion Error2 !");
            die($db->getError());
        }
    }


//------------  Functions used in Question edit forms  ------------//

    public function printQuestionTabsAndTextareas(){
        $mainLang = $this->get('fkLanguage');
        $temp = $this->newQuestionTabAndTextarea($mainLang, $active=true);
        $tabs = $temp[0];
        $textareas = $temp[1];
        foreach($this->allLangs as $idLanguage => $language)
            if($idLanguage != $mainLang){
                $temp = $this->newQuestionTabAndTextarea($idLanguage);
                $tabs .= $temp[0];
                $textareas .= $temp[1];
            }

        // Print question languages tabs
        echo '<div id="qLangsTabs">'.$tabs.'</div>';

        // Print question translations textareas used by ckeditor
        echo '<div id="qEditor">'.$textareas .'</div>';
    }

    public function newQuestionTabAndTextarea($idLanguage, $active=false){
        global $config;

        $active = ($active)? 'active' : '';
        if(array_key_exists($idLanguage, $this->get('QTranslations'))){
            $translation = $this->get('QTranslations');
            $translation = $translation[$idLanguage]['translation'];
        }else
            $translation = '';
        $alias =  $this->allLangs[$idLanguage]['alias'];
        $description = $this->allLangs[$idLanguage]['description'];

        $tab = '<a class="tab '.$active.'" value="'.$idLanguage.'">
                    <img title="'.$description.'" class="flag" src="'.$config['themeFlagsDir'].$alias.'.gif">'
            .strtoupper($alias).'
                </a>';
        $textarea = '<textarea class="ckeditor hidden" name="qt'.$idLanguage.'"
                          id="qt'.$idLanguage.'" onchange="function(){questionEditing = true;}">'.$translation.'</textarea>';
        return array($tab, $textarea);
    }

    public function printQuestionExtraForm($action){
        openBox(ttExtra, "right", "questionExtra");

        $extras = getQuestionExtras();
        $checked = "";
        echo '<div class="list"><ul>';
        foreach($extras as $extra){
            if($action == 'show')
                $checked = strpos($this->get('extra'), $extra) === false ? '' : 'checked';
            echo '<li>
                              <input type="checkbox" value="'.$extra.'" name="extra" '.$checked.'>
                              <a class="questionExtra">'.constant('ttQE'.$extra).'</a>
                          </li>';
        }
        echo '</ul></div>';
        closeBox();
    }

    public function printQuestionInfoEditForm($action, $readonly){
        $editClass = ($readonly)? "readonly" : "writable"; ?>

        <label class="left"><?= ttDescription ?> : </label>
        <input type="text" class="writable right" style="width: 88%" name="description" id="qDescription" value="<?= ($action == 'show')? $this->get('shortText') : "" ?>">
        <div class="clearer bSpace"></div>

        <div class="right">
            <label class="left"><?= ttDifficulty ?> : </label>
            <dl class="dropdownInfo" id="questionDifficulty">
            <?php
            if($action == 'show')
                echo '<dt class="'.$editClass.'"><span>'.constant('ttD'.$this->get('difficulty')).'<span class="value">'.$this->get('difficulty').'</span></span></dt>';
            else
                echo '<dt class="'.$editClass.'"><span>'.ttD1.'<span class="value">1</span></span></dt>';
            if(!$readonly){ ?>
                <dd>
                    <ol>
                        <?php
                        $maxdifficulty = getMaxQuestionDifficulty();
                        $index = 1;
                        while($index <= $maxdifficulty){
                            echo '<li>'.constant('ttD'.$index).'<span class="value">'.$index.'</span></li>';
                            $index++;
                        }
                        ?>
                    </ol>
                </dd>
            <?php } ?>
        </dl>
        </div>

        <div class="right">
            <label class="left"><?= ttTopic ?> : </label>
            <dl class="dropdownInfo r2Space" id="questionTopic">
                <?php
                $db = new sqlDB();
                if(($db->qSelect('Topics', 'fkSubject', $_SESSION['idSubject'], 'name')) && ($topics = $db->getResultAssoc())){
                    if(count($topics) > 0){

                        $requestedTopic = '-1';
                        $selectedTopic = '';
                        $otherTopics = '';

                        if($_POST['action'] == 'show'){                 // Question's stored topic
                            $requestedTopic = $this->get('fkTopic');
                        }elseif($_POST['topic'] != '-1'){               // New question without topic filtering
                            $requestedTopic = $_POST['topic'];
                        }else{                                          // New question with topic filtering
                            $requestedTopic = $topics[0]['idTopic'];
                        }

                        foreach($topics as $topic){
                            if($topic['idTopic'] == $requestedTopic)
                                $selectedTopic = '<dt class="'.$editClass.'"><span>'.$topic['name'].'<span class="value">'.$topic['idTopic'].'</span></span></dt>';
                            $otherTopics .= '<li>'.$topic['name'].'<span class="value">'.$topic['idTopic'].'</span></li>';
                        }

                        echo $selectedTopic;
                        if(!$readonly){
                            echo '<dd><ol>';
                            echo $otherTopics;
                            echo '</ol></dd>';
                        }
                    }else{
                        echo '<dt><span> --- <span class="value">-1</span></span></dt>
                              <dd>
                                  <ol>
                                      <li> --- <span class="value">-1</span></li>
                                  </ol>
                              </dd>';
                    }
                }
                ?>
            </dl>
        </div>

        <?php
    }

    public function printQuestionEditButtons($action, $readonly){
        if($action == 'show'){ ?>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(questionEditing);"><?= ttExit ?></a>
            <a class="button blue right lSpace tSpace" onclick="saveQuestionInfo_<?= $this->get('type') ?>(close = true);"><?= ttSave ?></a>
            <?php if(!$readonly){ ?>
                <a class="button red right tSpace" onclick="deleteQuestion(ask = true);" id="deleteQuestion"><?= ttDelete ?></a>
            <?php }
        }else{ ?>
            <a class="button normal left rSpace tSpace" onclick="cancelNewQuestion(ask = true);"><?= ttCancel ?></a>
            <a class="button blue right lSpace tSpace" onclick="createNewQuestion_<?= $this->get('type') ?>();"><?= ttCreate ?></a>
        <?php
        }
    }
    /**
     * @param $action
     * @param $readonly
     */
    public function printQuestionEditButtonsPL($action, $readonly){
        if($action == 'show'){ ?>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(questionEditing);"><?= ttExit ?></a>
            <a class="button blue right lSpace tSpace" onclick="saveQuestionInfo_<?= $this->get('type') ?>(close = true);"><?= ttSave ?></a>
            <a class="button blue right lSpace tSpace" onclick="createNewQuestion_();"><?= ttCreatePL2 ?></a>
            <?php if(!$readonly){ ?>
                <a class="button red right tSpace" onclick="deleteQuestion(ask = true);" id="deleteQuestion"><?= ttDelete ?></a>
            <?php }
        }else{ ?>
            <a class="button normal left rSpace tSpace" onclick="cancelNewQuestion(ask = true);"><?= ttCancel ?></a>
            <a class="button blue right lSpace tSpace" onclick="createNewQuestion_<?= $this->get('type') ?>();"><?= ttCreatePL ?></a>
        <?php
        }
    }


    public abstract function printAnswersTable($idQuestion, $idSubject);


//------------  Functions used in preview, test, correction and forms  ------------//
    public abstract function printQuestionPreview();

    public abstract function printQuestionInTest($idSubject, $answered, $extras);

    public abstract function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);

    public abstract function printQuestionInView($idSubject, $answered, $scale, $lastQuestion);

    public abstract function getScoreFromGivenAnswer();

    public abstract function printQuestionEditForm($action, $readonly);


}
