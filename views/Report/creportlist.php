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

        <?php
        $db=new sqlDB();
        openBox(ttReportCoaching, 'normal', 'report');

        ?>
    <h2><?=ttReportCoachingInformation?></h2>
    <div class="small-title"><?=ttReportPartecipant?>:</div> <div class="small-text"><?echo $db->qLoadStudent($_SESSION['CRuser']);?></div>
    <div class="small-title"><?=ttReportAssesmentName?>:</div><div class="small-text"><?echo $_SESSION['CRexam'];?></div>
    <div class="small-title"><?=ttGroup?>:</div><div class="small-text"><?echo $db->qLoadGroup($_SESSION['CRuser']);?></div>
    <div class="templatecontent">
        <table class="crlist" id="crtests">
            <thead>
            <tr>
                <th class="bold title">#</th>
                <th class="bold title"><?=ttScore?></th>
                <th class="bold title"><?=ttReportDateTaken?></th>
                <th class="bold title"><?=ttStatus?></th>
            </tr>
            </thead>
        <!-- All tests will be print here-->
        </table>
    </div>
        <?php
        closeBox();
        ?>


    <div class="clearer"></div>
</div>
