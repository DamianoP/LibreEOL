let fileEncoding = "ISO-8859-1";
// var altezza = $(window).height() - 305;
// if (altezza < 250) {
//     altezza = 250;
// }
let importArray = [];
let numQuestions = 0;
//document.getElementById("phase2").style.height = altezza + "px";
let ajaxWorking = 0;
let importCurrentID = 0;
let importInterval;
let arrayForAjax = [];
let pbRectangular;
let pbText;

async function selectFile() {
//    pbText = $('#progressBarImportText');
//    pbRectangular = $('#progressBarRectangular');
    await loadFile();
}

let importDebug = 0; //  0 for no debug (except the errors) --- 4 max info
function importCompleted() {
    setTimeout("closeLight()", 1);
}

function closeLight() {
    closeLightbox($("#importDIV2"));
}

let importTimestamp = new Date().getTime();

// function for load the file and call the right procedure based on the file format (xml or zip)
async function loadFile() {
    try {
        let selectedFile = document.getElementById('input').files[0];
        if (selectedFile === undefined) {
            showErrorMessage("ttSelectQTIfile")
        }

        if (selectedFile.type === 'text/xml') {
            if(await checkQTIFormat(selectedFile)) {
                readXML(selectedFile, null);
            }else{
                showErrorMessage("ttSelectQTIfile")
            }
        } else if (selectedFile.type === 'application/zip') {
            let filesFromZip = await unZip(selectedFile);
            if (filesFromZip) {
                let res = await getResources(filesFromZip);
                let bank = await findQBank(filesFromZip);
                if (bank) {
                    readXML(bank, res);
                }else{
                    showErrorMessage("ttSelectQTIfile")
                }
            }else{
                console.log(filesFromZip);
            }
        } else {
            showErrorMessage("ttSelectQTIfile")
        }
    } catch (e) {
        console.log(e)
    }
}

function readXML(selectedFile, resources) {
    let typeOK = [];
    numQuestions = 0;
    let reader = new FileReader();
    document.getElementById("importQuestions").style.display="unset";
    reader.onload = async function (e) {
        let readXml;
        try {
            readXml = e.target.result;
            let parser = new DOMParser();
            let doc = parser.parseFromString(readXml, "application/xml");
            if (importDebug >= 4) console.log(doc);
            let questions = doc.getElementsByTagName("questestinterop")[0].getElementsByTagName("item");
            numQuestions = questions.length;
            console.log("The XML contains n." + numQuestions + " questions");
            if (importDebug >= 1) console.log(questions);

            createPreviewTable()

            let id = 0;
            for (let i = 0; i < numQuestions; i++) {
                try {
                    if (importDebug >= 1) console.log("Parsing question i: " + i);
                    let parsedQuestion = {};
                    let questionI = questions[i];

                    //main nodes of the items
                    let presentation = questionI.getElementsByTagName("presentation")[0];
                    let resprocessing = questionI.getElementsByTagName("resprocessing")[0];

                    // retrieving the type from the metadata information
                    parsedQuestion["type"] = convertQuestionType(presentation, resprocessing);
                    if (['PL', 'HS', 'undefined'].includes(parsedQuestion["type"])) {
                        continue;
                    }
                    parsedQuestion["files"] = [];
                    if (parsedQuestion["type"] === "undefined") {
                        console.log(questionI);
                        continue;
                    }
                    if (importDebug >= 1) console.log("text content 1");
                    if (importDebug >= 1) console.log("text content 2");


                    //get the text of the question from the material nodes
                    parsedQuestion["text"] = getText(presentation)
                    parsedQuestion["text"] = purgeHTMLforImport(parsedQuestion["text"]);
                    if (["&nbsp;", "", " "].includes(parsedQuestion["text"])) {
                        console.log("Empty question text: " + questionI);
                        continue;
                    }

                    //retrieving the answers
                    let answers = getAnswers(presentation, resprocessing, parsedQuestion["type"]);
                    let total = getTotalScore(answers);
                    let numAnswers = answers.length;
                    if (importDebug >= 2) {
                        console.log("questionType " + parsedQuestion["type"]);
                        console.log("questionText " + parsedQuestion["text"]);
                    }
                    if (numAnswers > 0) {
                        parsedQuestion["answers"] = [];
                        for (let j = 0; j < numAnswers; j++) {
                            let parsedAnswer = {};
                            if (importDebug >= 3) console.log("Parsing question i: " + i + " answer j: " + j);
                            let answerJ = answers[j];
                            if (importDebug >= 1) console.log("text content 4");
                            parsedAnswer["text"] = answerJ["text"];
                            parsedAnswer["text"] = purgeHTMLforImport(parsedAnswer["text"]);

                            let tempScore = answerJ["score"];
                            parsedAnswer["score"] = parseScore(tempScore, total, parsedQuestion["type"], parsedAnswer["text"]);
                            parsedAnswer["originalScore"] = tempScore;
                            if (importDebug >= 3) {
                                console.log("answerText " + parsedAnswer["text"]);
                                console.log("answerScore " + parsedAnswer["score"]);
                            }
                            parsedQuestion["answers"].push(parsedAnswer);
                        }
                    }


                    //retrieving resources
                    if (resources !== null) {
                        console.log('importing files')
                        try {
                            let files = getFiles(presentation)
                            for (let h = 0; h < files.length; h++) {
                                let filename = files[h];
                                if (importDebug >= 1) console.log("text content 3");
                                let file = {};
                                file["base64"] = resources[filename];
                                file["name"] = filename.replace(" ", "_");
                                file["name"] = file["name"].replace("%20", "_");
                                file["name"] = importTimestamp + file["name"];
                                parsedQuestion["files"].push(file);
                            }
                        } catch (e) {
                            console.log(e);
                        }
                    }


                    typeOK.push(parsedQuestion["type"]);
                    parsedQuestion["checked"] = false;
                    importArray.push(parsedQuestion);
                    addToTable(id, parsedQuestion["type"], parsedQuestion["text"], parsedQuestion["answers"]);
                    //updateCustomPB(pbRectangular,pbText,i,numQuestions);
                    id++;


                } catch (e) {
                    console.log(i);
                    console.log(e);
                }
            }
            fillCustomPB(pbRectangular,pbText);
            showSuccessMessage(ttXMLreaded);
            document.getElementById("phase2").style.visibility="visible";
            console.log(typeOK);
            if (importDebug >= 1) console.log(importArray);
            if (importDebug >= 1) console.log(JSON.stringify(importArray));


            //debug
            //
            // let showRes = document.getElementById('resources');
            // console.log(importArray)
            // for (let question of importArray) {
            //     console.log(question.files);
            //     for (let file of question.files) {
            //         console.log('bip');
            //         if (['jpg', 'png', 'jpeg', 'gif'].includes(file.name.split('.').pop())) {
            //             let img = document.createElement('img');
            //             img.src = file['base64'];
            //             showRes.append(img);
            //         }
            //         if (['mp3', 'wav'].includes(file['name'].split('.').pop())) {
            //             let audio = document.createElement('audio');
            //             let source = document.createElement('source');
            //             audio.controls = true;
            //             source.src = file['base64'];
            //             source.type = 'audio/'+file['name'].split('.').pop();
            //             audio.append(source);
            //             showRes.append(audio);
            //         }
            //     }
            // }


        } catch (e) {
            //showErrorMessage("è accaduto un errore"/*ttXMLproblem*/);
            document.getElementById("tableContainer").innerText = "è accaduto un errore";
            console.log(e);
            document.getElementById("phase2").style.visibility="hidden";
            document.getElementById("phase1").style.display="unset";
            return;
        }
    };
    try{
        document.getElementById("phase1").style.display="none";
        //reader.readAsText(selectedFile,fileEncoding);
        reader.readAsText(selectedFile);
    }catch(e){
        console.log(e);
        document.getElementById("importQuestions").style.display="none";
        document.getElementById("phase2").style.visibility="hidden";
        document.getElementById("phase1").style.display="unset";
    }
}

//function for retrieving the question type from the item properties
function convertQuestionType(presentation, resprocessing) {

    let localType = null;
    let lid = presentation.getElementsByTagName("response_lid");
    let str = presentation.getElementsByTagName("response_str");
    let num = presentation.getElementsByTagName("response_num");

    if (lid.length > 0) {
        localType = "lid";
    }
    if (str.length > 0) {
        if (localType === null) {
            localType = "str";
        } else {
            throw new DOMException("too many different types of questions");
        }
    }
    if (num.length > 0) {
        if (localType === null) {
            return "NM";
        } else {
            throw new DOMException("too many different types of questions");
        }
    }
    if (localType === null) throw new DOMException("there are no response nodes, wrong format");
    if (lid.length > 1 || str.length > 1 || num.length > 1) throw new DOMException("there are too many nodes, wrong format");

    switch (localType) {
        case "lid":
            if (lid[0].getAttribute("rcardinality") === "Multiple") {
                return "MR";
            } else {
                let responseLabels = lid[0].getElementsByTagName("render_choice")[0].getElementsByTagName("response_label");
                if (responseLabels.length < 2) {
                    throw new DOMException("not enough labels for lid type question, wrong format");
                } else if (responseLabels.length === 2) {
                    let text1 = responseLabels[0].getElementsByTagName("material")[0].getElementsByTagName("mattext")[0].textContent.trim().toLowerCase();
                    console.log(responseLabels[0].getElementsByTagName("material")[0].getElementsByTagName("mattext")[0].textContent)
                    let text2 = responseLabels[1].getElementsByTagName("material")[0].getElementsByTagName("mattext")[0].textContent.trim().toLowerCase();
                    console.log(responseLabels[1].getElementsByTagName("material")[0].getElementsByTagName("mattext")[0].textContent)
                    if (checkTF(text1) && checkTF(text2)) {
                        return "TF";
                    }
                    if (checkYN(text1) && checkYN(text2)) {
                        return "YN";
                    }
                    return "MC"
                } else {
                    return "MC"
                }
            }
        case "str":
            let ident = str[0].getAttribute("ident");
            let respcondictions = resprocessing.getElementsByTagName("respcondition");
            for (let respcon of respcondictions) {
                let condvars = respcon.getElementsByTagName("conditionvar");
                for (let cond of condvars) {
                    let vars = cond.getElementsByTagName("varequal");
                    for (let vari of vars) {
                        if (vari.getAttribute("respident") === ident) {
                            return "TM";
                        }
                    }
                }
            }
            return "ES";
        default:
            throw new DOMException("error, wrong format");
    }

}

// alternative version of the previous function. this one retrieve the type from the metadata information of the item
function convertQuestionTypeAlt(itemtype) {
    switch (itemtype.textContent.toLowerCase()) {
        case 'multiple choice':
            return 'MC';
        case 'multiple response' :
            return 'MR';
        case 'numeric' :
            return 'NM';
        case 'text match' :
            return 'TM';
        case 'essay' :
            return 'ES';
        case 'pull-down list' :
            return 'PL';
        case 'hot spot' :
            return 'HS';
        default :
            return 'undefined';
    }
}

/*function to get text from material nodes that are children of the parameter nodeWithMaterials.
 matimage and mataudio nodes will be converted into img and audio tags (html)*/
function getText(nodeWithMaterials) {
    let text = "";
    let mats = nodeWithMaterials.querySelectorAll(":scope > material");
    for (let mat of mats) {
        let materials = mat.querySelectorAll(":scope > *");
        for (let material of materials) {
            switch (material.nodeName) {
                case 'mattext':
                    text += material.textContent;
                    break;
                case 'matimage':
                    text += '<img src="' + material.getAttribute('uri') + '" alt="(image)">';
                    break;
                case 'mataudio':
                    text += '<audio controls> <source src="' + material.getAttribute('uri') + '" type="' + material.getAttribute('audiotype') + '">(audio)</audio>';
            }
        }
    }
    console.log(text);
    return text;
}

// function for get the name of the files of matimage and mataudio nodes into an array
function getFiles(nodeWithMaterials) {
    let files = [];
    let fileNodes = nodeWithMaterials.querySelectorAll("matimage, mataudio");
    for (let node of fileNodes) {
        files.push(node.getAttribute('uri'))
    }
    return files;
}

// function for retrieving answers from the qti item. returns an array of answer objects with the format: { id: answerId, text: answerText, score: answerScore }
function getAnswers(presentation, resprocessing, type) { // format of answers: [['text', 'score'],....]
    if (type === "ES") return [];
    let answers = [];
    switch (type) {
        case "MC":
        case "TF":
        case "YN":
        case "MR":
            //response_lid node
            let lidMR = presentation.getElementsByTagName("response_lid")[0];
            let responsesMR = resprocessing.getElementsByTagName("respcondition");
            let resplabelsMR = lidMR.getElementsByTagName("render_choice")[0].getElementsByTagName("response_label");
            for (let label of resplabelsMR) {
                let answer = {};
                answer["id"] = label.getAttribute("ident");
                answer["text"] = getText(label);
                // searching for the answer processing in the respcondition section
                let scores = Object.values(responsesMR).filter(respcon => {
                    let condvar = respcon.getElementsByTagName("conditionvar")[0];
                    let varequal = condvar.getElementsByTagName("varequal")[0];
                    if (varequal === undefined) {
                        return false;
                    } else {
                        return varequal.textContent === answer["id"];
                    }
                });
                if (scores.length < 1) {
                    answer["score"] = 0;
                } else {
                    answer["score"] = scores[0].getElementsByTagName("setvar")[0].textContent;
                }
                answers.push(answer);
            }
            return answers;

        case "TM":
        case "NM":
            //the presentation doesn't matter, the only thing is the answer evaluation
            let responses = resprocessing.getElementsByTagName("respcondition");
            for (let resp of responses) {
                let answer = {};
                let varequals = resp.getElementsByTagName("conditionvar")[0].getElementsByTagName("varequal");
                if (varequals[0] === undefined || varequals[0] === null || varequals[0] === "") continue;
                answer["text"] = varequals[0].textContent;
                answer["score"] = resp.getElementsByTagName("setvar")[0].textContent;
                answers.push(answer);
            }
            return answers;
    }
}

// function for get the total score of the answers, needed for score conversion
function getTotalScore(answers) {
    let total = 0;
    for (let answer of answers) {
        let number = parseFloat(answer["score"]);
        number = !isNaN(number) ? number : 0
        total += number > 0 ? number : 0;
    }
    return total;
}

//function for checking if the text contains TF answers
function checkTF(text) {
    switch (text) {
        case "vero":
        case "falso":
        case "true":
        case "false":
            return true;
        default:
            return false;
    }
}

//function for checking if the text contains YN answers
function checkYN(text) {
    switch (text) {
        case "si":
        case "no":
        case "yes":
            return true;
        default:
            return false;
    }
}

function getQTname(type) {
    if (type === "ES") return "essay"; //ttQTES;
    else if (type === "MC") return "multiple choice";//ttQTMC;
    else if (type === "MR") return "multiple response";//ttQTMR;
    else if (type === "NM") return "numeric";//ttQTNM;
    else if (type === "TF") return "true false";//ttQTTF;
    else if (type === "TM") return "text match";//ttQTTM;
    else if (type === "YN") return "yes no";//ttQTTM;
    //else if(type==="HS")   return ttQTHS;
    else return "undefined";
}

function addToTable(t1, t2, t3, answer) {
    let tr = document.createElement("tr");
    let td1 = document.createElement("td");
    let checkBox = document.createElement("input");
    checkBox.setAttribute("type", "checkbox");
    checkBox.setAttribute("class", "checkboxImport");
    checkBox.setAttribute("id", "checkbox_" + t1);
    checkBox.setAttribute("onchange", "handlerCheckbox(" + t1 + ")");
    td1.appendChild(checkBox);
    let td2 = document.createElement("td");
    td2.innerHTML = getQTname(t2);
    let td3 = document.createElement("td");
    let textTD3 = document.createElement("p");
    textTD3.innerHTML = purgeHTMLforImport(t3);
    //textTD3.innerText=t3;
    td3.appendChild(textTD3);
    if (answer && answer.length > 0) {
        let answerArea = document.createElement("div");
        answerArea.innerText = "Risposte :"//ttAnswers + ":";
        for (let i = 0; i < answer.length; i++) {
            let answerI = document.createElement("p");
            answerI.innerHTML = "n." + (i + 1) + ", " + "Punteggio"/*ttScore*/ + ": " + answer[i]["score"] + ", " + "Testo"/*ttText*/ + ": " + purgeHTMLforImport(answer[i]["text"]);
            answerArea.appendChild(answerI);
        }
        td3.appendChild(answerArea);
    }
    tr.setAttribute("class", "elmentTableImport");
    td1.setAttribute("class", "elmentTableImport");
    td2.setAttribute("class", "elmentTableImport");
    td3.setAttribute("class", "elmentTableImport");
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    //tr.appendChild(td4);
    document.getElementById("tableImport").appendChild(tr);
}

function selectAll() {
    $("#tableImport tr :input").each(function () {
        $(this).prop('checked', true).trigger('change');
    })
}

function deselectAll() {
    $("#tableImport tr :input").each(function () {
        $(this).prop('checked', false).trigger('change');
    })

}

function updateCheckCounter() {
    let checkCounter = 0;
    for (let i = 0; i < importArray.length; i++) {
        if (importArray[i]["checked"]) {
            checkCounter++;
        }
    }
    document.getElementById("selectedQuestion").innerHTML = checkCounter;
}

function handlerCheckbox(id, multiple = false) {
    if ($("#checkbox_" + id).is(":checked")) {
        importArray[id]["checked"] = true;
    } else {
        importArray[id]["checked"] = false;
    }
    if (!multiple) {

    }
    updateCheckCounter();
    //console.log(importArray);
}

function parseScore(oldScore, total, type, text) {

    let score = Math.round((oldScore / total) * 10) / 10;

    if (score > 1) score = 1;
    if (score < -1) score = -1;
    if (type == "MR" || type == "NM") {
        score = score == 1 ? "1.0" : score;
        score = score == -1 ? "-1.0" : score;
    }
    if (type == "MC") {
        score = score != 1 ? 0 : score;
    }
    if (type == "TF") {
        text = text == "true" ? "T" : "F";
        score = score == 1 ? text + "*" + 1 : text + "*" + 0;
    }
    if (type == "YN") {
        text = text == "yes" ? "Y" : "N";
        score = score == 1 ? text + "*" + 1 : text + "*" + 0;
    }
    return score;
}

function importSelected() {
    if (!isSingleClickEOL()) return;
    importCurrentID = 0;
    document.getElementById("importButton").style.visibility = "hidden";
    document.getElementById("deselectALL").style.visibility = "hidden";
    document.getElementById("selectALL").style.visibility = "hidden";
    document.getElementById("closeImportPanel").style.visibility = "hidden";
    arrayForAjax = [];
    document.getElementById("progressBarRectangular").style.backgroundColor = "#4CAF50";
    emptyCustomPB(pbRectangular, pbText);
    for (let i = 0; i < importArray.length; i++) {
        if (importArray[i]["checked"]) {
            arrayForAjax.push(importArray[i]);
        }
    }
    if (importDebug >= 4) {
        console.log("To be imported: ");
        console.log(arrayForAjax);
    }
    document.getElementById("phase2").innerHTML = ttPleaseWait + "...";
    importInterval = setInterval(importWorker, 500);
}

function importWorker() {
    //importCurrentID=0;
    if (importCurrentID < arrayForAjax.length) {
        if (ajaxWorking == 0) {
            updateCustomPB(pbRectangular, pbText, importCurrentID, arrayForAjax.length, true);
            appendText(ttImportQuestionNumber + (importCurrentID + 1));
            ajaxWorking = 1;
            ajaxCall();
        }
    } else {
        fillCustomPB(pbRectangular, pbText);
        clearInterval(importInterval);
        importEnded();
    }
}

function ajaxCall() {
    if (importDebug > 0) {
        console.log("importing ID: " + importCurrentID);
        console.log(JSON.stringify(arrayForAjax[importCurrentID]));
    }
    $.ajax({
        url: 'index.php?page=question/importquestion',
        data: {
            importQuestion: JSON.stringify(arrayForAjax[importCurrentID]),
            mainLang: importLang,
            idTopic: importTopic,
            idS: pathUploadImport
        },
        type: 'POST',
        success: function (data) {
            if (importDebug >= 1) console.log(data);
            if (data == "ACK") {
                appendText(ttOperationCompleted);
            } else {
                appendText(ttOperationFailed);
            }
            importCurrentID++;
            ajaxWorking = 0;
            updateCustomPB(pbRectangular, pbText, importCurrentID, arrayForAjax.length, true);
        },
        error: function (data) {
            if (importDebug >= 1) console.log(data);
            importCurrentID++;
            ajaxWorking = 0;
            appendText(ttOperationFailed);
            updateCustomPB(pbRectangular, pbText, importCurrentID, arrayForAjax.length, true);
        }
    });
}

function importEnded() {
    appendText(ttImportPhase3OK);
    showSuccessMessage(ttImportReload, 7000);
    document.getElementById("closeImportPanel").style.display = "none";
    document.getElementById("reloadPage").style.display = "unset";
}

function appendText(text) {
    let p = document.createElement("p");
    p.innerText = text;
    let myDiv = document.getElementById("phase2");
    myDiv.appendChild(p);
    try {
        myDiv.scrollTop = myDiv.scrollHeight;
    } catch (e) {
        console.log(e);
    }
}

function purgeHTMLforImport(str) {
    if (str.startsWith("<p>") && str.endsWith("</p>")) {
        try {
            str = str.substr(3, str.length - 4);
        } catch (e) {
            console.log(e);
        }
    }
    let div = document.createElement("div");
    div.innerHTML = str;
    let images = div.getElementsByTagName("img");
    for (let i = 0; i < images.length; i++) { //find all images
        let image = images[i];
        let src = image.src;
        let srcName = src.split("/").pop().replace(" ", "_");
        srcName = srcName.replace("%20", "_");
        images[i].src = "/upload/" + "pathUploadImport" + "/uploaded/" + importTimestamp + srcName;
    }
    let audios = div.getElementsByTagName("source");
    for (let i = 0; i < audios.length; i++) { //find all audios
        let audio = audios[i];
        let src = audio.src;
        let srcName = src.split("/").pop().replace(" ", "_");
        srcName = srcName.replace("%20", "_");
        audios[i].src = "/upload/" + "pathUploadImport" + "/uploaded/" + importTimestamp + srcName;
    }
    return div.innerHTML;
}

// function for decompress zip files and retrieving the files (first level) as an array of objects with the format: { filename: fileName, type: fileType, data: fileAsBlobData }
async function unZip(selectedFile) {

    const zipFile = new zip.ZipReader(new zip.BlobReader(selectedFile));
    const entries = await zipFile.getEntries();
    let files = [], blob;

    for (let entry of entries) {

        let type = entry.filename.split('.').pop();
        let mimestring = "";
        if (!['xml', 'jpg', 'png', 'jpeg', 'gif', 'mp3', 'wav',].includes(type)) {
            return false
        }

        switch (type) {
            case 'xml':
                mimestring = 'application/xml';
                break;
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
                mimestring = 'image/' + type;
                break;
            case 'mp3':
            case 'wav':
                mimestring = 'audio/' + type;
        }

        blob = await entry.getData(
            new zip.BlobWriter([mimestring]),
            {
                onprogress: (index, max) => {
                }
            }
        );

        files.push({filename: entry.filename, type: type, data: blob})
    }

    await zipFile.close();
    return files
}

// function for get the question bank xml file from the array of objects files ( see unZip() function for the files data structure )
async function findQBank(files) {
    let file;
    for (file of files) {
        if (file.type === 'xml') {
            try {
                if( await checkQTIFormat(file.data)){
                    return file.data;
                }
            } catch (e) {
                console.log(e)
            }
        }
    }
    return false;
}

// function for get file resources as images or audio files from the array of objects files ( see unZip() function for the files data structure ).
// the function returns an object as big as the number of resources with the format: { filename1: base64fileData1, filename2: base64fileData2, .... }
async function getResources(files) {
    let res = {}, reader;
    for (let file of files) {
        if (file.type !== 'xml') {
            reader = new FileReader();

            const promise = new Promise((resolve) => {
                reader.onload = function () {
                    resolve(reader.result);
                }
                reader.readAsDataURL(file.data);
            });
            res[file.filename] = await promise;
        }
    }
    console.log(res)
    return res;
}
//check if the xml file have the qti format
async function checkQTIFormat(file){
    try {
        let xml = await file.text();
        let parser = new DOMParser();
        let doc = parser.parseFromString(xml, "application/xml");
        return doc.getElementsByTagName('questestinterop').length > 0;
    } catch (e) {
        console.log(e)
    }
}

// function for create the preview table in the page
function createPreviewTable() {
    let tableContainer = document.getElementById("tableContainer");
    tableContainer.innerHTML = "";
    let table = document.createElement("table");
    table.classList.add("tableImport");
    table.classList.add("tableCenteredMargin");
    table.setAttribute("id", "tableImport");

    let tr = document.createElement("tr");
    let th1 = document.createElement("th");
    th1.innerText = "Add";//ttAdd;
    let th2 = document.createElement("th");
    th2.innerText = "Tipo";//ttType;
    let th3 = document.createElement("th");
    th3.innerText = "Domanda";//ttQuestion;
    let th4 = document.createElement("th");
    th4.innerText = "testo"; //ttText;
    tr.appendChild(th1);
    tr.appendChild(th2);
    tr.appendChild(th3);
    tr.appendChild(th4);
    table.appendChild(tr);
    tableContainer.appendChild(table);
}
