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
        openBox(ttAssesmentOverview, 'normal', 'report');

        ?>

        <div>

            <ul id="tabs">
                <li id="tab1"><a id="t1" class="active" href="#maintab"><?= ttReportMainAO ?></a></li>
                <!-- <li id="tab2"><a id="t2" href="#groupstab"><?=ttReportGroup?></a></li> -->
                <li id="tab3"><a id="t3" href="#partecipantstab"><?=ttReportPartecipants?></a></li>
            </ul>
            <div id="containertab">
                <div id="maintab">

                    <p><?= ttReportAOWelcome ?></p>
                    <hr/>
                    <form name="aoreport" method="post">
                    <h3><?= ttReportAOSelectAssesment ?></h3>
                    <p><?=ttReportAODescription?></p>
                    <p><?=ttAOreportSaved?></p>

                    <div class="col-left">
                        <h4><?=ttReportTyping?><br></h4>
                        <input type="text" class="input-report" name="word" placeholder="<?=ttReportSearch?>" oninput="printAssesments(this.value,<?php echo(json_encode($user->id)); ?>)">
                    </div>
                    <div class="col-center">
                        <h4><?=ttReportSearched?><br></h4>
                        <select size="5" id="searched" class="select">

                        </select>
                    </div>
                        <div class="col-left">
                            <a class="normal button right rSpace" id="add" onclick="addAssessment(searched.value)"><?=ttAdd?></a>
                        </div>
                    <div class="col-center">
                        <h4><?=ttReportSelected?><br></h4>
                        <select size="5" id="selected" class="select">

                        </select>
                    </div>
                    <div class="col-center">
                        <a class="normal button rSpace" id="remove" onclick="removeAssessment(selected.value)"><?=ttRemove?></a>
                        <br>

                        <a class="normal button rSpace" id="removeall" onclick="clearAssessments()"><?=ttRemoveAll?></a>
                    </div>
                        <br/>
                        <hr class="divider"/>
                        <h3><?=ttReportDateInterval?></h3>
                        <?=ttReportDateFrom?> <input class="input-report" type="date" id="dateIn"/>&nbsp;&nbsp;<?=ttReportDateTo?> <input class="input-report" type="date" id="dateFn"/>
                        <hr/>
                        <!--<div class="col-left">
                            <h4><?=ttReportSelectFilter?></h4>
                        </div>
                        <div class="col-left">
                            <select id="filter">
                                <option value=""><?=ttReportNotFilter?></option>
                                <option value="<?=ttReportAllFinished?>"><?=ttReportAllFinished?></option>
                                <!--<option value="<?=ttReportScheduled?>"><?=ttReportScheduled?></option>
                                <option value="<?=ttReportTimedOut?>"><?=ttReportTimedOut?></option>
                            </select>
                        </div>
                    <br/>
                    <hr class="divider2"/>-->
                    <div id="tabsbutton">
                        <!--<a class="normal button rSpace" id="back" onclick="prevTab()"><?=ttBack?></a>-->
                        <a class="normal button rSpace" id="next" onclick="nextMainTab()"><?=ttNext?></a>
                    </div>
                </div>

                <div id="groupstab">
                   <h3><?= ttReportSelectGroup ?></h3>

                    <div class="col-left">
                        <h4><?=ttReportTyping?><br></h4>
                        <input class="input-report" type="text" name="wordgroup" placeholder="<?=ttReportSearch?>" oninput="printGroups(this.value)">
                    </div>
                    <div class="col-center">
                        <h4><?=ttReportSearched?><br></h4>
                        <select size="5" id="searchedgroup" class="select">

                        </select>
                    </div>
                    <div class="col-left">
                        <a class="normal button right rSpace" id="addg" onclick="addGroup(searchedgroup.value)"><?=ttAdd?></a>
                    </div>
                    <div class="col-center">
                        <h4><?=ttReportSelectedGroup?><br></h4>
                        <select size="5" id="selectedgroup" class="select">

                        </select>
                    </div>
                    <div class="col-center">
                        <a class="normal button rSpace" id="removeg" onclick="removeGroup(selectedgroup.value)"><?=ttRemove?></a>
                        <br>

                        <a class="normal button rSpace" id="removeallg" onclick="clearGroups()"><?=ttRemoveAll?></a>
                    </div>
                    <br/>
                    <!--<hr class="divider"/>
                    <div class="col-left">
                        <h4><?=ttReportSelectFilter?></h4>
                    </div>
                    <div class="col-left">
                        <select id="filter">
                            <option value=""><?=ttReportNotFilter?></option>
                            <!--<option value="<?=ttReportAllFinished?>"><?=ttReportAllFinished?></option>
                            <option value="<?=ttReportScheduled?>"><?=ttReportScheduled?></option>
                            <option value="<?=ttReportTimedOut?>"><?=ttReportTimedOut?></option>
                        </select>
                    </div>-->
                    <br/>
                    <hr class="divider"/>
                    <div id="tabsbutton">
                        <a class="normal button rSpace" id="backg" onclick="prevGroupTab()"><?=ttBack?></a>
                        <a class="normal button rSpace" id="nextg" onclick="nextGroupTab()"><?=ttNext?></a>
                    </div>
                </div>

                <div id="partecipantstab">
                    <table id="filterpartecipants" class="filter">
                        <tr>
                            <td class="bold"><?=ttReportAssesmentScore?></td>
                            <td><input type="checkbox" id="assesmentScore" onclick="unlock(this,assesmentMinScore,assesmentMaxScore)"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="bold"><?=ttReportMinimumScore?></td>
                            <td><input class="input-report" type="number" min="0" value="0" disabled="disabled" id="assesmentMinScore"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="bold"><?=ttReportMaximumScore?></td>
                            <td><input class="input-report" type="number" min="0" value="30" disabled="disabled" id="assesmentMaxScore"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="bold"><?=ttReportStudent?></td>
                            <td><textarea class="textarea-report" rows="1" id="student" readonly="readonly"></textarea></td>
                            <td>
                                <a class="normal button rSpace" onclick="showPartecipant(),printStudent()"><?=ttAdd?></a>
                                <a class="normal button rSpace" onclick="removePartecipant()"><?=ttRemove?></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold"><?=ttReportStudentDetail?></td>
                            <td><textarea class="textarea-report" rows="1" id="studentDetail" readonly="readonly"></textarea></td>
                            <td>
                                <a class="normal button rSpace" onclick="showParticipantDetails(),printStudentDetail()"><?=ttAdd?></a>
                                <a class="normal button rSpace" onclick="removePartecipantDetail()"><?=ttRemove?></a>
                            </td>
                        </tr>
                        <!--<tr>
                            <td class="bold"><?=ttReportScoreBand?></td>
                            <td><textarea rows="1" id="scoreBand" readonly="readonly"></textarea></td>
                            <td>
                                <a class="normal button rSpace" onclick="showScoreBand()"><?=ttAdd?></a>
                                <a class="normal button rSpace" onclick=""><?=ttRemove?></a>
                            </td>
                        </tr>-->

                    </table>
                    <hr>
                    <div id="tabsbutton">
                        <a class="normal button rSpace" id="backp" onclick="prevPartecipantsTab()"><?=ttBack?></a>
                        <a class="normal button rSpace" id="nextp" onclick="transferData(assesmentMinScore.value,assesmentMaxScore.value)"><?=ttNext?></a>
                    </div>
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
