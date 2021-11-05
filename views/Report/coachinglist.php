<?php
global $config, $user, $log;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <?php openBox(ttCoachingReport, 'normal', 'report'); ?>
    <h2><?= ttReportCoachingInformation ?></h2>
    <div class="report-title"><?= ttSubject ?>:</div>
    <div class="report-text"><?php echo $_SESSION['subject']; ?></div>
    <div class="report-title"><?= ttDate ?>:</div>
    <div class="report-text" id="date"><?php echo $_SESSION['year'] . ", " . $_SESSION['examdate'] ?></div>
    <?php if ($_SESSION['minscoreparam'] != -1 && $_SESSION['maxscoreparam'] != -1) {
        echo '<div class="report-title">' . ttReportScoreRange . ':</div>';
        echo '<div class="report-text">' . $_SESSION['minscoreparam'] . '-' . $_SESSION['maxscoreparam'] . '</div>';
    }
    ?>
    <div class="report-tablecontainer">
        <table class="report-table" id="crtests">
            <thead>
            <tr>
                <th class="report-table-title">#</th>
                <th class="report-table-title"><?= ttParticipant ?></th>
                <th class="report-table-title"><?= ttScore ?></th>
            </tr>
            </thead>
            <?php
            $i = 1;
            $db = new sqlDB();
            if (!($db->qGetParticipants($_SESSION['exam'], $_SESSION['minscoreparam'], $_SESSION['maxscoreparam']))) {
                echo "NACK";
            } else {
                while ($row = $db->nextRowAssoc()) {
                    ?>
                    <tr>
                        <th class="report-table-content" id="num"
                            onclick="sendCoachingReport(<?= $row['idTest']; ?>)"><?= $i ?></th>
                        <th class="report-table-content" id="participant"
                            onclick="sendCoachingReport(<?= $row['idTest']; ?>)"><?= $row['name'] . " " . $row['surname'] ?></th>
                        <th class="report-table-content" id="score"
                            onclick="sendCoachingReport(<?= $row['idTest']; ?>)"><?= $row['scoreFinal']; ?></th>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
        </table>
    </div>
    <div class="report-coaching-button">
        <a class="report-button" id="back" href="index.php?page=report/creport"><?= ttBack ?></a>
    </div>
    <?php closeBox(); ?>
    <div class="clearer"></div>
</div>
