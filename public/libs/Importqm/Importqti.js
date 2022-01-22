/**
 * File: import.js
 * User: Damiano Perri
 * Date: 2021
 * Time: 10:04 AM
 */

let fileEncoding="ISO-8859-1";
var altezza = $(window).height()-305;
if(altezza<250){
    altezza=250;
}
let importArray=[];
let numQuestions=0;
document.getElementById("phase2").style.height=altezza+"px";
let ajaxWorking=0;
let importCurrentID=0;
let importInterval;
let arrayForAjax=[];
let pbRectangular;
let pbText;
function selectFile(){
    pbText=$('#progressBarImportText');
    pbRectangular=$('#progressBarRectangular');
    readXML();
}
let importDebug=0; //  0 for no debug (except the errors) --- 4 max info
function importCompleted(){
    setTimeout("closeLight()",1);
}
function closeLight(){
    closeLightbox($("#importDIV2"));
}
let importTimestamp=new Date().getTime();
function readXML() {
    let typeOK   = [];
    importArray  = [];
    numQuestions = 0;
    let selectedFile = document.getElementById('input').files[0];
    if(selectedFile==undefined){
        showErrorMessage(ttSelectXMLfile);
    }
    let reader = new FileReader();
    document.getElementById("importQuestions").style.display="unset";
    reader.onload = function (e) {
        try {
            readXml = e.target.result;
            let parser = new DOMParser();
            let doc = parser.parseFromString(readXml, "application/xml");
            if (importDebug >= 4) console.log(doc);
            let questions = doc.getElementsByTagName("questestinterop")[0].getElementsByTagName("item");
            numQuestions = questions.length;
            console.log("The XML contains n." + numQuestions + " questions");
            if (importDebug >= 1) console.log(questions);
            let tableContainer = document.getElementById("tableContainer");
            tableContainer.innerHTML = "";
            let table = document.createElement("table");
            table.classList.add("tableImport");
            table.classList.add("tableCenteredMargin");
            table.setAttribute("id", "tableImport");

            let tr = document.createElement("tr");
            let th1 = document.createElement("th");
            th1.innerText = ttAdd;
            let th2 = document.createElement("th");
            th2.innerText = ttType;
            let th3 = document.createElement("th");
            th3.innerText = ttQuestion;
            //let th4=document.createElement("th");
            //th4.innerText=ttText;
            tr.appendChild(th1);
            tr.appendChild(th2);
            tr.appendChild(th3);
            //tr.appendChild(th4);
            table.appendChild(tr);
            tableContainer.appendChild(table);
            let id = 0;
            for(let i=0;i<numQuestions;i++){
                try {
                    if(importDebug>=1)console.log("Parsing question i: " + i);
                    let parsedQuestion = {};
                    let questionI = questions[i];
                    let presentation = questionI.getElementsByTagName("presentation")[0];
                    let resprocessing = questionI.getElementsByTagName("resprocessing")[0];
                    parsedQuestion["type"] = convertQuestionType(presentation, resprocessing);
                    parsedQuestion["files"] = [];
                    if (parsedQuestion["type"] === "undefined") {
                        console.log(questionI);
                        continue;
                    }
                    if (importDebug >= 1) console.log("text content 1");
                    // if (parsedQuestion["type"] === "MC" && questionI.getElementsByTagName("single")[0].textContent === "false") {
                    //     parsedQuestion["type"] = "MR";
                    // }
                    if (importDebug >= 1) console.log("text content 2");
                    parsedQuestion["text"] = getText(presentation)//questionI.getElementsByTagName("questiontext")[0].getElementsByTagName("text")[0].textContent;
                    //checkFiles
//                     try {
//                         let files = questionI.getElementsByTagName("questiontext")[0].getElementsByTagName("file");
//                         for (let h = 0; h < files.length; h++) {
//                             let fileXML = files[h];
// //                            workVar=fileXML;
//                             if (importDebug >= 1) console.log("text content 3");
//                             let base64 = fileXML.textContent;
//                             let name = fileXML.getAttribute("name");
//                             let file = {};
//                             file["name"] = name.replace(" ", "_");
//                             file["name"] = file["name"].replace("%20", "_");
//                             file["name"] = importTimestamp + file["name"];
//                             file["base64"] = base64;
//                             parsedQuestion["files"].push(file);
//                         }
//                     } catch (e) {
//                         console.log(e);
//                     }
                    //end checkFiles
                    parsedQuestion["text"] = purgeHTMLforImport(parsedQuestion["text"]);
                    if(parsedQuestion["text"]=="" || parsedQuestion["text"]=="&nbsp;" || parsedQuestion["text"]==" "){
                        console.log("Empty question text: "+questionI);
                        continue;
                    }
                    let answers = getAnswers(presentation, resprocessing, parsedQuestion["type"]);//questionI.getElementsByTagName("answer");
                    let numAnswers = answers.length;
                    if(importDebug>=2){
                        console.log("questionType " + parsedQuestion["type"]);
                        console.log("questionText " + parsedQuestion["text"]);
                    }
                    if (numAnswers > 0) {
                        parsedQuestion["answers"] = [];
                        for (let j = 0; j < numAnswers; j++) {
                            let parsedAnswer = {};
                            if(importDebug>=3)console.log("Parsing question i: " + i + " answer j: " + j);
                            let answerJ = answers[j];
                            if (importDebug >= 1) console.log("text content 4");
                            parsedAnswer["text"] = answerJ["text"];//answerJ.getElementsByTagName("text")[0].textContent;
                            parsedAnswer["text"] = purgeHTMLforImport(parsedAnswer["text"]);
                            let tempScore = answerJ["score"]//answerJ.attributes["fraction"].value / 100;
                            parsedAnswer["score"] = parseScore(tempScore, parsedQuestion["type"], parsedAnswer["text"]);
                            parsedAnswer["originalScore"] = tempScore;
                            if (importDebug >= 3) {
                                console.log("answerText " + parsedAnswer["text"]);
                                console.log("answerScore " + parsedAnswer["score"]);
                            }
                            parsedQuestion["answers"].push(parsedAnswer);
                            // try {
                            //     let files = answerJ.getElementsByTagName("file");
                            //     for (let h = 0; h < files.length; h++) {
                            //         let fileXML = files[h];
                            //         if (importDebug >= 1) console.log("text content 5");
                            //         let base64 = fileXML.textContent;
                            //         let name = fileXML.getAttribute("name");
                            //         let file = {};
                            //         file["name"] = name.replace(" ", "_");
                            //         file["name"] = file["name"].replace("%20", "_");
                            //         file["name"] = importTimestamp + file["name"];
                            //         file["base64"] = base64;
                            //         parsedQuestion["files"].push(file);
                            //     }
                            // } catch (e) {
                            //     console.log(e);
                            // }
                        }
                    }
                    typeOK.push(parsedQuestion["type"]);
                    parsedQuestion["checked"]=false;
                    importArray.push(parsedQuestion);
                    addToTable(id, parsedQuestion["type"], parsedQuestion["text"], parsedQuestion["answers"]);
                    updateCustomPB(pbRectangular,pbText,i,numQuestions);
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
            if(importDebug>=1)console.log(importArray);
            if(importDebug>=1)console.log(JSON.stringify(importArray));
        }catch(e){
            showErrorMessage(ttXMLproblem);
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

//function for retrieving the question type from the item, metadata
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

function getText(nodeWithMaterials) {
    let text = "";
    let mats = nodeWithMaterials.querySelectorAll(":scope > material");
    for (let mat of mats) {
        let mattexts = mat.getElementsByTagName("mattext");
        for (let mattext of mattexts) {
            if (text === "") {
                text = mattext.textContent;
            } else {
                text = text.concat( mattext.textContent);
            }
        }
    }
    console.log(text);
    return text;
}
// function for retrieving answers from the qti item
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
                    if(varequal === undefined){
                        return false;
                    }else {
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
                if (varequals[0] === undefined ||varequals[0] === null || varequals[0] === "") continue;
                answer["text"] = varequals[0].textContent;
                answer["score"] = resp.getElementsByTagName("setvar")[0].textContent;
                answers.push(answer);
            }
            return answers;
    }
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

function getQTname(type){
    if(type==="ES")        return ttQTES;
    else if(type==="MC")   return ttQTMC;
    else if(type==="MR")   return ttQTMR;
    else if(type==="NM")   return ttQTNM;
    else if(type==="TF")   return ttQTTF;
    else if(type==="TM")   return ttQTTM;
    //else if(type==="HS")   return ttQTHS;
    else                   return "undefined";
}

function addToTable(t1,t2,t3,answer){
    let tr=document.createElement("tr");
    let td1=document.createElement("td");
    let checkBox=document.createElement("input");
    checkBox.setAttribute("type", "checkbox");
    checkBox.setAttribute("class", "checkboxImport");
    checkBox.setAttribute("id", "checkbox_"+t1);
    checkBox.setAttribute("onchange", "handlerCheckbox("+t1+")");
    td1.appendChild(checkBox);
    let td2=document.createElement("td");
    td2.innerText=getQTname(t2);
    let td3=document.createElement("td");
    let textTD3=document.createElement("p");
    textTD3.innerText=purgeHTML(t3);
    //textTD3.innerText=t3;
    td3.appendChild(textTD3);
    if(answer && answer.length>0){
        let answerArea=document.createElement("div");
        answerArea.innerText=ttAnswers+":";
        for(let i=0;i<answer.length;i++){
            let answerI=document.createElement("p");
            answerI.innerText="n."+(i+1)+", "+ttScore+": "+answer[i]["originalScore"]+", "+ttText+": "+purgeHTML(answer[i]["text"]);
            answerArea.appendChild(answerI);
        }
        td3.appendChild(answerArea);
    }
    tr.setAttribute("class", "elmentTableImport");
    td1.setAttribute("class", "elmentTableImport");
    td2.setAttribute("class", "elmentTableImport");
    td3.setAttribute("class", "elmentTableImport");
    tr.appendChild(td1);tr.appendChild(td2);tr.appendChild(td3);
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

function updateCheckCounter(){
    let checkCounter=0;
    for(let i=0;i<importArray.length;i++){
        if(importArray[i]["checked"]){
            checkCounter++;
        }
    }
    document.getElementById("selectedQuestion").innerHTML=checkCounter;
}

function handlerCheckbox(id,multiple=false){
    if( $("#checkbox_"+id).is(":checked") ){
        importArray[id]["checked"] = true;
    }
    else {
        importArray[id]["checked"] = false;
    }
    if(!multiple){

    }
    updateCheckCounter();
    //console.log(importArray);
}

function parseScore(score,type,text){
    score = Math.round(score * 10) / 10;
    if(score>1)  score=1;
    if(score<-1) score=-1;
    if(type == "MR" || type=="NM"){
        score=score==1?"1.0":score;
        score=score==-1?"-1.0":score;
    }
    if(type == "MC"){
        score=score!=1?0:score;
    }
    if(type=="TF"){
        text=text=="true"?"T":"F";
        score=score==1?text+"*"+1:text+"*"+0;
    }
    if(type=="YN"){
        text=text=="yes"?"Y":"N";
        score=score==1?text+"*"+1:text+"*"+0;
    }
    return score;
}



function importSelected(){
    if(!isSingleClickEOL()) return;
    importCurrentID=0;
    document.getElementById("importButton").style.visibility="hidden";
    document.getElementById("deselectALL").style.visibility="hidden";
    document.getElementById("selectALL").style.visibility="hidden";
    document.getElementById("closeImportPanel").style.visibility="hidden";
    arrayForAjax=[];
    document.getElementById("progressBarRectangular").style.backgroundColor="#4CAF50";
    emptyCustomPB(pbRectangular,pbText);
    for(let i=0;i<importArray.length;i++){
        if(importArray[i]["checked"]){
            arrayForAjax.push(importArray[i]);
        }
    }
    if(importDebug>=4){
        console.log("To be imported: ");
        console.log(arrayForAjax);
    }
    document.getElementById("phase2").innerHTML=ttPleaseWait+"...";
    importInterval=setInterval(importWorker,500);
}

function importWorker(){
    //importCurrentID=0;
    if(importCurrentID<arrayForAjax.length){
        if(ajaxWorking==0){
            updateCustomPB(pbRectangular,pbText,importCurrentID,arrayForAjax.length,true);
            appendText(ttImportQuestionNumber+(importCurrentID+1));
            ajaxWorking=1;
            ajaxCall();
        }
    }else{
        fillCustomPB(pbRectangular,pbText);
        clearInterval(importInterval);
        importEnded();
    }
}

function ajaxCall(){
    if(importDebug>0){
        console.log("importing ID: "+importCurrentID);
        console.log(JSON.stringify(arrayForAjax[importCurrentID]));
    }
    $.ajax({
        url: 'index.php?page=question/importquestion',
        data: {
                importQuestion  : JSON.stringify(arrayForAjax[importCurrentID]),
                mainLang        : importLang,
                idTopic         : importTopic,
                idS             : pathUploadImport
        },
        type: 'POST',
        success: function (data) {
            if(importDebug>=1)console.log(data);
            if(data=="ACK"){
                appendText(ttOperationCompleted);
            }else{
                appendText(ttOperationFailed);
            }
            importCurrentID++;
            ajaxWorking=0;
            updateCustomPB(pbRectangular,pbText,importCurrentID,arrayForAjax.length,true);
        },
        error: function (data){
            if(importDebug>=1)console.log(data);
            importCurrentID++;
            ajaxWorking=0;
            appendText(ttOperationFailed);
            updateCustomPB(pbRectangular,pbText,importCurrentID,arrayForAjax.length,true);
        }
    });
}

function importEnded(){
    appendText(ttImportPhase3OK);
    showSuccessMessage(ttImportReload,7000);
    document.getElementById("closeImportPanel").style.display="none";
    document.getElementById("reloadPage").style.display="unset";
}

function appendText(text){
    let p=document.createElement("p");
    p.innerText=text;
    let myDiv=document.getElementById("phase2");
    myDiv.appendChild(p);
    try{
        myDiv.scrollTop = myDiv.scrollHeight;
    }catch(e){
        console.log(e);
    }
}


function purgeHTMLforImport(str){
    if(str.startsWith("<p>") && str.endsWith("</p>")){
        try { str = str.substr(3, str.length - 4); }
        catch(e){ console.log(e); }
    }
    let div = document.createElement("div");
    div.innerHTML = str;
    let images=div.getElementsByTagName("img");
    for(let i=0; i<images.length; i++){ //find all images
        let image=images[i];
        let src=image.src;
        let srcName=src.split("/").pop().replace(" ","_");
        srcName=srcName.replace("%20","_");
        images[i].src="/upload/"+pathUploadImport+"/uploaded/"+importTimestamp+srcName;
    }
    let audios=div.getElementsByTagName("source");
    for(let i=0; i<audios.length; i++){ //find all audios
        let audio=audios[i];
        let src=audio.src;
        let srcName=src.split("/").pop().replace(" ","_");
        srcName=srcName.replace("%20","_");
        audios[i].src="/upload/"+pathUploadImport+"/uploaded/"+importTimestamp+srcName;
    }
    return div.innerHTML;
}
