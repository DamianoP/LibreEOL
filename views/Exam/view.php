<?php
/**
 * File: view.php
 * User: Masterplan
 * Date: 5/6/13
 * Time: 3:42 PM
 * Desc: View archived test
 */

global $config;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">

    <?php
    $db = new sqlDB();
    if(($db->qTestDetails(null, $_POST['idTest'])) && ($testInfo = $db->nextRowAssoc())){
        $studentName = $testInfo['name'].' '.$testInfo['surname'];
        $idSubject = $testInfo['fkSubject'];
        $numQuestions = $testInfo['questions'];
        $scoreTest = $testInfo['scoreTest'];
        $scoreType = $testInfo['scoreType'];
        $bonus = $testInfo['testBonus'];
        $scoreFinal = ($testInfo['scoreFinal'] > $testInfo['scoreType'])? $testInfo['scoreType'].' '.ttCumLaudae : $testInfo['scoreFinal'];
        $scale = $testInfo['scale'];
        $isEditable = ($testInfo['editable'] == 0)? false : true;
	
        if(($db->qViewArchivedTest($_POST['idTest'], null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))){
            if(count($questions) != $numQuestions){
                die(ttEQuestionNotFound);
            }
            openBox(ttTest.': '.$studentName.' ('.$scoreFinal.')', 'normal', 'correct', array('showHide'));
            $lastQuestion = '';
            foreach($questions as $idQuestion => $questionInfo){
                $questionAnswers = '';
                $questionScore = 0;

                $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                $answered = json_decode(stripslashes($questionInfo['answer']), true);
                if($answered == '')
                    $answered = array('');

                $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                $question->printQuestionInView($idSubject, $answered, $scale, $lastQuestion);

            }
            ?>

            <div id="lastLine">
                <div id="finalScorePanel">
                    <table id="finalScore">
                        <tr>
                            <td class="sLabel"><?= ttScoreTest ?></td>
                            <td class="sScore"><label id="scorePre"><?= $scoreTest ?></label></td>
                            <td>+</td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttBonus ?></td>
                            <td class="sScore"><label id="scoreBonus"><?= $bonus ?></label></td>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttFinalScore ?></td>
			    <td class="sScore"><label id="scorePost"><?= $scoreFinal ?></label></td>
                            <td><span id="areaModificaVoto" style="display:none">
                                <?php if($isEditable){?>
                                <input id="punteggioFinale" type="number" min="0" max=<?php echo "'".$scoreType."'";?> value=<?php echo "'".$scoreFinal."'";?>>
                                
                                <?php }?>

                                </span>
                            </td>                        </tr>
                    </table>
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

                </div>
                <a class="button ok" onclick="checkStatus()" style="width:70px" id="bottoneChiusuraPagina"><?= ttClose ?></a>
                <?php if($isEditable){?>
                <a class="button delete" style="width:70px" onclick="editVoteStudent()">
                                    <?= ttEdit ?>
                </a>
                <?php } ?>
            </div>

            <div class="clearer"></div>

            <?php closeBox(); ?>
            <div class="clearer"></div>

        <?php
        }else{
            die(ttEDatabase);
        }
    }else{
        die(ttEDatabase.' / '.ttETestNotFound);
    }

    ?>

</div>
<script>
var salvataggio=0;
function editVoteStudent(){
    if(document.getElementById('areaModificaVoto').style.display=="none"){
        document.getElementById('areaModificaVoto').style.display = '';        
        document.getElementById('bottoneChiusuraPagina').innerHTML = "<?= ttSave ?>";
        salvataggio=1;
    }
    else {
        document.getElementById('areaModificaVoto').style.display = 'none';        
        document.getElementById('bottoneChiusuraPagina').innerHTML = "<?= ttClose ?>";
        salvataggio=0;
    }
}

function checkStatus(){
    if(salvataggio==1){
        if(confirm("<?= ttConfirmProcedure ?>") == true){
            risalvaEsame()  
        }        
    }else
        window.close();
    
}

function risalvaEsame(){
    var valore=document.getElementById('punteggioFinale').value;
    if(valore><?php echo $scoreType;?>){
        alert("<?php echo ttError; ?>");
    }else{
        $.ajax({
            url     : "index.php?page=exam/Updatearchivedtest",
            type    : "post",
            data    : {
                idTest        :   <?php echo $_POST['idTest'];?>,
                scoreFinal    :   valore
            },
            success : function (data) {
                if(data == "ACK"){
                    showSuccessMessage(ttMConfirm);
                    setTimeout(function(){ window.close() }, 1500);
                }else{
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}
</script>
