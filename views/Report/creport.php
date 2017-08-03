<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Admin's Homepage
 */

global $config, $user;

?>
<!--THIS SCRIPT IS USED FOR ABILITATE HTML 5 INPUT DATE FOR ALL BROWSER-->
<!-- cdn for modernizr, if you haven't included it already -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
    webshims.setOptions('waitReady', false);
    webshims.setOptions('forms-ext', {types: 'date'});
    webshims.polyfill('forms forms-ext');
</script>
<!-- *********************************************************************-->
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="assesmentHomepage">

        <?php
        openBox(ttReportCoaching, 'normal', 'report');

        ?>

        <div>

            <div id="containertab">

                    <form name="creport" method="post">
                    <h3><?= ttReportCWelcome ?></h3>
                    <p><?=ttReportCDescription?></p>
                    <p><?=ttAOreportSaved?></p>

                    <div class="col-left">
                        <h4><?=ttReportTyping?><br></h4>
                        <input type="text" name="word" class="input-report" placeholder="<?=ttReportSearch?>" oninput="printAssesments(this.value,<?php echo(json_encode($user->id)); ?>)">
                        <br/>
                        <h4><?=ttReportSearched?><br></h4>
                        <select size="5" id="crsearched_ass" onchange="printParticipant()" class="select-creport">

                        </select>
                    </div>
                    <div class="col-center">
                        <!--<h4><?=ttReportSearched?><br></h4>
                        <select size="5" id="crsearched_ass" onchange="printParticipant()" class="select-creport">

                        </select>-->
                        <h3><?=ttReportDateInterval?></h3>
                        <?=ttReportDateFrom?> <input type="date" class="input-report" id="crdateIn" oninput="printParticipant()"/>&nbsp;&nbsp;<?=ttReportDateTo?>
                        <input class="input-report" type="date" id="crdateFn" oninput="printParticipant()"/>
                    </div>
                    <div class="col-center">
                        <h4><?=ttReportAOSelectPartecipant?><br></h4>
                        <select size="5" id="crparticipant" class="select-creport">

                        </select>
                    </div>
                        <br/>
                        <hr class="divider"/>
                        <!--<h3><?=ttReportDateInterval?></h3>
                        <?=ttReportDateFrom?> <input type="date" class="input-report" id="crdateIn" oninput="printParticipant()"/>&nbsp;&nbsp;<?=ttReportDateTo?>
                        <input class="input-report" type="date" id="crdateFn" oninput="printParticipant()"/>
                        <hr/>-->
                        <h3><?=ttReportAssesmentScore?></h3>
                        <?=ttActivate?><input type="checkbox" id="assesmentScore" onclick="unlock(this,assesmentMinScore,assesmentMaxScore)">
                        <br/>
                        <br/>
                        <?=ttReportMinimumScore?>
                        &nbsp;<input class="input-report" type="number" min="0" value="0" oninput="printParticipant()" disabled="disabled" id="assesmentMinScore">
                        <br/>
                        <br/>
                        <?=ttReportMaximumScore?>
                        <input class="input-report" type="number" min="0" value="30" oninput="printParticipant()" disabled="disabled" id="assesmentMaxScore">
                        <hr/>
                    <div id="tabsbutton">
                        <a class="normal button rSpace" id="next" onclick="transferData(assesmentMinScore.value,assesmentMaxScore.value)"><?=ttNext?></a>
                    </div>

                </form>
            </div>

        </div>

        <?php
        closeBox();
        ?>

    </div>
    <div class="clearer"></div>
</div>