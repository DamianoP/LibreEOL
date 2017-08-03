
<?php
/**
 * File: QT_FB.php
 * User: Alberto
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Fill In Blanks Question
 */

class QT_FB extends Question {

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

            <!-- Print hidden field for question's type (FB) -->
            <input type="hidden" id="questionType" value="FB">

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
                        <div id="newAnswer_FB">
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
        echo '<script> initialize_FB(); </script>';
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
            $score = array("0" => ttFalse, "0.1" => ttTrue, "0.2" => ttTrue, "0.3" => ttTrue, "0.4" => ttTrue, "0.5" => ttTrue, "0.6" => ttTrue, "0.7" => ttTrue, "0.8" => ttTrue, "0.9" => ttTrue, "1.0" => ttTrue);

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

    public function printQuestionPreview()
    {
        global $config;


        $db = new sqlDB();
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())) {


            // -------  Add extra buttons  ------- //
            $extra = '';
            if (strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
            if (strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';

            $domanda = $this->get('translation') . $extra;


            ?>



            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="FB">
            <div class="questionText"><?= $null = "";

                //calcolo la lunghezza dell'array "domanda"
                $lunghezza = strlen($domanda);
                $conta = 1;
                for ($i = 0; $i < $lunghezza; $i++) {
                    if ($domanda[$i] != "_") {
                        //stampo la lettera nella posizione $i dell'array "domanda"
                        echo "$domanda[$i]";
                    } else {
                        //verifico se ci sono 3 underscore di fila
                        $j = 1;
                        for ($k = 0; $k < 2; $k++) {
                            if ($i < $lunghezza - 1) {
                                if ($domanda[$i + 1] == "_") {
                                    $j++;
                                    $i++;
                                }
                            }
                        }
                        //se j==3 significa che ci sono 3 underscore di fila
                        if ($j == 3) {
                            //inserisco la textbox
                            ?>
                            <input name="risposta" type="text" value="" size="13" maxlength="25"/>
                            <?php
                            $conta++;
                        } else if ($j == 1) {
                            echo "_";
                        } elseif ($j == 2) {
                            echo "__";
                        }
                    }
                }
                ?></div>

        </div>

    <?php
    }
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;


            $domanda=$this->get('translation');
            $idQuestionTest=$this->get('idQuestion');
            ?>
            <div class="questionTest" value=<?php echo "'".$idQuestionTest."'" ?> type="FB">
                <div class="questionText">Completa il testo seguente con le opportune parole
                    <br>
                </div>
                <div class="questionAnswers"><?php 
                    $null="";
                    //calcolo la lunghezza dell'array "domanda"
                    $lunghezza=strlen($domanda);


                    $conta=1;
                    for($i=0; $i<$lunghezza; $i++){
                        if($domanda[$i]!="_"){
                            //stampo la lettera nella posizione $i dell'array "domanda"
                            echo $domanda[$i];
                        }else{
                            //verifico se ci sono 3 underscore di fila
                            $j=1;

                            for ($k = 0; $k < 2; $k++) {
                                if ($i < $lunghezza - 1) {
                                    if ($domanda[$i + 1] == "_") {
                                        $j++;
                                        $i++;
                                    }
                                }
                            }
                            //se j==6 significa che ci sono 3 underscore di fila
                            if($j==3){
                                //inserisco la textbox

                                $textbox="<input type='text' id='rispostas'>";
                                //$questionAnswers = '<textarea class="textareaTest" rows="1">'.$answered[0].'</textarea>';
                                echo $textbox;
                                //incremento contatore
                                $conta++;
                            } else if ($j == 1) {
                                echo "_";
                            } elseif ($j == 2) {
                                echo "__";
                            }
                        }
                    }
                    ?></div>
            </div>
            <br>
            <?php
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
            return $extras;
    }









    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){

        global $config;?>
        <?php
        $questionScore=0;
        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))){
            $contatore=1;
            $contagrafica=1;
            $punteggiografica=0;


            $inc=0;
            foreach($answerSet as $idAnswer => $answer) {
                $answerdClass = "";
                $giusta[$contagrafica]=$answer['translation'];
                $rispostaesatta=strtolower(trim($giusta[$contagrafica]));
                $rispostastudente=strtolower(trim($answered[$inc]));
                if($rispostaesatta==$rispostastudente){
                    $punteggiografica += round(($answer['score'] * $scale), 1);
                    $vettore[$inc]=1;
                }else{$vettore[$inc]=0;}
                $inc++;
                $contagrafica++;

            }

            if(count($answered) != 0)
                $questionClass = ($punteggiografica > 0) ? 'rightQuestion' : 'wrongQuestion';



        ?>
        <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="FB">
            <div class="questionText" onclick="showHide(this);">
                <span class="responseQuestion"></span>
                <?= $null="";
                $domanda=$this->get('translation');
                //calcolo la lunghezza dell'array "domanda"
                $lunghezza=strlen($domanda);
                //indice per il numero di textbox
                $conteggiotextbox=0;
                $conta=1;
                for($i=0; $i<$lunghezza; $i++){
                    if($domanda[$i]!="_"){
                        //stampo la lettera nella posizione $i dell'array "domanda"
                        echo"$domanda[$i]";
                    }else{
                        //verifico se ci sono 3 underscore di fila
                        $j=1;

                        for ($k = 0; $k < 2; $k++) {
                            if ($i < $lunghezza - 1) {
                                if ($domanda[$i + 1] == "_") {
                                    $j++;
                                    $i++;
                                }
                            }
                        }
                        //se j==3 significa che ci sono 3 underscore di fila
                        if($j==3){
                            //inserisco la textbox
                            if($vettore[$conteggiotextbox]==1){
                                $colore="#AFFAB4";

                            }else{$colore="#F4A8A8";}

                            $textbox='<input type="text" id="rispostas" style="background-color:'.$colore.';" readonly value="'.$answered[$conteggiotextbox].'">';
                            echo $textbox;
                            $conteggiotextbox++;
                            //incremento contatore
                            $conta++;
                        } else if ($j == 1) {
                            echo "_";
                        } elseif ($j == 2) {
                            echo "__";
                        }
                    }
                }

                ?>
                <span class="responseScore"><?= number_format($punteggiografica, 1); ?></span>
            </div>

            <div class="questionAnswers hidden"><?php
                $inc=0;
                foreach($answerSet as $idAnswer => $answer) {
                    $answerdClass = "";
                    $giusta[$contatore]=$answer['translation'];

                    $rispostaesatta=strtolower(trim($giusta[$contatore]));
                    $rispostastudente=strtolower(trim($answered[$inc]));
                    if($rispostaesatta==$rispostastudente){
                        $right_wrongClass ='rightAnswer';
                        ?>

                        <div STYLE="background:#90ee90;" class="<?php echo $answerdClass ?>">
                        <?php
                        echo "Risposta Corretta $contatore: ";
                        echo $giusta[$contatore];?>
                        <?php
                        $answerdClass = 'answered';
                        $questionScore += round(($answer['score'] * $scale), 1);
                        ?>
                        <label class="score"><?php echo round($answer['score'] * $scale, 1)?></label>
                        </div><?php
                    }elseif (strcmp($rispostastudente,"")==0) {
                         ?>

                        <div STYLE="background:lightcoral;" class="<?php echo $answerdClass ?>">
                        <?php
                        echo "Risposta Corretta $contatore: ";
                        echo $giusta[$contatore];
                        ?>
                        <label class="score"><?php echo "0" ?></label>
                            </div>
                        <?php
                    }

                    else
                    {
                        ?>

                        <div STYLE="background:lightcoral;" class="<?php echo $answerdClass ?>">
                        <?php
                        echo "Risposta Corretta $contatore: ";
                        echo $giusta[$contatore];
                        ?>
                        <label class="score"><?php echo "0" ?></label>
                            </div>
                        <?php
                    }
                    $inc++;
                    $contatore++;
                    ?><br><?php
                }



                ?><label class="questionScore"><?php echo number_format($questionScore, 1); ?></label>
                <div class="clearer"></div>

  </div>
        </div>

    <?php
        }else{
            die(ttEAnswers);
        }
    }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion){
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }

    public function getScoreFromGivenAnswer()
    {
        global $log;
        $log->append("FB_getScoreFromGivenAnswer()");

        $score=0;
        $answerr = json_decode(stripslashes($this->get('answer')), true);
        //numero di riposte
        $contarisposte=count($answerr);
        $log->append("Il numero di risposte contate viene ora riportato:".$contarisposte);
        for ($m=0;$m<$contarisposte;$m++){
            $rispostautente[$m]=strtolower(trim($answerr[$m]));
            $log->append("Risposta letta:".$rispostautente[$m]);
        }
        //ID della domanda
        $idAns=$this->get('idQuestion');

        $log->append("idAns:".$idAns);
        $db = new sqlDB();
        $db2 = new sqlDB();
        //controllo se esiste
        if($pippo=$db->qSelect('Answers', 'fkQuestion', $idAns)) {

            $log->append("Sono dentro il primo if");
            $giro=0;
            //scorro le risposte
            while ($result = $db->nextRowAssoc()) {

                $log->append("Sto ciclando nel while, giro:".$giro);
                //salvo dentro $id l'id della risposta corrente
                $id = $result['idAnswer'];
                $log->append("id letto:".$id);
                if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $id)) {
                    $log->append("Sono dentro il secondo if");
                    while ($traslation = $db2->nextRowAssoc()) {
                        $log->append("ciclo nel secondo while");
                        $trasl = strtolower(trim($traslation['translation']));
                        $log->append("trasl:".$trasl);
                        if ($rispostautente[$giro] == $trasl) {
                            $score += $result['score'];
                            $log->append("score:".$score);
                        }
                        $log->append("score:".$score);
                    }
                    $giro++;
                }else{
                    $error=$db->getError();
                    $log->append("Debug(crash):".$error);
                    die($error);
                } 
            }
        }else{
                $error=$db->getError();
                $log->append("Debug(crash):".$error);
                die($error);
                } 
        $log->append("The score is:".$score);
        return $score;
    }
}