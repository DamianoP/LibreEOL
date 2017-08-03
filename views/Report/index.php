<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Report Homepage
 */

global $config, $user;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="reportHomepage">
        <div class="clearer"></div>
        <?php
        openBox(ttReport, 'center', 'report');

        ?>

        <table id="reportTable">
            <tr><td><img src="<?=$config['themeImagesDir']?>report.png" class="img-report"/></td><td><a class="replink" href="index.php?page=report/aoreport"><?= ttAssesmentOverview ?></a></td></tr>
            <tr><td></td><td>See an overwiev of result for one or more assessments</td></tr>
            <tr><td><img src="<?=$config['themeImagesDir']?>report.png" class="img-report"/></td><td><a class="replink" href="index.php?page=report/creport"><?= ttReportCoaching ?></a></td></tr>
            <tr><td></td><td><?=ttReportCoachingDescription?></td></tr>
            <tr><td><img src="<?=$config['themeImagesDir']?>report.png" class="img-report"/></td><td><a class="replink" target="_blank" href="<?=$config['systemFileManagerDir']?>filemanager.php?type=report"><?= ttReportViewer ?></a></td></tr>
            <tr><td></td><td><?=ttReportViewerDescription?></td></tr>
        </table>

        <?php
        closeBox();
        ?>

    </div>
</div>