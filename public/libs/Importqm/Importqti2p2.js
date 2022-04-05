let fileEncoding = "UTF-8";
// var altezza = $(window).height() - 305;
// if (altezza < 250) {
//     altezza = 250;
// }
let importArray = [];
let typeOK = [];
let numQuestions = 0;
//document.getElementById("phase2").style.height = altezza + "px";
let ajaxWorking = 0;
let importCurrentID = 0;
let importInterval;
let arrayForAjax = [];
let pbRectangular;
let pbText;
let importDebug = 0;
let importTimestamp = new Date().getTime();

async function selectFile() {
//    pbText = $('#progressBarImportText');
//    pbRectangular = $('#progressBarRectangular');
    await loadFile();
}

function importCompleted() {
    setTimeout("closeLight()", 1);
}

function closeLight() {
    closeLightbox($("#importDIV2"));
}

// function for load the file and call the right procedure based on the file format (xml or zip)
async function loadFile() {
    try {
        let selectedFile = document.getElementById('input').files[0];
        let tableContainer = document.getElementById('tableContainer');
        tableContainer.innerHTML = "";
        createPreviewTable()
        numQuestions = 0;
        if (selectedFile === undefined) {
            //showErrorMessage("ttSelectQTIfile")
        } else {
            if (selectedFile.type === 'text/xml') {
                let questionItem = await selectedFile.text()
                if (checkQTIFormat(questionItem)) {
                    parseQuestion(questionItem, null, null)
                } else {
                    //showErrorMessage("ttSelectQTIfile")
                    console.log("invalid format");
                }
            } else if (selectedFile.type === 'application/zip') {
                let zip = await unZip(selectedFile);
                if (zip) {
                    let manifest = await getManifest(zip);
                    if (manifest) {
                        let files = await parseManifest(manifest, zip);
                        if (files) {
                            let quests = files["questions"];
                            for (let question of quests) {
                                let questionItem = files[question][question];
                                if (checkQTIFormat(questionItem)) {
                                    parseQuestion(questionItem, files[question], question)
                                } else {
                                    //showErrorMessage("ttSelectQTIfile")
                                    console.log("invalid format");
                                }
                            }
                        } else {
                            //showErrorMessage("wrongManifest")
                            console.log("could not parse the manifest");
                        }
                    } else {
                        //showErrorMessage("noManifest")
                        console.log("no manifest file in the zip");
                    }
                } else {
                    //showErrorMessage("unzipError")
                    console.log("unzip Error");
                }
            } else {
                //showErrorMessage("ttSelectQTIfile")
                console.log("wrong file format");
            }
        }
    } catch (e) {
        console.log(e)
    }
}

//function for parse the single xml question files
function parseQuestion(question, resources, questionFilename) {
    try {
        let parser = new DOMParser();
        let doc = parser.parseFromString(question, "text/xml");

        //assessmentItem
        let assessItem = doc.getElementsByTagName("assessmentItem")[0];

        replaceElements(assessItem);

        //assessmentItem nodes
        let responseDeclaration = assessItem.getElementsByTagName("responseDeclaration")[0];
        //let outcomeDeclaration = assessItem.getElementsByTagName("outcomeDeclaration")[0];
        let itemBody = assessItem.getElementsByTagName("itemBody")[0];
        //let responseProcessing = assessItem.getElementsByTagName("responseProcessing")[0];

        let parsedQuestion = {};

        // retrieving the type of the question
        parsedQuestion["type"] = convertQuestionType(responseDeclaration, itemBody);
        parsedQuestion["files"] = [];
        console.log(questionFilename + " " + parsedQuestion["type"]);
        if (parsedQuestion["type"] === "undefined") {
            return null
        }

        //get the text of the question from the itemBody node
        if (parsedQuestion["type"] === "HS") {
            let selPointInt = itemBody.getElementsByTagName('selectPointInteraction')[0];
            parsedQuestion["text"] = getQuestionText(selPointInt);
        } else {
            parsedQuestion["text"] = getQuestionText(itemBody);
        }
        //parsedQuestion["text"] = purgeHTMLforImport(parsedQuestion["text"]);
        if (["&nbsp;", "", " "].includes(parsedQuestion["text"])) {
            console.log("Empty question text");
            return null
        }

        //retrieving the answers
        let answers = getAnswers(itemBody, responseDeclaration, parsedQuestion["type"]);
        let total = getTotalScore(answers);
        let numAnswers = answers.length;
        if (numAnswers > 0) {
            parsedQuestion["answers"] = [];
            for (let i = 0; i < numAnswers; i++) {
                let parsedAnswer = {};
                let answerI = answers[i];
                parsedAnswer["text"] = answerI["text"];
                //parsedAnswer["text"] = purgeHTMLforImport(parsedAnswer["text"]);

                let tempScore = answerI["score"];
                parsedAnswer["score"] = parseScore(tempScore, total, parsedQuestion["type"], parsedAnswer["text"]);
                parsedAnswer["originalScore"] = tempScore;
                parsedQuestion["answers"].push(parsedAnswer);
            }
        }

        //retrieving resources
        if (resources !== null) {
            //console.log('importing files')
            try {
                for (let res in resources) {
                    if (res !== questionFilename) {
                        let filename = res.split("/").pop();
                        let file = {};
                        file["base64"] = resources[res];
                        file["name"] = filename.replace(" ", "_");
                        file["name"] = file["name"].replace("%20", "_");
                        file["name"] = importTimestamp + file["name"];
                        console.log(file["name"]+" "+res);

                        parsedQuestion["files"].push(file);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }

        typeOK.push(parsedQuestion["type"]);
        parsedQuestion["checked"] = false;
        importArray.push(parsedQuestion);
        addToTable('id', parsedQuestion["type"], parsedQuestion["text"], parsedQuestion["answers"], resources);
        //updateCustomPB(pbRectangular,pbText,i,numQuestions);

    } catch (e) {
        console.log(e);
    }
}

//unizp the file
async function unZip(file) {
    let zip = JSZip()
    await zip.loadAsync(file);
    return zip;
}

//function for get the manifest as string from the zip
async function getManifest(zip) {
    return await zip.file('imsmanifest.xml').async('String');
}

/*function for parse the manifest,  returns an object like
{
     questions:["qfile1.xml", "qfile2.xml", ...],
     qfile1.xml: {
                     qfile1.xml:"content of file as string",
                     pathResourseFile: "resource file content as base64 string"
                     ..
                  }
     qfile2.xml: {
                     ..
                 }
     ...
 }
 */
async function parseManifest(manifest, zip) {
    let filesMap = {};
    let questions = [];
    let parser = new DOMParser();
    let doc = parser.parseFromString(manifest, "application/xml");
    let resourcesNode = doc.getElementsByTagName('resources')[0];
    let resources = resourcesNode.getElementsByTagName('resource');

    //scanning resource nodes
    for (let res of resources) {
        let resHref = res.attributes.getNamedItem('href').textContent;
        //question filename
        questions.push(resHref);

        let files = res.getElementsByTagName('file');
        //scanning file nodes
        filesMap[resHref] = {};
        for (let file of files) {
            let fileHref = file.attributes.getNamedItem('href').textContent;
            if (fileHref !== resHref) {
                filesMap[resHref][fileHref] = await zip.file(fileHref).async('base64');     //resource file
            } else {
                filesMap[resHref][fileHref] = await zip.file(fileHref).async('string');     //question file
            }
        }
    }
    filesMap["questions"] = questions;
    return filesMap;
}

//function for retrieving the question type from the assessmentItem properties
function convertQuestionType(responseDeclaration, itemBody) {

    let localType = null;
    let choiceInt = itemBody.getElementsByTagName("choiceInteraction");
    let exTextInt = itemBody.getElementsByTagName("extendedTextInteraction");
    let textInt = itemBody.getElementsByTagName("textEntryInteraction");
    let selectPoint = itemBody.getElementsByTagName("selectPointInteraction");

    if (choiceInt.length === 1) {
        localType = "choice";
    }

    if (exTextInt.length === 1) {
        if (localType === null) {
            localType = "exttext";
        } else {
            return "undefined";
        }
    }

    if (textInt.length === 1) {
        if (localType === null) {
            localType = "text";
        } else {
            return "undefined";
        }
    }

    if (selectPoint.length === 1) {
        if (localType === null) {
            localType = "selectpoint";
        } else {
            return "undefined";
        }
    }

    if (localType === null) return "undefined";

    switch (localType) {
        case "choice":
            if (responseDeclaration.getAttribute("cardinality") === "multiple") {
                return "MR";
            } else {
                let simpleChoices = itemBody.getElementsByTagName("simpleChoice")
                if (simpleChoices.length < 2) {
                    throw new DOMException("invalide question format");
                } else if (simpleChoices.length === 2) {
                    let text1 = simpleChoices[0].textContent.trim().toLowerCase();
                    let text2 = simpleChoices[1].textContent.trim().toLowerCase();
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
        case "exttext":
            return "ES";

        case "text":
            if (responseDeclaration.getAttribute("baseType") === "string") {
                return "TM";
            } else {
                return "NM";
            }
        case "selectpoint":
            let areaMapEntries = responseDeclaration.getElementsByTagName("areaMapEntry");
            for (let area of areaMapEntries) {
                if (area.getAttribute("shape") !== "rect") {
                    return "undefined";
                }
            }
            return "HS";

        default:
            return "undefined";
    }

}

//function to get question text
function getQuestionText(itembody) {
    try {
        let text = "";
        replaceElements(itembody);
        let elems = itembody.querySelectorAll(":scope > div, :scope > img, :scope > audio")
        if (elems.length) {
            for (let elem of elems) {
                if (['audio', 'img'].includes(elem.nodeName)) {
                    let temp = elem.outerHTML;
                    temp = removeNamespace(temp);
                    text += temp;
                } else {
                    let temp = elem.innerHTML;
                    temp = removeNamespace(temp);
                    text += temp;
                }
            }
        }
        return text;
    } catch (e) {
        console.log(e);
    }
}

// function for retrieving answers from the qti item. returns an array of answer objects with the format: { id: answerId, text: answerText, score: answerScore }
function getAnswers(itemBody, responseDeclaration, type) { // format of answers: [['text', 'score'],....]
    if (type === "ES") return [];
    let answers = [];
    switch (type) {
        case "MC":
        case "TF":
        case "YN":
            //choiceInteraction single
            let choices = itemBody.getElementsByTagName("simpleChoice");
            let correct = responseDeclaration.getElementsByTagName("correctResponse")[0].getElementsByTagName("value")[0];
            for (let choice of choices) {
                let answer = {};
                answer["id"] = choice.getAttribute("identifier");
                answer["text"] = removeNamespace(choice.innerHTML);

                if (answer["id"] === correct.textContent) {
                    answer["score"] = 1;
                } else {
                    answer["score"] = 0;
                }
                answers.push(answer);
            }
            return answers;

        case "MR":
            //choiceInteraction multiple
            let choicesMultiple = itemBody.getElementsByTagName("simpleChoice");
            let mappings = responseDeclaration.getElementsByTagName("mapping")[0].getElementsByTagName("mapEntry");
            for (let choice of choicesMultiple) {
                let answer = {};
                answer["id"] = choice.getAttribute("identifier");
                answer["text"] = removeNamespace(choice.innerHTML);
                answer["score"] = 0;
                for (let entry of mappings) {
                    if (entry.getAttribute("mapKey") === answer["id"]) {
                        answer["score"] = entry.getAttribute("mappedValue");
                    }
                }
                answers.push(answer);
            }
            return answers;

        case "TM":
        case "NM":
            //the presentation doesn't matter, the only thing is the response declaration
            let mappingsText = responseDeclaration.getElementsByTagName("mapping")[0].getElementsByTagName("mapEntry");
            for (let entry of mappingsText) {
                let answer = {};
                answer["text"] = entry.getAttribute("mapKey");
                answer["score"] = entry.getAttribute("mappedValue");
                answers.push(answer);
            }
            return answers;

        case "HS":
            //the presentation doesn't matter, the only thing is the response declaration
            let areaMapEntries = responseDeclaration.getElementsByTagName("areaMapping")[0].getElementsByTagName("areaMapEntry");
            for (let entry of areaMapEntries) {
                let answer = {};
                answer["text"] = entry.getAttribute("coords");
                answer["score"] = entry.getAttribute("mappedValue");
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
    else if (type === "HS") return "hotspot";//ttQTHS;
    else return "undefined";
}

//replace some elements like object to img or audio elements and prompt to div elements
function replaceElements(node) {

    let objects = node.getElementsByTagName('object');
    let prompts = node.getElementsByTagName('prompt');
    let tables = node.getElementsByTagName('table');

    for (let obj of objects) {

        let objType = obj.getAttribute('type');
        let objSrc = obj.getAttribute('data');
        let objWidth = obj.getAttribute('width');
        let objHeight = obj.getAttribute('height');
        let type = objType.split('/')[0];

        if (type === 'image') {

            let img = obj.ownerDocument.createElement('img');
            img.setAttribute('alt', 'image');
            img.setAttribute('height', objHeight);
            img.setAttribute('width', objWidth);
            img.setAttribute('src', objSrc);
            obj.parentElement.replaceChild(img, obj);

        } else if (type === 'audio') {

            let audio = obj.ownerDocument.createElement('audio');
            let source = obj.ownerDocument.createElement('source');
            let text = obj.ownerDocument.createTextNode('audioFile')
            audio.setAttribute('controls', 'true');
            source.setAttribute('src', objSrc);
            source.setAttribute('type', objType);
            audio.appendChild(source);
            audio.appendChild(text);
            obj.parentElement.replaceChild(audio, obj);
        }
    }

    for (let prompt of prompts) {
        let div = prompt.ownerDocument.createElement('div');
        div.innerHTML = prompt.innerHTML;
        prompt.parentElement.replaceChild(div, prompt);
    }

    for (let table of tables) {
        table.setAttribute("border", "1");
        table.setAttribute("cellpadding", "1");
        table.setAttribute("cellspacing", "1");
        table.setAttribute("style", "width:500px;");
    }
    return node;
}

function addToTable(t1, t2, t3, answer, resources) {
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
    let textTD3 = document.createElement("div");
    textTD3.setAttribute("style", "background-color:#bbbbbb;");
    textTD3.innerHTML = purgeHTMLforPreview(t3, resources);
    //textTD3.innerHTML = t3;
    td3.appendChild(textTD3);
    let td4 = document.createElement("td");
    if (answer && answer.length > 0) {
        let answerArea = document.createElement("div");
        //answerArea.innerHTML = "Risposte :"//ttAnswers + ":";
        for (let i = 0; i < answer.length; i++) {
            let answerI = document.createElement("p");
            answerI.setAttribute("style", "background-color:#bbbbbb;");
            answerI.innerHTML = "n." + (i + 1) + ", " + "Punteggio"/*ttScore*/ + ": " + answer[i]["score"] + ", " + "Testo"/*ttText*/ + ":<br>" + purgeHTMLforPreview(answer[i]["text"],resources);
            answerArea.appendChild(answerI);
        }
        td4.appendChild(answerArea);
    }
    tr.setAttribute("class", "elmentTableImport");
    td1.setAttribute("class", "elmentTableImport");
    td2.setAttribute("class", "elmentTableImport");
    td3.setAttribute("class", "elmentTableImport");
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
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
    if($("#checkbox_" + id).is(":checked")) {
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
    if (type == "HS") {
        score = 1;
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

//remove the namespace attribute from tags
function removeNamespace(text) {
    let newtext = text.replace(/ xmlns="http:\/\/www\.imsglobal\.org\/xsd\/imsqti_v2p2"/g, "");
    newtext = newtext.replace(/ xmlns=""/g, "");
    return newtext
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

//prepare the texts for the preview
function purgeHTMLforPreview(str, resources){
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
        let srcName = src.split("/").pop();
        let res64 = resources["Resources/"+srcName];
        srcName = srcName.replace("%20", "_");
        images[i].src = "data:image/"+srcName.split(".").pop()+";base64,"+res64;
    }
    let audios = div.getElementsByTagName("source");
    for (let i = 0; i < audios.length; i++) { //find all audios
        let audio = audios[i];
        let src = audio.src;
        console.log(src);
        let srcName = src.split("/").pop();
        let res64 = resources["Resources/"+srcName];
        audios[i].src = "data:audio/"+srcName.split(".").pop()+";base64,"+res64;
    }
    return div.innerHTML;
}

//check if the xml file have the qti format
function checkQTIFormat(xml) {
    try {
        let parser = new DOMParser();
        let doc = parser.parseFromString(xml, "application/xml");
        let assess = doc.getElementsByTagName('assessmentItem')[0];
        let version = assess.getAttribute("xmlns");
        return version.includes("imsqti_v2p2") | version.includes("imsqti_v2p1");
    } catch (e) {
        console.log(e)
        return false;
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
    table.setAttribute("border", "1");
    table.setAttribute("frame", "box");
    table.setAttribute("cellpadding", "14")

    let tr = document.createElement("tr");
    let th1 = document.createElement("th");
    th1.innerText = "Add";//ttAdd;
    let th2 = document.createElement("th");
    th2.innerText = "Tipo";//ttType;
    let th3 = document.createElement("th");
    th3.innerText = "Domanda";//ttQuestion;
    let th4 = document.createElement("th");
    th4.innerText = "Risposte"; //ttText;
    tr.appendChild(th1);
    tr.appendChild(th2);
    tr.appendChild(th3);
    tr.appendChild(th4);
    table.appendChild(tr);
    tableContainer.appendChild(table);
}
