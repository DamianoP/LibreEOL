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

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <!--<div class="clearer"></div>-->
    <div id="assesmentCustomize">

        <?php
        $db=new sqlDB();
        openBox(ttAssesmentOverview, 'normal', 'report');

        ?>
            <h1><?=ttReportCustomize?></h1>
            <hr>
            <div>
                <?=ttReportTemplateLoad?> <select class="select-template" id="template" size="1"><?if (!($db->qLoadReportTemplate())){echo "errore query";}?></select>
		<a class="normal button" id="load" onclick="LoadCheckbox()"><?=ttLoad?></a>
		<a class="normal button" id="del" onclick="DeleteTemplate()"><?=ttDelete?></a>
                <br><br>
                <?=ttReportTemplateSave?> <input class="input-report" type="text" placeholder="<?=ttReportTemplateName?>" id="template_name"/>
                <a class="normal button" id="save" onclick="saveTemplate()"><?=ttSave?></a>
            </div>
            <h3><?=ttReportAssessmentInformation?></h3>
            <hr>
            <form name="template" action="index.php?page=report/aoreportresult" target="_blank" method="post">
            <div class="templatecontent">
            <table id="assesment_info "class="customize">
                <thead>
                    <tr>
                        <th class="bold title"><?=ttReportField?></th>
                        <th class="bold title"><?=ttReportChecked?></th>
                        <!--<th class="bold title"><?=ttReportOrder?></th>-->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=ttReportAssesmentName?></td>
                        <td><input type="checkbox" name="assesmentName" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentID?></td>
                        <td><input type="checkbox" name="assesmentID" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentAuthor?></td>
                        <td><input type="checkbox" name="assesmentAuthor" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentDateTimeFirst?></td>
                        <td><input type="checkbox" name="assesmentDateTimeFirst" class="checkass" value="si"></td>
                       <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentDateTimeLast?></td>
                        <td><input type="checkbox" name="assesmentDateTimeLast" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentNumberStarted?></td>
                        <td><input type="checkbox" name="assesmentNumberStarted" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentNumberNotFinished?></td>
                        <td><input type="checkbox" name="assesmentNumberNotFinished" class="checkass" value="si"></td>
                       <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentNumberFinished?></td>
                        <td><input type="checkbox" name="assesmentNumberFinished" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentMinscoreFinished?></td>
                        <td><input type="checkbox" name="assesmentMinscoreFinished" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentMaxcoreFinished?></td>
                        <td><input type="checkbox" name="assesmentMaxscoreFinished" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentMediumFinished?></td>
                        <td><input type="checkbox" name="assesmentMediumFinished" class="checkass" value="si"></td>
                       <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentLeastTimeFinished?></td>
                        <td><input type="checkbox" name="assesmentLeastTimeFinished" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentMostTimeFinished?></td>
                        <td><input type="checkbox" name="assesmentMostTimeFinished" class="checkass" value="si"></td>
                       <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentMediumTimeFinished?></td>
                        <td><input type="checkbox" name="assesmentMediumTimeFinished" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>
                    <tr>
                        <td><?=ttReportAssesmentStdDeviation?></td>
                        <td><input type="checkbox" name="assesmentStdDeviation" class="checkass" value="si"></td>
                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td><a class="normal button" id="selectall" onclick="selectAllAssesment()"><?=ttSelectAll?></a></td>
                        <!--<td><a class="normal button" id="resetorder" onclick=""><?=ttResetOrder?></a></td>-->
                    </tr>
                </tfoot>
            </table>

            </div>

                <h3><?=ttReportTopicInformation?></h3>
                <hr>
                <form name="template" action="" method="post">
                    <div class="templatecontent">
                        <table class="customize">
                            <thead>
                                <tr>
                                    <th class="bold title"><?=ttReportField?></th>
                                    <th class="bold title"><?=ttReportChecked?></th>
                                    <!--<th class="bold title"><?=ttReportOrder?></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?=ttReportTopicAverageScore?></td>
                                    <td><input type="checkbox" name="topicAverageScore" class="checktopic" value="si"></td>
                                    <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                </tr>
                                <tr>
                                    <td><?=ttReportTopicMinimumScore?></td>
                                    <td><input type="checkbox" name="topicMinimumScore" class="checktopic" value="si"></td>
                                    <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                </tr>
                                <tr>
                                    <td><?=ttReportTopicMaximumScore?></td>
                                    <td><input type="checkbox" name="topicMaximumScore" class="checktopic" value="si"></td>
                                   <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                </tr>
                                <tr>
                                    <td><?=ttReportTopicStandardDeviation?></td>
                                    <td><input type="checkbox" name="topicStdDeviation" class="checktopic" value="si"></td>
                                    <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                </tr>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td><a class="normal button" id="selectallt" onclick="selectAllTopic()"><?=ttSelectAll?></a></td>
                                    <!--<td><a class="normal button" id="resetordert" onclick=""><?=ttResetOrder?></a></td>-->
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <h3><?=ttReportGraphicalDsiplays?></h3>
                    <hr>
                    <form name="template" action="" method="post">
                        <div class="templatecontent">
                            <table class="customize">
                                <thead>
                                    <tr>
                                        <td class="bold title"><?=ttReportField?></td>
                                        <td class="bold title"><?=ttReportChecked?></td>
                                        <!--<td class="bold title"><?=ttReportOrder?></td>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?=ttReportGraphicHistogram?></td>
                                        <td><input type="checkbox" name="graphicHistogram" class="checkgraphic" value="si"></td>
                                       <!-- <td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                    </tr>
                                    <tr>
                                        <td><?=ttReportGraphicTopicScore?></td>
                                        <td><input type="checkbox" name="graphicTopicScore" class="checkgraphic" value="si"></td>
                                        <!--<td><input type="number" min="1" max="23" id="" class="order"></td>-->
                                    </tr>
                                    <!-- <tr>
                                        <td><?=ttReportGraphicScoreBands?></td>
                                        <td><input type="checkbox" class="checkgraphic"></td>
                                        <td><input type="number" min="1" max="23" id="" class="order"></td>
                                    </tr>-->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td><a class="normal button" id="selectallg" onclick="selectAllGraphic()"><?=ttSelectAll?></a></td>
                                        <!--<td><a class="normal button" id="resetorder" onclick=""><?=ttResetOrder?></a></td>-->
                                    </tr>
                                </tfoot>
                            </table>
                            <br>
                            <a class="normal button done" onclick="template.submit()"><?=ttSend?></a>
                        </div>
                        <br/>

            </form>
        <?php
        closeBox();
        ?>

    </div>
    <div class="clearer"></div>
</div>
