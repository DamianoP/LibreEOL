    <!DOCTYPE html>

<html>

<head>

<script type="text/javascript" src="libs/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="libs/jquery-ui.min.js"></script>
<script src="libs/package/dist/chart.umd.js"></script>
<link rel="stylesheet" href="themes/stylePrint.css" type="text/css">
<title>Embed PHP in a .html File</title>

</head>
    <div id="navbar">
        <?php printMenu(); ?>
    </div>
<body>

<h1><?php 
    $db = new sqlDB();
    $db->qCountStudentForExam($_POST['idExamPost']);
    $row = $db->nextRowAssoc();
    $db->qExamRegistrationsList($_POST['idExamPost']);
    $row = $db->nextRowAssoc();
    echo '<p style="display:none;" id="idExam">'.$_POST['idExamPost'].'</p>';
?></h1>

<div>
    <?php
        $db = new sqlDB();
        $db->gExamInfo($_POST['idExamPost']);
        $row = $db->nextRowAssoc();

        echo '<h1>Coaching Report per '.$row['name'].'</h1>';
        echo '<b>'.ttDateExam.': </b> '.$row['datetime'].
        '<label for="students" style="margin-left:15px"><b>'.ttStudent.':</b></label>
        <p id="studentNamePrint" style="display:none"> name </p>
        <select name="students" id="studentsDropDown">';
        
        $db->qExamRegistrationsList($_POST['idExamPost']);

        echo '<option value="all" id="dropDownAll" selected>'.ttAll.'</option>';

        $row = $db->nextRowAssoc();

        while($row != NULL){
            echo '<option value="'.$row['name']." ".$row['surname']." ".$row['fkUser'].'">'.$row['name']." ".$row['surname'].'</option>';
            $row = $db->nextRowAssoc();
        }
    ?>

</div>    
    </select>
        <select name="mode" id="mode" style="margin-left: 15px">
            <option value="charts"><?=ttCharts?></option>
            <option value="answers"><?=ttAnswers?></option>
        </select><button class="normal button lSpace tSpace" style="margin-left: 15px;" id = "submitBtn" onclick = "submitAction()"><?=ttSelectButton?></button>
<div class="datawrapper">

<!-- Francesco Report -->

    <table style='margin : auto;'id='coachingTab'  width='80%'>
        <thead>
            <tr>
                <th colspan='2'>Coaching Report</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id='idScore'>Punteggio conseguito</td>
                <td id='idTotal'>Totale</td>
            </tr>
            <tr>
                <td id='idAnswered'>Domande corrette</td>
                <td id='idPresented'>Domande totali</td>
            </tr>
            <tr>
                <td id='idStart'>Inizio esame</td>
                <td id='idEnd'>Fine esame</td>
            </tr>
        </tbody>
    </table> 

    <div style = 'margin-top : 5vh'>
        <h2 style ='display : none ' id ="questionsCoaching"> <?=ttAnswersTableTitle?></h2>
    <table style='margin : auto ; display : none' id='questionTab'  width='80%'>
        <thead>
            <tr>
                <th id = 'questionSecondTab'><?=ttQuestion?></th>
                <th id = 'typeSecondTab'><?=ttTipology?></th>
                <th id = 'scoreSecondTab'><?=ttScore?></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table> 
    
    </div>

    <!-- Fine modifiche -->
    <div class="answers" style="margin-top:15px; margin-bottom:15px;">
    </div>
    <div id="legend" style="display:none;">
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#96CCF1;" />
        </svg>
        <b>ES</b>: <?= ttOpenAnswer ?> </p>
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#FB9BB0;" />
        </svg>
        <b>MC</b>: <?= ttMc ?></p>
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#FBCB9B;" />
        </svg>
        <b>MR</b>: <?= ttSc ?></p>
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#FBE2A6;" />
        </svg>
        <b>NM</b>: <?= ttOpenNum ?></p>
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#A1DBDB;" />
        </svg>
        <b>TF</b>: <?= ttTf ?></p>
        <p style="display:inline-block">
        <svg width="40" height="12">
        <rect width="300" height="100" style="fill:#C8AEFB;" />
        </svg>
        <b>YN</b>: <?= ttYn ?></p>
    </div>


    <div class="global-charts">
        <div style="margin:auto; width:80vw; height:40vh;">
            <canvas id = "chart-global-1" ></canvas>
        </div>
        <div class="global-wrapper">
            <canvas class = "chart-global" id = "chart-global-4" width="700px" style="display: inline-block"></canvas>
            <canvas class = "chart-global" id = "chart-global-3" width="700px" style="display: inline-block"></canvas>
        </div>
        <div class="global-wrapper">
            <canvas class = "chart-global" id = "chart-global-5" width="700px" style="display: inline-block"></canvas>
            <canvas class = "chart-global" id = "chart-global-2" width="400px" style="display: inline-block"></canvas>
        </div>
    </div>


    <div class="charts">
        <div class="charts-line" id="first-group-charts">
            <canvas class="chart-left" id="chart1" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-right" id="chart2" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-left" id="chart3" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-right" id="chart4" width="300px" height="300px" style="display: inline-block"></canvas>
        </div>
        <div class="charts-line" id="second-group-charts" style="margin-top: 60px">
            <canvas class="chart-left" id="chart5" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-right" id="chart6" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-left" id="chart7" width="300px" height="300px" style="display: inline-block"></canvas>
            <canvas class="chart-right" id="chart8" width="300px" height="300px" style="display: inline-block"></canvas>
        </div>
    </div>
    <button class="normal button lSpace tSpace" style="margin-bottom: 10px; display:none;" onclick = printReport() id="printBtn"><?= ttPrintButton ?></button>
</div>


<script type="text/javascript" src="libs/Exam/CoachingReports.js"></script>
</body>

</html>
