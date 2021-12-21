<?php

class MoodleXMLDocument
{
    private DOMDocument $root;
    private $currentQuestion;

    public function __construct()
    {
        $this->root = new DOMDocument();
        $this->root->formatOutput = true;
        $this->quizNode = $this->root->createElement("quiz");
        $this->currentQuestion = $this->root->createElement("temp");
        $this->root->appendChild($this->quizNode);
    }

    public function getDoc()
    {
        return $this->root->saveXML();
    }

    public function createCategory($name, $info = "", $idTopic = " ")
    {
        //question node
        $topic = $this->root->createElement('question');
        $topic->setAttribute("type", "category");

        //category node
        $category = $this->root->createElement("category");
        $category->appendChild($this->textNode('$course$/' . $name, false));
        $topic->appendChild($category);

        //info node
        $infoNode = $this->root->createElement('info');
        $infoNode->setAttribute("format", "html");

        if ($info === "") {
            $info = $name;
        }
        $infoNode->appendChild($this->textNode($info, false));
        $topic->appendChild($infoNode);

        //id node
        $topic->appendChild($this->idNode($idTopic));

        //append to quiz node
        $this->quizNode->appendChild($topic);
    }

    public function createQuestion($type, $id, $name, $text)
    {
        //create question
        $question = $this->root->createElement('question');
        $question->setAttribute("type", $this->selectType($type));

        //question name
        $question->appendChild($this->nameNode($name));

        //question text
        $question->appendChild($this->questionText($text));

        //general feedback
        $question->appendChild($this->generalFeedbackNode());

        //default grade
        $question->appendChild($this->defaultGradeNode());

        //penality
        if ($type === 'TF') {
            $question->appendChild($this->penalityNode(1.0000000));
        } else {
            $question->appendChild($this->penalityNode());
        }

        // hidden
        $question->appendChild($this->hiddenNode());

        //question properties
        if ($type === 'MC' || $type === 'YN') {
            $question->appendChild($this->shuffleAnswerNode());
            $question->appendChild($this->singleNode('true'));
            $question->appendChild($this->answerNumberingNode("abc"));
            $question->appendChild($this->correctFeedbackNode());
            $question->appendChild($this->pCorrectFeedbackNode());
            $question->appendChild($this->incorrectFeedbackNode());
        } elseif ($type === 'MR') {
            $question->appendChild($this->shuffleAnswerNode());
            $question->appendChild($this->singleNode());
            $question->appendChild($this->answerNumberingNode("abc"));
            $question->appendChild($this->correctFeedbackNode());
            $question->appendChild($this->pCorrectFeedbackNode());
            $question->appendChild($this->incorrectFeedbackNode());
        } elseif ($type === 'NM') {
            $question->appendChild($this->unitgradingtypeNode());
            $question->appendChild($this->unitpenalityNode());
            $question->appendChild($this->showunitsNode());
            $question->appendChild($this->unitsleftNode());
        } elseif ($type === 'TM') {
            $question->appendChild($this->usecaseNode());
        }

        $this->currentQuestion = $question;
        $this->quizNode->appendChild($this->currentQuestion);
    }

    public function createAnswer($id, $text, $score, $feedback)
    {
        $answer = $this->root->createElement("answer");
        $answer->setAttribute('fraction', $this->scoreFixed($score));
        if ($this->currentQuestion->getAttribute("type") === "truefalse") {
            $answer->appendChild($this->textNode($this->textAnswerFixed($score, $text), true));
        } elseif ($this->currentQuestion->getAttribute("type") === "numerical") {
            $answer->appendChild($this->textNode($this->textAnswerFixed($score, $text), true));
            $answer->appendChild($this->toleranceNode());
        } elseif ($this->currentQuestion->getAttribute("type") === "shortanswer") {
            $answer->appendChild($this->textNode($this->fixSrcPath($this->textAnswerFixed($score, $text)), true));
        } else {
            $answer->appendChild($this->textNode($this->fixSrcPath($this->textAnswerFixed($score, $text)), true));
        }
        $this->addFileNodes($answer, $text);
        $answer->appendChild($this->feedbackNode($feedback));
        $answer->appendChild($this->idNode($id));
        $this->currentQuestion->appendChild($answer);
    }

    private function selectType($type)
    {
        switch ($type) {
            case 'MR':
            case 'YN':
            case 'MC':
                return 'multichoice';
            case 'NM':
                return 'numerical';
            case 'TM':
                return 'shortanswer';
            case 'ES':
                return 'essay';
            case 'TF':
                return 'truefalse';
            default:
                return '';
        }
    }

    private function nameNode($text)
    {
        $name = $this->root->createElement('name');
        $textField = $this->textNode($text, true);
        $name->appendChild($textField);
        return $name;
    }

    private function questionText($text)
    {
        $qTextNode = $this->root->createElement('questiontext');
        $qTextNode->setAttribute('format', 'html');
        $textField = $this->textNode($this->fixSrcPath($text), true);
        $qTextNode->appendChild($textField);
        $this->addFileNodes($qTextNode, $text);

        return $qTextNode;
    }

    private function textNode($text, $cdata)
    {
        $node = $this->root->createElement("text");
        if ($cdata) {
            $textField = $this->root->createCDATASection($text);
        } else {
            $textField = $this->root->createTextNode($text);
        }
        $node->appendChild($textField);
        return $node;
    }

    private function idNode($id)
    {
        $node = $this->root->createElement('idnumber');
        $idField = $this->root->createTextNode($id);
        $node->appendChild($idField);
        return $node;
    }

    private function feedbackNode($text)
    {
        $node = $this->root->createElement('feedback');
        $textField = $this->textNode($text, false);
        $node->appendChild($textField);
        return $node;
    }

    private function shuffleAnswerNode($bool = 1)
    {
        $node = $this->root->createElement('shuffleanswers');
        $textField = $this->root->createTextNode($bool);
        $node->appendChild($textField);
        return $node;
    }

    private function singleNode($bool = 'false')
    {
        $node = $this->root->createElement('single');
        $textField = $this->root->createTextNode($bool);
        $node->appendChild($textField);
        return $node;

    }

    private function answerNumberingNode($text = 'none')
    {
        $node = $this->root->createElement('answernumbering');
        $textField = $this->root->createTextNode($text);
        $node->appendChild($textField);
        return $node;
    }

    private function generalFeedbackNode($text = '')
    {
        $node = $this->root->createElement('generalfeedback');
        $node->setAttribute('format', 'html');
        $textField = $this->textNode($this->fixSrcPath($text), false);
        $this->addFileNodes($node, $text);
        $node->appendChild($textField);
        return $node;
    }

    private function correctFeedbackNode($text = 'Risposta corretta.')
    {
        $node = $this->root->createElement('correctfeedback');
        $node->setAttribute('format', 'html');
        $textField = $this->textNode($this->fixSrcPath($text), false);
        $this->addFileNodes($node, $text);
        $node->appendChild($textField);
        return $node;
    }

    private function pCorrectFeedbackNode($text = 'Risposta parzialmente corretta.')
    {
        $node = $this->root->createElement('partiallycorrectfeedback');
        $node->setAttribute('format', 'html');
        $textField = $this->textNode($this->fixSrcPath($text), false);
        $this->addFileNodes($node, $text);
        $node->appendChild($textField);
        return $node;
    }

    private function incorrectFeedbackNode($text = 'Risposta errata.')
    {
        $node = $this->root->createElement('incorrectfeedback');
        $node->setAttribute('format', 'html');
        $textField = $this->textNode($this->fixSrcPath($text), false);
        $this->addFileNodes($node, $text);
        $node->appendChild($textField);
        return $node;
    }

    private function defaultGradeNode($grade = 1.0000000)
    {
        $node = $this->root->createElement('defaultgrade');
        $textField = $this->root->createTextNode($grade);
        $node->appendChild($textField);
        return $node;
    }

    private function penalityNode($penality = 0.3333333)
    {
        $node = $this->root->createElement('penality');
        $textField = $this->root->createTextNode($penality);
        $node->appendChild($textField);
        return $node;
    }

    private function hiddenNode($hidden = 0)
    {
        $node = $this->root->createElement('hidden');
        $textField = $this->root->createTextNode($hidden);
        $node->appendChild($textField);
        return $node;
    }


    private function usecaseNode($value = 0)
    {
        $node = $this->root->createElement('hidden');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function unitgradingtypeNode($value = 0)
    {
        $node = $this->root->createElement('unitgradingtype');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function unitpenalityNode($value = 0.1000000)
    {
        $node = $this->root->createElement('unitpenality');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function showunitsNode($value = 3)
    {
        $node = $this->root->createElement('showunits');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function unitsleftNode($value = 0)
    {
        $node = $this->root->createElement('unitsleft');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function toleranceNode($value = 0)
    {
        $node = $this->root->createElement('tolerance');
        $textField = $this->root->createTextNode($value);
        $node->appendChild($textField);
        return $node;
    }

    private function fileNode($src, $path = '/', $encoding = 'base64')
    {
        $src2 = explode("/", $src);
        $fileNode = $this->root->createElement("file");
        $fileNode->setAttribute("name", end($src2));
        $fileNode->setAttribute("path", $path);
        $fileNode->setAttribute("encoding", $encoding);
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $src);
        if ($file) {
            $fileNode->nodeValue = chunk_split(base64_encode($file));
        } else {
            $fileNode->nodeValue = "file_not_found";
        }
        return $fileNode;
    }

    private function addFileNodes(DOMElement $node, $text)
    {
        if (!empty($text)) {
            $htmlText = new DOMDocument();
            $htmlText->loadHTML($text);
            $images = $htmlText->getElementsByTagName('img');
            $audios = $htmlText->getElementsByTagName('audio');

            foreach ($images as $image) {
                echo " " . $image->nodeName . " ";
                $src = $image->attributes->getNamedItem("src")->nodeValue;
                $node->appendChild($this->fileNode($src));
            }

            foreach ($audios as $audio) {
                echo " " . $audio->nodeName . " ";
                foreach ($audio->childNodes as $child){
                    if($child->nodeName == "source"){
                        $src = $child->attributes->getNamedItem("src")->nodeValue;
                        $node->appendChild($this->fileNode($src));
                    }
                }
            }
        }
    }


    private function fixSrcPath($text)
    {
        if (empty($text)) {
            return $text;
        } else {
            $html = new DOMDocument();
            $html->loadHTML($text);
            $images = $html->getElementsByTagName('img');
            $audios = $html->getElementsByTagName('audio');

            foreach ($images as $image) {
                $srci = $image->attributes->getNamedItem("src")->nodeValue;
                $srciList = explode("/", $srci);
                $srci = '@@PLUGINFILE@@/' . end($srciList);
                $image->attributes->getNamedItem("src")->nodeValue = $srci;
            }

            foreach ($audios as $audio) {
                foreach($audio->childNodes as $child) {
                    if($child->nodeType == XML_TEXT_NODE){
                        $child->nodeValue = "(audioFile)";
                    }
                    if($child->nodeName == "source"){
                        $srca = $child->attributes->getNamedItem("src")->nodeValue;
                        $srcaList = explode("/", $srca);
                        $srca = '@@PLUGINFILE@@/' . end($srcaList);
                        $child->attributes->getNamedItem("src")->nodeValue = $srca;
                    }
                }
            }
            return $html->saveXML($html->getElementsByTagName('p')->item(0));
        }
    }

    private
    function scoreFixed($score)
    {
        switch ($score) {
            case 'N*1':
            case 'Y*1':
            case 'T*1':
            case 'F*1':
                return 100;
            case 'N*0':
            case 'Y*0':
            case 'T*0':
            case 'F*0':
                return 0;
            default:
                return $score * 100;
        }
    }

    private
    function textAnswerFixed($score, $text)
    {
        switch ($score) {
            case 'N*1':
            case 'N*0':
                return 'No';
            case 'Y*0':
            case 'Y*1':
                return 'Si';
            case 'T*0':
            case 'T*1':
                return 'true';
            case 'F*0':
            case 'F*1':
                return 'false';
            default:
                return $text;
        }
    }


}