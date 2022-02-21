<?php

class MoodleXMLDocument
{
    private DOMDocument $root;
    private ?DOMElement $currentQuestion;
    private $quizNode;
    private ?string $error;

    public function __construct()
    {
        $this->root = new DOMDocument('1.0', 'ISO-8859-1');
        $this->root->formatOutput = true;
        $this->quizNode = $this->root->createElement("quiz");
        $this->currentQuestion = null;
        $this->root->appendChild($this->quizNode);
        $this->error = null;
    }

    //function for retrieve the MoodleXMLDocument as string
    public function getDoc()
    {
        return $this->root->saveXML();
    }

    //function for get the error string
    public function getError(): ?string
    {
        if ($this->error != null) {
            return __CLASS__ . " -> " . $this->error;
        } else {
            return null;
        }
    }

    //function for create the category node
    public function createCategory($name, $idTopic, $info): bool
    {
        try {
            if (empty($idTopic)) {
                throw new Exception("Category id cannot be null");
            }
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

        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return false;
        }
        return true;
    }

    //function for create the question nodes, must create them after the category node is created
    public function createQuestion($type, $id, $name, $text, $genFeedback = null, $defGrade = 1.0000000, $defPenalty = 0.3333333, $corFeed = 'Risposta corretta.', $parCorFeed = 'Risposta parzialmente corretta.', $incorFeed = 'Risposta errata.', $shuffleAnswer = 1, $hidden = 0, $unitGradingType = 0, $unitPenality = 0.1000000, $showUnits = 3, $unitsLeft = 0, $useCase = 0): bool
    {
        try {
            if ($id == null) {
                throw new Exception("Question id cannot be null");
            }
            //create question
            $question = $this->root->createElement('question');

            $formattedType = $this->selectType($type);
            if ($formattedType === null) {
                throw new Exception();
            }
            $question->setAttribute("type", $formattedType);

            //question name
            $question->appendChild($this->nameNode($name));

            //question text
            $question->appendChild($this->questionText($text));

            //general feedback
            $question->appendChild($this->generalFeedbackNode($genFeedback));

            //default grade
            $question->appendChild($this->defaultGradeNode($defGrade));

            //penality
            if ($type === 'TF') {
                $question->appendChild($this->penalityNode(1.0000000));
            } else {
                $question->appendChild($this->penalityNode($defPenalty));
            }

            // hidden
            $question->appendChild($this->hiddenNode($hidden));

            //question properties
            if ($type === 'MC' || $type === 'YN') {
                $question->appendChild($this->shuffleAnswerNode($shuffleAnswer));
                $question->appendChild($this->singleNode('true'));
                $question->appendChild($this->answerNumberingNode("abc"));
                $question->appendChild($this->correctFeedbackNode($corFeed));
                $question->appendChild($this->pCorrectFeedbackNode($parCorFeed));
                $question->appendChild($this->incorrectFeedbackNode($incorFeed));
            } elseif ($type === 'MR') {
                $question->appendChild($this->shuffleAnswerNode($shuffleAnswer));
                $question->appendChild($this->singleNode());
                $question->appendChild($this->answerNumberingNode("abc"));
                $question->appendChild($this->correctFeedbackNode($corFeed));
                $question->appendChild($this->pCorrectFeedbackNode($parCorFeed));
                $question->appendChild($this->incorrectFeedbackNode($incorFeed));
            } elseif ($type === 'NM') {
                $question->appendChild($this->unitgradingtypeNode($unitGradingType));
                $question->appendChild($this->unitpenalityNode($unitPenality));
                $question->appendChild($this->showunitsNode($showUnits));
                $question->appendChild($this->unitsleftNode($unitsLeft));
            } elseif ($type === 'TM') {
                $question->appendChild($this->usecaseNode($useCase));
            }

            $this->currentQuestion = $question;
            $this->quizNode->appendChild($this->currentQuestion);

        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return false;
        }
        return true;
    }

    //function for create the answer nodes
    public function createAnswer($id, $text, $score, $feedback = "", $tolerance = 0): bool
    {
        try {

            if ($this->currentQuestion == null) {
                throw new Exception("cannot create answer without a question, create a question before");
            }

            if ($this->currentQuestion->getAttribute("type") !== "essay") {
                if ($id == null) {
                    throw new Exception("answer id must be not null");
                }

                $answer = $this->root->createElement("answer");
                $answer->setAttribute('fraction', $this->scoreFixed($score));

                switch ($this->currentQuestion->getAttribute("type")) {
                    case "truefalse":
                        $answer->appendChild($this->textNode($this->textAnswerFixed($score, $text), true));
                        break;

                    case "numerical":
                        $answer->appendChild($this->textNode($this->textAnswerFixed($score, $text), true));
                        $answer->appendChild($this->toleranceNode($tolerance));
                        break;

                    case "shortanswer":
                        $answer->appendChild($this->textNode($this->fixSrcPath($this->textAnswerFixed($score, $text)), true));
                        break;

                    default:
                        $answer->appendChild($this->textNode($this->fixSrcPath($this->textAnswerFixed($score, $text)), true));

                }

                if (!$this->addFileNodes($answer, $text)) {
                    throw new Exception();
                }
                $answer->appendChild($this->feedbackNode($feedback));
                $answer->appendChild($this->idNode($id));
                $this->currentQuestion->appendChild($answer);

            }
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return false;
        }
        return true;
    }

    //function for questionType conversion
    private function selectType($type): ?string
    {
        try {
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
                    if ($this->checkTypeFormat($type)) {
                        return $type;
                    } else {
                        throw new Exception("questions of type " . $type . " are not allowed in the Moodle format");
                    }
            }
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
    }

    //function for create name nodes
    private function nameNode($text)
    {
        $name = null;
        try {
            $name = $this->root->createElement('name');
            $textField = $this->textNode($text, true);
            $name->appendChild($textField);
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $name;
    }

    //function for create questionText nodes, placed inside question nodes
    private function questionText($text)
    {
        $qTextNode = null;
        try {
            $qTextNode = $this->root->createElement('questiontext');
            $qTextNode->setAttribute('format', 'html');
            $fixedSrc = $this->fixSrcPath($text);
            if ($text !== null && $fixedSrc === null) {
                throw new Exception();
            }
            $textField = $this->textNode($fixedSrc, false);
            $qTextNode->appendChild($textField);
            if (!$this->addFileNodes($qTextNode, $text)) {
                throw new Exception();
            }
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $qTextNode;
    }

    //function for create text nodes
    private function textNode($text, $cdata)
    {
        $node = null;
        try {
            $node = $this->root->createElement("text");
            if ($cdata) {
                $textField = $this->root->createCDATASection(htmlentities($text));
            } else {
                $textField = $this->root->createTextNode($text);
            }
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create id nodes
    private function idNode($id)
    {
        $node = null;
        try {
            $node = $this->root->createElement('idnumber');
            $idField = $this->root->createTextNode($id);
            $node->appendChild($idField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create feedback  nodes
    private function feedbackNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement('feedback');
            $textField = $this->textNode($text, false);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $node;
    }

    //function for create shuffleanswer nodes, used in question nodes
    private function shuffleAnswerNode($bool)
    {
        $node = null;
        try {
            $node = $this->root->createElement('shuffleanswers');
            $textField = $this->root->createTextNode($bool);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create single nodes, used in question nodes for multiple or single response
    private function singleNode($bool = 'false')
    {
        $node = null;
        try {
            $node = $this->root->createElement('single');
            $textField = $this->root->createTextNode($bool);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create answerNumbering nodes, used in question nodes
    private function answerNumberingNode($text = 'none')
    {
        $node = null;
        try {
            $node = $this->root->createElement('answernumbering');
            $textField = $this->root->createTextNode($text);
            $node->appendChild($textField);
        } catch (Exception $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create generalfeedback nodes, used in question nodes
    private function generalFeedbackNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement('generalfeedback');
            $node->setAttribute('format', 'html');
            $fixedSrc = $this->fixSrcPath($text);
            if ($text !== null && $fixedSrc === null) {
                throw new Exception();
            }
            $textField = $this->textNode($fixedSrc, false);
            if (!$this->addFileNodes($node, $text)) {
                throw new Exception();
            }
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $node;
    }

    //function for create correctfeedback nodes, used in question nodes
    private function correctFeedbackNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement('correctfeedback');
            $node->setAttribute('format', 'html');
            $fixedSrc = $this->fixSrcPath($text);
            if ($text !== null && $fixedSrc === null) {
                throw new Exception();
            }
            $textField = $this->textNode($fixedSrc, false);
            if (!$this->addFileNodes($node, $text)) {
                throw new Exception();
            }
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $node;
    }

    //function for create partiallycorrectfeedback nodes, used in question nodes
    private function pCorrectFeedbackNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement('partiallycorrectfeedback');
            $node->setAttribute('format', 'html');
            $fixedSrc = $this->fixSrcPath($text);
            if ($text !== null && $fixedSrc === null) {
                throw new Exception();
            }
            $textField = $this->textNode($fixedSrc, false);
            if (!$this->addFileNodes($node, $text)) {
                throw new Exception();
            }
            $node->appendChild($textField);
        } catch (Exception $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $node;
    }

    //function for create incorrectfeedback nodes, used in question nodes
    private function incorrectFeedbackNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement('incorrectfeedback');
            $node->setAttribute('format', 'html');
            $fixedSrc = $this->fixSrcPath($text);
            if ($text !== null && $fixedSrc === null) {
                throw new Exception();
            }
            $textField = $this->textNode($fixedSrc, false);
            if (!$this->addFileNodes($node, $text)) {
                throw new Exception();
            }
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return null;
        }
        return $node;
    }

    //function for create defaultgrade nodes, used in question nodes
    private function defaultGradeNode($grade)
    {
        $node = null;
        try {
            $node = $this->root->createElement('defaultgrade');
            $textField = $this->root->createTextNode($grade);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create penalty nodes, used in question nodes
    private function penalityNode($penality)
    {
        $node = null;
        try {
            $node = $this->root->createElement('penality');
            $textField = $this->root->createTextNode($penality);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create hidden nodes, used in question nodes
    private function hiddenNode($hidden)
    {
        $node = null;
        try {
            $node = $this->root->createElement('hidden');
            $textField = $this->root->createTextNode($hidden);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create usecase nodes, used in question nodes
    private function usecaseNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('usecase');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create unitgradingtype nodes, used in question nodes
    private function unitgradingtypeNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('unitgradingtype');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create unitpenalty nodes, used in question nodes
    private function unitpenalityNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('unitpenality');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create showunits nodes, used in question nodes
    private function showunitsNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('showunits');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create unitsleft nodes, used in question nodes
    private function unitsleftNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('unitsleft');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create tolerance nodes, used in question nodes
    private function toleranceNode($value)
    {
        $node = null;
        try {
            $node = $this->root->createElement('tolerance');
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $node;
    }

    //function for create file nodes
    private function fileNode($src)
    {
        $fileNode = null;
        try {
            $src2 = explode("/", $src);
            $fileNode = $this->root->createElement("file");
            $fileNode->setAttribute("name", end($src2));
            $fileNode->setAttribute("path", '/');
            $fileNode->setAttribute("encoding", 'base64');
            $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $src);
            if ($file) {
                $fileNode->nodeValue = chunk_split(base64_encode($file));
            } else {
                throw new Exception("file at " . $src . " not found");
            }
        } catch (Throwable $e) {
            $this->error = __FUNCTION__ . $this->errorSummary($e);
            return null;
        }
        return $fileNode;
    }

    //function for add file nodes to the document
    private function addFileNodes(DOMElement $node, $text): bool
    {
        try {
            if (!empty($text)) {
                $htmlText = new DOMDocument();
                $htmlText->loadHTML($text);
                $images = $htmlText->getElementsByTagName('img');
                $audios = $htmlText->getElementsByTagName('audio');

                foreach ($images as $image) {
                    $src = $image->attributes->getNamedItem("src")->nodeValue;
                    $node->appendChild($this->fileNode($src));
                }

                foreach ($audios as $audio) {
                    foreach ($audio->childNodes as $child) {
                        if ($child->nodeName == "source") {
                            $src = $child->attributes->getNamedItem("src")->nodeValue;
                            $node->appendChild($this->fileNode($src));
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $this->updateError($e, __FUNCTION__);
            return false;
        }
        return true;
    }

    //function change the path of the file to plugin path
    private function fixSrcPath($text)
    {
        if (empty($text)) {
            return $text;
        } else {
            $result = '';
            try {
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
                    foreach ($audio->childNodes as $child) {
                        if ($child->nodeType == XML_TEXT_NODE) {
                            $child->nodeValue = "(audioFile)";
                        }
                        if ($child->nodeName == "source") {
                            $srca = $child->attributes->getNamedItem("src")->nodeValue;
                            $srcaList = explode("/", $srca);
                            $srca = '@@PLUGINFILE@@/' . end($srcaList);
                            $child->attributes->getNamedItem("src")->nodeValue = $srca;
                        }
                    }
                }
                $result = $html->saveXML($html->getElementsByTagName('p')->item(0));
            } catch (Throwable $e) {
                $this->updateError($e, __FUNCTION__);
                return null;
            }
            return $result;
        }
    }

    //function for change the format of the score
    private function scoreFixed($score)
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

    //function for change the text of the answers of type yes/no true/false
    private function textAnswerFixed($score, $text)
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

    //function for check if the type of the question is supported by moodle
    private function checkTypeFormat($type): bool
    {
        switch ($type) {
            case 'multichoice':
            case 'truefalse':
            case 'shortanswer':
            case 'matching':
            case 'cloze':
            case 'essay':
            case 'numerical':
            case 'description':
                return true;
            default:
                return false;
        }
    }

    //function for build an error string
    private function errorSummary(Throwable $exception): string
    {
        return ": " . $exception->getMessage() . " ( line:" . $exception->getLine() . ", code:" . $exception->getCode() . ", trace:" . $exception->getTrace() . " )";
    }

    //function for update the error string
    private function updateError(Throwable $exception, $function)
    {
        if ($this->error == null) {
            $this->error = $function . $this->errorSummary($exception);
        } else {
            $this->error = $function . " (line:" . $exception->getLine() . ") -> " . $this->error;
        }
    }

}