let arrayOfSingleStudentCharts = [];

let globalExamInfo = {
  avg: 0,
  avgDuration: 0,
  totalEasy: 0,
  totalMedium: 0,
  totalHard: 0,
  users: [],
  arrayDuration: [],
  testSettings: [],
  minScore: 0,
  maxScore: 0,
  globalCatCorrect: [],
  globalCatCount: [],
  idExam: 0,
};

let languagesCodes = {
  en: 1,
  it: 2,
};
let categories = ["ES", "MC", "MR", "NM", "TF", "YN"];

const SOFT_RED = "rgba(255, 99, 132, 0.2)";
const SOFT_GREEN = "rgba(75, 192, 192, 0.2)";
const SOFT_YELLOW = "rgba(255, 159, 64, 0.2)";
const SOFT_PURPLE = "rgba(180, 148, 243,0.2)";
const SOFT_PINK = "rgba(255, 116, 146, 0.2)";
const SOFT_BLUE = "rgba(0, 140, 186, 0.2)";
const SOFT_GRAPE = "rgba(192, 24, 204, 0.2)";
const SOFT_RED_ROME = "#FFB1C1";
const SOFT_YELLOW_ROME = "#FFCF9F";
const HARD_RED = "rgb(255, 99, 132)";
const HARD_GREEN = "rgb(75, 192, 192)";
const HARD_YELLOW = "rgb(255, 159, 64)";
const HARD_PURPLE = "rgba(180, 148, 243,1)";
const HARD_PINK = "rgb(255, 116, 146)";
const HARD_BLUE = "rgb(0, 140, 186)";
const HARD_GRAPE = "rgb(192, 24, 204)";
const HARD_RED_ROME = "#FF7B97";
const HARD_YELLOW_ROME = "#FFA74F";

let graphsAlignedForMobile = false;

//Francesco json
const translateCode = {
  MC: ttMc,
  MR: ttSc,
  NM: ttOpenNum,
  YN: ttYn,
  TF: ttTf,
  ES: ttOpenAnswer,
};

$("#studentsDropDown").change(disableSubmitButton);
$("#mode").change(disableSubmitButton);

function disableSubmitButton() {
  if ($("#mode").val() == "answers" && $("#studentsDropDown").val() == "all") {
    $("#submitBtn").attr("disabled", true);
  } else {
    $("#submitBtn").attr("disabled", false);
  }
}

$(document).ready(function () {
  initExamObj();
  console.log(globalExamInfo);
});

function submitAction() {
  destroyCharts(arrayOfSingleStudentCharts);
  appendTotalScore();

  //Switch to answers view
  if ($("#mode").val() == "answers") {
    $("#legend")[0].style.display = "none";
    hideCharts();
    setAnswersTableVisible();
    appendTable();
  }

  //Hide Answers
  if ($("#mode").val() == "charts" && $("#studentsDropDown").val() != "all") {
    $("#legend")[0].style.display = "inline";
    hideAnswersTable();
    hideCharts();

    createStudentCharts();
    setStudentChartsVisible();
  }

  if ($("#mode").val() == "charts" && $("#studentsDropDown").val() == "all") {
    $("#legend")[0].style.display = "inline";
    hideAnswersTable();
    createAllStudentsCharts();
    hideCharts();
    setAllStudentsChartsVisible();
  }
}

function destroyCharts(listOfCharts) {
  for (chart of listOfCharts) {
    if (chart) {
      chart.destroy();
    }
  }
}

function printReport() {
  let nameSurnameId = getNameSurnameId();
  $("#studentNamePrint")[0].innerText = nameSurnameId[0] + " " + nameSurnameId[1];
  print();
}

function initExamObj() {
  globalExamInfo["idExam"] = $("#idExam").text();
  setGlobalExamInfoUsers();
  setGlobalExamInfoEMHTotalCount();
  setGlobalExamInfoArrayDuration();
  setGlobalExamInfoAvg();
  setGlobalExamInfoAvgTime();
  setGlobalExamInfoMaxMin();
  setGlobalExamInfoTestSettings();
  setGlobalExamInfoCategoriesNumbers();
}

//-------------------Set Functions-------------------//
//----------------for globalexam info---------------//
function setGlobalExamInfoArrayDuration() {

  let ids = getAjaxCall("index.php?page=exam/getallstudentsids", "post", false, {
    idExam: globalExamInfo["idExam"],
  });

  globalExamInfo["arrayDuration"] = getAjaxCall(
    "index.php?page=exam/getdurationtests",
    "post",
    false,
    {
      studentsIds: ids,
      idExam: globalExamInfo["idExam"],
    }
  );
}

function setGlobalExamInfoEMHTotalCount() {
  let nameSurnameId = $("#studentsDropDown")[0][1].text.split(" ");

  let answers = getAjaxCall(
    "index.php?page=exam/studentanswers",
    "post",
    false,
    {
      idStudent: $("#studentsDropDown")[0][1].value.split(" ")[2],
      idExam: globalExamInfo["idExam"],
    }
  );

  //Contains no correct answer per category hard/medium/easy
  let difficultyScores = getDifficultyScores(
    getQuestionsDetails(answers),
    answers
  );

  globalExamInfo["totalEasy"] = difficultyScores["easy"]["total"];
  globalExamInfo["totalMedium"] = difficultyScores["medium"]["total"];
  globalExamInfo["totalHard"] = difficultyScores["hard"]["total"];
}

function setGlobalExamInfoAvgTime() {
  let sumTime = 0;
  globalExamInfo["arrayDuration"].forEach((element) => {
    sumTime += Number(element["duration"]);
  });
  globalExamInfo.avgDuration = sumTime / globalExamInfo["arrayDuration"].length;
}

function setGlobalExamInfoUsers() {
  let names = [];
  let dropDown = $("#studentsDropDown")[0];

  for (let i = 1; i < dropDown.length; i++) {
    names.push(dropDown[i].text.split(" "));
  }
  globalExamInfo["users"] = names;
}

function setGlobalExamInfoAvg() {
  let ids = getAjaxCall("index.php?page=exam/getallstudentsids", "post", false, {
    idExam: globalExamInfo["idExam"],
  });

  globalExamInfo["avg"] = Number(
    getAjaxCall("index.php?page=exam/averagefromset", "post", false, {
      studentsIds: ids,
      idExam: globalExamInfo["idExam"],
    })
  );
}

function setGlobalExamInfoMaxMin() {
  let ids = getAjaxCall("index.php?page=exam/getallstudentsids", "post", false, {
    idExam: globalExamInfo["idExam"],
  });
  result = getAjaxCall("index.php?page=exam/getmaxmin", "post", false, {
    studentsIds: ids,
    idExam: globalExamInfo["idExam"],
  });
  globalExamInfo.minScore = result["min"];
  globalExamInfo.maxScore = result["max"];
}

function setGlobalExamInfoTestSettings() {
  globalExamInfo.testSettings = getTestSettings();
}

function setGlobalExamInfoCategoriesNumbers() {

  let ids = getAjaxCall("index.php?page=exam/getallstudentsids", "post", false, {
    idExam: globalExamInfo["idExam"],
  });

  result = getAjaxCall("index.php?page=exam/globalcatcount", "post", false, {
    studentsIds: ids,
    idExam: globalExamInfo["idExam"],
  });

  globalExamInfo.globalCatCorrect = result["correct"];
  globalExamInfo.globalCatCount = result["total"];
}

function setClickListenersCollaps() {
  let collapsibles = document.getElementsByClassName("collapsible");

  for (i = 0; i < collapsibles.length; i++) {
    collapsibles[i].addEventListener("click", function () {
      this.classList.toggle("active");
      let content = this.nextElementSibling;
      if (content.style.display === "block") {
        content.style.display = "none";
      } else {
        content.style.display = "block";
      }
    });
  }
}

//-------------------Get Functions-------------------//
function getQuestionsDetails(answers) {
  return getAjaxCall("index.php?page=exam/getanswersinfo", "post", false, {
    answers: answers,
  });
}

function getNameSurnameId() {
  return $("#studentsDropDown").val().split(" ");
}

function getDifficultyScores(questions, answers) {
  let scores = {
    easy: {
      total: 0,
      correct: 0,
    },
    medium: {
      total: 0,
      correct: 0,
    },
    hard: {
      total: 0,
      correct: 0,
    },
  };
  let converter = {
    1: "easy",
    2: "medium",
    3: "hard",
  };

  questions.forEach((currentQuestion) => {
    scores[converter[currentQuestion["difficulty"]]]["total"]++;

    for (let i = 0; i < answers.length; i++) {
      if (
        answers[i]["fkQuestion"] == currentQuestion["idQuestion"] &&
        answers[i]["score"] > 0
      ) {
        scores[converter[currentQuestion["difficulty"]]]["correct"]++;
        break;
      }
    }
  });
  return scores;
}

function getAllStudentsScoresNames() {

  let ids = getAjaxCall("index.php?page=exam/getallstudentsids", "post", false, {
    idExam: globalExamInfo["idExam"],
  });

  return getAjaxCall("index.php?page=exam/allscores", "post", false, {
    students: globalExamInfo["users"],
    idStudents: ids,
    idExam: globalExamInfo["idExam"],
  });
}

//Returns the exam test Settings.
function getTestSettings() {
  return getAjaxCall("index.php?page=exam/testsettings", "post", false, {
    idExam: globalExamInfo["idExam"],
  });
}

//Returns the count of correct answers for each category -> true false, numeric etc...
function getAnswersCount() {
  let nameSurnameId = getNameSurnameId();

  return getAjaxCall("index.php?page=exam/getanswercount", "post", false, {
    idStudent: nameSurnameId[2],
    idExam: globalExamInfo["idExam"],
  });
}

function getNumberOfCorrectAnswers() {
  let total = 0;
  getAnswersCount()["correctAnswers"].forEach(function (element) {
    total += Number(element["cnt"]);
  });
  return total;
}

function getCorrectAnswersCountCat() {
  return getAjaxCall(
    "index.php?page=exam/getanswerscorecategory",
    "post",
    false,
    {
      idStudent: nameSurnameId[2],
      idExam: globalExamInfo["idExam"],
    }
  );
}

function getScoresData() {
  let scoresData = {};

  let counterCategoryAnswers = getAnswersCount();
  for (let i = 0; i < counterCategoryAnswers["correctAnswers"].length; i++) {
    scoresData[counterCategoryAnswers["correctAnswers"][i]["type"]] = Number(counterCategoryAnswers["correctAnswers"][i]["cnt"]);
  }
  return scoresData;
}

function getDiffScores() {
  let nameSurnameId = getNameSurnameId();

  let answers = getAjaxCall(
    "index.php?page=exam/studentanswers",
    "post",
    false,
    {
      idStudent: nameSurnameId[2],
      idExam: globalExamInfo["idExam"],
    }
  );
  return getDifficultyScores(getQuestionsDetails(answers), answers);
}

//Francesco
function getTimeStartTimeEnd() {
  let nameSurnameId = getNameSurnameId();

  return getAjaxCall("index.php?page=exam/getdateusertest", "post", false, {
    idStudent: nameSurnameId[2],
    idExam: globalExamInfo["idExam"],
  });
}

function getUserTestDetails() {
  let nameSurnameId = getNameSurnameId();
  return getAjaxCall("index.php?page=exam/getdetailsusertest", "post", false, {
    idStudent: nameSurnameId[2],
    idExam: globalExamInfo["idExam"],
    idLanguage: languagesCodes[getCurrentUser()["lang"]],
  });
}

function initValuesQuestionTab() {
  let totalAndCorr = getTotalAndCorrect();
  getUserTestDetails().forEach((element) => {
    let percentageNumbers = currentQuestionNumbers(totalAndCorr, element);
    if (Number(element["score"]) > 0) {
      appendTableHTML(true, element, percentageNumbers);
    } else {
      appendTableHTML(false, element, percentageNumbers);
    }
  });

  setClickListenersCollaps();
}

//Others
function roundTwoDigits(toRound) {
  return Math.round((toRound + Number.EPSILON) * 100) / 100;
}
function hideCharts() {
  $(".charts")[0].style.display = "none";
  $(".global-charts")[0].style.display = "none";
  $("#printBtn")[0].style.display = "none";
  $("#totalScore")[0].style.display = "none";
}

function setAllStudentsChartsVisible() {
  $(".global-charts")[0].style.display = "inline";
}

function setStudentChartsVisible() {
  $(".charts")[0].style.display = "inline";
  $("#printBtn")[0].style.display = "inline";
  $("#totalScore")[0].style.display = "inline";
}

function appendTotalScore() {
  $(".answers").html("");
  $(".answers").append(
    "<b id='totalScore'>" +
    ttScoreStudent +
    ": " +
    roundTwoDigits(currentStudentTotalScore()) +
    "/" + globalExamInfo["testSettings"]["scoreType"] + "</b>"
  );
}

function currentStudentTotalScore() {
  let totalScore = 0;
  nameSurnameId = getNameSurnameId();

  let answers = getAjaxCall(
    "index.php?page=exam/studentanswers",
    "post",
    false,
    {
      idStudent: nameSurnameId[2],
      idExam: globalExamInfo["idExam"],
    }
  );
  for (i in answers) {
    totalScore += Number(answers[i]["score"]);
  }

  return totalScore;
}

//Francesco
function initValuesSummaryTab() {
  let times = getTimeStartTimeEnd();
  $("#idStart")[0].innerText += times["timeStart"];
  $("#idEnd")[0].innerText += times["timeEnd"];

  $("#idScore")[0].innerText += roundTwoDigits(currentStudentTotalScore());
  $("#idTotal")[0].innerText += globalExamInfo["testSettings"]["scoreType"];
  $("#idPresented")[0].innerText += globalExamInfo["testSettings"]["questions"];
  $("#idAnswered")[0].innerText += getNumberOfCorrectAnswers();
}

function createStudentCharts() {
  //------Chart 1 info------//
  let diffScores = getDiffScores();

  let dataChart1 = {
    labels: [ttD1, ttD2, ttD3],
    datasets: [
      {
        label: [tttotals],
        data: [
          globalExamInfo["totalEasy"],
          globalExamInfo["totalMedium"],
          globalExamInfo["totalHard"],
        ],
        borderWidth: 1,
      },
    ],
  };
  //------Chart 2 info------//
  let noCorrectAnswers =
    Number(diffScores["easy"]["correct"]) +
    Number(diffScores["medium"]["correct"]) +
    Number(diffScores["hard"]["correct"]);

  let dataChart2 = {
    labels: [ttD1, ttD2, ttD3],
    datasets: [
      {
        label: [ttPercentage],
        data: [
          toPercentageRounded(diffScores["easy"]["correct"], noCorrectAnswers),
          toPercentageRounded(
            diffScores["medium"]["correct"],
            noCorrectAnswers
          ),
          toPercentageRounded(diffScores["hard"]["correct"], noCorrectAnswers),
        ],
        backgroundColor: [SOFT_GREEN, SOFT_YELLOW, SOFT_RED],
        borderColor: [HARD_GREEN, HARD_YELLOW, HARD_RED],
        borderWidth: 1,
      },
    ],
  };

  //------Chart 3 info------//
  let dataChart3 = {
    labels: [ttGlobal, ttStudent],
    datasets: [
      {
        label: [ttAverages],
        data: [globalExamInfo["avg"], currentStudentTotalScore()],
        borderWidth: 1,
      },
    ],
  };

  //------Chart 4 info------//
  let scoresData = getScoresData()
  let data4 = [];

  //Orders the result based on the order of the categories.
  for (i of categories) {
    data4.push(scoresData[i])
  }

  let dataChart4 = {
    labels: categories,
    datasets: [
      {
        data: data4,
        borderWidth: 1,
      },
    ],
  };
  //------Chart 5 info------//
  let selectedStudentDuration = globalExamInfo["arrayDuration"].filter(
    function (item) {
      if (item.name === nameSurnameId[0] && item.surname === nameSurnameId[1]) {
        return true;
      }
    }
  )[0]["duration"];

  let dataChart5 = {
    labels: [ttGlobal, ttStudent],
    datasets: [
      {
        label: [ttStudentDuration],
        data: [
          globalExamInfo["avgDuration"],
          selectedStudentDuration,
        ],
        backgroundColor: [SOFT_RED, SOFT_GREEN],
        borderColor: [HARD_RED, HARD_GREEN],
        borderWidth: 1,
      },
    ],
  };

  let optionsChart5 = {
    indexAxis: "y",
    responsive: false,
    plugins: {
      title: {
        display: true,
        text: ttStudentTimeComparison,
      },
      tooltip: {
        enabled: true,
        callbacks: {
          label: function (tooltipItem, data) {
            const date = new Date(null);
            date.setSeconds(tooltipItem.formattedValue);
            const hmsStud = date.toISOString().slice(11, 19);
            return ttDateFormat + " " + hmsStud;
          }
        }
      },
    },
  };

  //------Chart 6 info------//
  data6 = getCorrectAnswersCountCat();

  //Orders the result based on the order of the categories.
  let greenLine = [];
  for (i of categories) {
    for (j of data6['correctAnswers']) {
      if (i == j["type"]) {
        greenLine.push(j["cnt"] * globalExamInfo["testSettings"]["scale"]);
      }
    }
  }

  //Orders the result based on the order of the categories.
  let redDots = [];
  for (i of categories) {
    for (j of data6['correctAnswers']) {
      if (i == j["type"]) {
        redDots.push(j["total_score"]);
      }
    }
  }

  let dataChart6 = {
    labels: categories,
    datasets: [
      {
        label: ttMaximumObtainable,
        data: greenLine,
        backgroundColor: SOFT_GREEN,
        borderColor: HARD_GREEN,
        borderWidth: 1,
        tension: 0.1,
      },
      {
        type: "scatter",
        label: ttScored,
        data: redDots,
        backgroundColor: SOFT_RED,
        borderColor: HARD_RED,
        tension: 0.1,
      },
    ],
  };

  //------Chart 7 info------//
  let dataChart7 = {
    labels: [ttMiniumScore, ttStudent, ttMaximumScore],
    datasets: [
      {
        label: ["Max Min"],
        data: [
          globalExamInfo["minScore"],
          currentStudentTotalScore(),
          globalExamInfo["maxScore"],
        ],
        backgroundColor: SOFT_YELLOW,
        borderColor: HARD_YELLOW,
        borderWidth: 1,
      },
    ],
  };

  //------Chart 8 info------//
  let dataChart8 = {
    labels: [ttD1s, ttD2s, ttD3s],
    datasets: [
      {
        label: ttCorrect,
        data: [diffScores["easy"]["correct"], diffScores["medium"]["correct"], diffScores["hard"]["correct"]],
        backgroundColor: SOFT_YELLOW,
        borderColor: HARD_YELLOW,
        borderWidth: 1
      },
      {
        label: tttotals,
        data: [globalExamInfo["totalEasy"], globalExamInfo["totalMedium"], globalExamInfo["totalHard"]],
        backgroundColor: SOFT_PURPLE,
        borderColor: HARD_PURPLE,
        borderWidth: 1
      }
    ],
  };

  //--------------------//
  let chartType = [
    "doughnut",
    "bar",
    "line",
    "polarArea",
    "bar",
    "line",
    "line",
    "bar",
  ];
  let dataChart = [
    dataChart1,
    dataChart2,
    dataChart3,
    dataChart4,
    dataChart5,
    dataChart6,
    dataChart7,
    dataChart8,
  ];

  labels = [
    ttStudentPieTitle,
    ttStudentBarTitle,
    ttStudentLinearAvgTitle,
    ttStudentRadiusQuestionsType,
    undefined,
    ttPointsPerQuestions,
    ttStudentComparisonTitle,
    ttCorrectWrongPieTitle,
  ];

  for (let i = 0; i < 8; i++) {
    let optionsOnlyLabel = {
      responsive: false,
      plugins: {
        title: {
          display: true,
          text: "label",
        },
      },
    };
    optionsOnlyLabel["plugins"]["title"]["text"] = labels[i];
    if (i != 4) {
      arrayOfSingleStudentCharts[i] = createChart(
        $("#chart" + (i + 1)),
        chartType[i],
        dataChart[i],
        optionsOnlyLabel
      );
    } else {
      arrayOfSingleStudentCharts[i] = createChart(
        $("#chart" + (i + 1)),
        chartType[i],
        dataChart[i],
        optionsChart5
      );
    }
  }
}

function createAllStudentsCharts() {
  //------Chart 1 info------//
  let studentsScoresNames = getAllStudentsScoresNames();

  let dataChartGlobal1 = {
    labels: studentsScoresNames["names"],
    datasets: [
      {
        label: ttScores,
        data: studentsScoresNames["scores"],
        backgroundColor: SOFT_GREEN,
        borderColor: HARD_GREEN,
        pointStyle: "circle",
        pointRadius: 10,
        pointHoverRadius: 15,
      },
    ],
  };

  let optionsChartGlobal1 = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      title: {
        display: true,
        text: (ctx) => ttGlobalChartScores,
      },
    },
    scale: {
      y: {
        min: 0,
        max: roundTwoDigits(
          Number(globalExamInfo["testSettings"]["scoreType"])
        ),
      },
    },
    scales: {
      x: {
        display: false,
      },
    },
  };
  //------Chart 2 info------//
  jsonAllStudentsScores = getAllStudentsScoresNames();

  let totalMaxScore =
    jsonAllStudentsScores["scores"].length *
    globalExamInfo["testSettings"]["scoreType"];
  let sum = jsonAllStudentsScores["scores"].reduce(
    (accumulator, currentValue) => Number(accumulator) + Number(currentValue)
  );
  let dataChartGlobal2 = {
    labels: [ttWrong, ttGlobalPieLabelCorrect],
    datasets: [
      {
        data: [totalMaxScore - sum, sum],
        backgroundColor: [SOFT_BLUE, SOFT_YELLOW],
        borderColor: [HARD_BLUE, HARD_YELLOW],
        borderWidth: 1,
      },
    ],
  };

  let optionsChartGlobal2 = {
    responsive: false,
    maintainAspectRatio: true,
    plugins: {
      title: {
        display: true,
        text: ttGlobalPieTitle,
      },
    },
  };

  //------Chart 3 info------//
  let data3Correct = {
    label: "Correct",
    data: [],
    backgroundColor: SOFT_BLUE,
    borderColor: HARD_BLUE,
    borderWidth: 1,
  };
  let data3Total = {
    label: "Total",
    data: [],
    backgroundColor: SOFT_GRAPE,
    borderColor: HARD_GRAPE,
    borderWidth: 1,
  };

  categories.forEach(function (catLabel) {

    data3Correct.data.push(
      globalExamInfo["globalCatCorrect"][catLabel]
    );
    data3Total.data.push(
      globalExamInfo["globalCatCount"][catLabel]
    );
  });

  let dataChartGlobal3 = {
    labels: [ttOpenAnswer, ttMc, ttSc, ttOpenNum, ttTf, ttYn],
    datasets: [
      data3Correct,
      data3Total,
    ]
  };

  let optionsChartGlobal3 = {
    responsive: false,
    maintainAspectRatio: true,
    plugins: {
      title: {
        display: true,
        text: ttCorrectPerCategory,
      },
      legend: {
        display: false,
      },
      subtitle: {
        display: true,
        text: ttBarSubtitle,
        position: 'bottom',
        padding: {
          top: 10,
        }
      },
    },
  };


  //------Chart 4 info------//
  studentsScoresNames["scores"] = studentsScoresNames["scores"].filter(Number);
  studentsScoresNames["scores"] = studentsScoresNames["scores"].map(function (element) {
    return parseInt(element, 10);
  });

  function gaussianPoints(x, avg, sd) {
    return (1 / (sd * Math.sqrt(2 * Math.PI))) * (Math.E ** (-((x - avg) ** 2) / 2 * (sd ** 2)));
  }


  let points = [];
  let labelsArr = [];

  incrementValue = Number(globalExamInfo.testSettings["scoreType"]) / 50;

  for (let i = 0; i < Number(globalExamInfo.testSettings["scoreType"]) + 1; i += incrementValue) {
    crtPoint = gaussianPoints(i, globalExamInfo.avg, 1);
    points.push(crtPoint);
    labelsArr.push(i);
  }

  let absFreq = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

  for (i of studentsScoresNames["scores"]) {
    absFreq[i]++;
  }

  pointsExpected = []

  let expectedAvg = Number(globalExamInfo.testSettings["scoreType"]) / 2 + (Number(globalExamInfo.testSettings["scoreType"]) / 10) * 3;

  for (let i = 0; i < Number(globalExamInfo.testSettings["scoreType"]) + 1; i += incrementValue) {
    crtPoint = gaussianPoints(i, expectedAvg, 1);
    pointsExpected.push(crtPoint);
  }

  let dataChartGlobal4 = {
    label: "Gauss",
    labels: labelsArr.map(x => Math.round(x * 10) / 10),
    datasets: [
      {
        label: ttAvg,
        data: points,
        cubicInterpolationMode: 'monotone',
        borderColor: HARD_PURPLE,
        backgroundColor: SOFT_PURPLE,
        borderWidth: 1,
      },
      {
        label: ttExpectedAvg,
        data: pointsExpected,
        cubicInterpolationMode: 'monotone',
        borderColor: HARD_YELLOW,
        backgroundColor: SOFT_YELLOW,
        borderWidth: 1,
      },
    ],
  };

  let optionsChartGlobal4 = {
    responsive: false,
    maintainAspectRatio: true,
    plugins: {
      title: {
        display: true,
        text: "Gauss",
      },
      legend: {
        display: true,
      },
    },
  };

  let optionsChartGlobal5 = {
    responsive: false,
    maintainAspectRatio: true,
    plugins: {
      title: {
        display: true,
        text: ttCompareWithAvg,
      },
      legend: {
        display: true,
      },
      tooltip: {
        enabled: true,
        callbacks: {
          title: function (tooltipItems, data) {
            return '';
          },
          label: function (tooltipItem, data) {
            return ttScores + ": " + roundTwoDigits(tooltipItem["parsed"]["y"]);
          }
        }
      },
      subtitle: {
        display: true,
        text: [ttScatterSubtitle1, ttScatterSubtitle2],
        position: 'bottom',
        padding: {
          top: 10,
        }
      },
    },
    scales: {
      x: {
        ticks: {
          display: false,
        }
      },
    },
  };

  let dataChartGlobal5 = {
    labels: Array.from(Array(globalExamInfo.users.length * 2).keys()),
    datasets: [
      {
        label: ttScores,
        data: getAllStudentsScoresNames()['scores'],
        borderColor: HARD_RED_ROME,
        backgroundColor: SOFT_RED_ROME,
        borderWidth: 1,
      },
      {
        label: ttAvg,
        data: Array(getAllStudentsScoresNames()['scores'].length).fill(globalExamInfo['avg']),
        type: 'line',
        borderColor: HARD_YELLOW_ROME,
        backgroundColor: SOFT_YELLOW_ROME,
        borderWidth: 1,
        pointRadius: 0,
        order: 0
      }
    ],
  };

  //------------------------//
  let chartType = ["line", "pie", "bar", "line", "scatter"];
  let dataChart = [dataChartGlobal1, dataChartGlobal2, dataChartGlobal3, dataChartGlobal4, dataChartGlobal5];
  let optionsChart = [
    optionsChartGlobal1,
    optionsChartGlobal2,
    optionsChartGlobal3,
    optionsChartGlobal4,
    optionsChartGlobal5
  ];

  for (let i = 0; i < 5; i++) {
    arrayOfSingleStudentCharts[i] = createChart(
      $("#chart-global-" + (i + 1)),
      chartType[i],
      dataChart[i],
      optionsChart[i]
    );
  }
}

function toPercentageRounded(part, total) {
  return roundTwoDigits((Number(part) / Number(total)) * 100);
}

//Francesco's functions
function setAnswersTableVisible() {
  $("#coachingTab")[0].style.display = "table";
  $("#questionTab")[0].style.display = "table";
  $("#questionsCoaching")[0].style.display = "inline";
  $("#printBtn")[0].style.display = "inline";
}

function appendTableHTML(isCorrect, element, percentageNumbers) {
  let cssClass = "tableRow-correct";
  if (isCorrect) {
    cssClass = "tableRow-correct";
  } else {
    cssClass = "tableRow-wrong";
  }
  $("#questionTab").append(
    "<tr class = '" +
    cssClass +
    "'><td> " +
    element["shortText"] +
    "</td><td>" +
    translateCode[element["type"]] +
    "</td><td class = 'scoreColumn'><button type='button' class='collapsible'><b><u>" +
    element["score"] +
    " / " +
    globalExamInfo["testSettings"]["scale"] +
    "</b></u></button><div class='content'><p>" +
    ttDropDownAnalyticPt1 +
    " " +
    toPercentageRounded(
      percentageNumbers["correctCount"],
      percentageNumbers["totalCount"]
    ) +
    "% " +
    ttDropDownAnalyticPt2 +
    "</p></div></td></tr>"
  );
}

function appendTable() {
  let translations = [
    ttObtainedScore,
    ttMaxObt,
    ttCorrectAnswers,
    ttTotalAnswers,
    ttExamStart,
    ttExamEnd,
  ];
  $("#coachingTab td").each(function (index) {
    this.innerHTML = "";
    this.innerHTML = translations[index] + ":&nbsp";
  });

  initValuesSummaryTab();

  $(".tableRow-correct").remove();
  $(".tableRow-wrong").remove();
  initValuesQuestionTab();
}

function hideAnswersTable() {
  $("#coachingTab")[0].style.display = "none";
  $("#questionTab")[0].style.display = "none";
  $("#questionsCoaching")[0].style.display = "none";
  $("#printBtn")[0].style.display = "none";
}

function getTotalAndCorrect() {
  return getAjaxCall("index.php?page=exam/gettotalandcorrect", "post", false, {
    idExam: globalExamInfo["idExam"],
  });
}

function currentQuestionNumbers(arrayQuestions, crtElement) {
  return arrayQuestions.filter(function (questionSet) {
    if (questionSet["fkQuestion"] == crtElement["fkQuestion"]) {
      return true;
    }
    return false;
  })[0];
}

function getCurrentUser() {
  return getAjaxCall("index.php?page=exam/getcurrentuser", "post", false, {});
}

function createChart(ctx, typePar, dataPar, optionsPar) {
  return new Chart(ctx, {
    type: typePar,
    data: dataPar,
    options: optionsPar,
  });
}

function getAjaxCall(urlPar, typePar, asyncPar, dataPar) {
  console.log(urlPar);console.log(dataPar);console.log(typePar);console.log(asyncPar);
  let result = 0;
  $.ajax({
    url: urlPar,
    type: typePar,
    async: asyncPar,
    data: dataPar,
    success: function (data) {
      result = JSON.parse(data);
    },
    error: function (request, status, error) {
      alert("jQuery AJAX request error:".error);
      return error;
    },
  });
  return result;
}

$(window).resize(function () {
  if (window.innerWidth < 1228 && !graphsAlignedForMobile) {
    jQuery("#chart5").detach().appendTo('#first-group-charts')
    jQuery("#chart6").detach().appendTo('#first-group-charts')
    jQuery("#chart7").detach().appendTo('#first-group-charts')
    jQuery("#chart8").detach().appendTo('#first-group-charts')
    graphsAlignedForMobile = true
  }
  if (window.innerWidth >= 1228 && graphsAlignedForMobile) {
    jQuery("#chart5").detach().appendTo('#second-group-charts')
    jQuery("#chart6").detach().appendTo('#second-group-charts')
    jQuery("#chart7").detach().appendTo('#second-group-charts')
    jQuery("#chart8").detach().appendTo('#second-group-charts')
  }
});