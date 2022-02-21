<?php

class QTIXMLDocument
{
    private DOMDocument $root;
    private $questestinteropNode;
    private ?string $error;
    private $resources;

    public function __construct()
    {
        $this->root = new DOMDocument('1.0', 'ISO-8859-1');
        $this->root->formatOutput = true;
        $this->questestinteropNode = $this->root->createElement('questestinterop');
        $this->questestinteropNode->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/ims_qtiasiv1p2');
        $this->root->appendChild($this->questestinteropNode);
        $this->error = null;
        $this->resources = [];
    }

    public function getDoc()
    {
        return $this->root->saveXML();
    }

    public function getError(): ?string
    {
        if ($this->error != null) {
            return __CLASS__ . " -> " . $this->error;
        } else {
            return null;
        }
    }

    public function getResources(){
        return $this->resources;
    }


    // function for add the item to the questestinterop node
    public function addItemNode($topic, $questionId, $questionName, $questionText, $questionType, $answers): bool
    {
        try {
            $node = $this->root->createElement('item');
            $node->setAttribute('title', $questionName);
            $node->setAttribute('ident', $questionId);
            $node->appendChild($this->itemmetadataNode($questionType, $topic));
            $node->appendChild($this->presentationNode($questionText, $answers, $questionType));
            $node->appendChild($this->resprocessingNode($answers, $questionType));
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['feedbackId'] !== null && $answer['feedback'] !== null) {
                    $node->appendChild($this->itemfeedbackNode($answer['feedbackId'], $answer['feedback']));
                }
            }
            $this->questestinteropNode->appendChild($node);
            return true;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }
    }



    /*********************************************************/
    /*                                                       */
    /*                  ITEM NODES  (lv1)                    */
    /*                                                       */
    /*********************************************************/

    // creates the itemmetadata node, it contains metadata of the item (considered as question) like the type (multichoice, multiple response, etc) the topic etc..
    private function itemmetadataNode($itemtype, $topic)
    {
        try {
            $node = $this->root->createElement('itemmetadata');
            $node->appendChild($this->qmd_itemtypeNode($itemtype));
            $node->appendChild($this->qmd_statusNode());
            $node->appendChild($this->qmd_toolvendorNode());
            $node->appendChild($this->qmd_topic($topic));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the presentation node, it contains the information for representing question and answers
    private function presentationNode($question, $answers, $type)
    {
        try {
            $node = $this->root->createElement('presentation');
            $node->appendChild($this->materialNode($question)); // insert the question text in a material->mattext node

            // insert the answers in correct format for each category
            switch ($type) {
                case 'MC':
                case 'YN':
                case 'TF':
                    $node->appendChild($this->response_lidNode($answers, "Single"));
                    break;
                case 'NM':
                    $answerText = $answers[0]['text'];
                    $node->appendChild($this->response_numNode($answerText, "Decimal"));
                    break;
                case 'TM':
                    $answerText = $answers[0]['text'];
                    $node->appendChild($this->response_strNode("1", strlen($answerText), strlen($answerText)));
                    break;
                case 'ES':
                    $node->appendChild($this->response_strNode("4", "50", "200"));
                    break;
                case 'MR':
                    $node->appendChild($this->response_lidNode($answers, "Multiple"));
                    break;
                default:
                    throw new Exception("Wrong question type format: $type");
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the resprocessing node, it contains information for processing the result (for example setting the score for choosing the right answers)
    private function resprocessingNode($answers, $type)
    {
        try {
            $node = $this->root->createElement('resprocessing');
            $node->appendChild($this->outcomesNode());
            $index = 0;

            // for every answer with a non zero score must have a response condiction node
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] !== 0) {
                    $index += 1;
                    $node->appendChild($this->respcondictionNode($index, $answer['id'], $answer['feedbackId'], $answer['score']));

                    // for numeric answers there is also the condiction of not answering the question
                    if ($type == 'NM') {
                        $node->appendChild($this->respcondictionNode($index + 1, $answer['id'], $answer['feedbackId'], $answer['score'], true));
                    }
                }
                if($answer['id'] === "essay"){
                    $node->appendChild($this->respcondictionNode('default', null, null, null));
                }
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the itemfeedback node, it contains the text and the id of the feedback message that is used in other nodes
    // every feedback message has an id and for example it could be specified to give a feedback message after choosing an answer (in the resprocessing node)
    private function itemfeedbackNode($ident, $feedback)
    {
        try {
            $node = $this->root->createElement('itemfeedback');
            $node->setAttribute('ident', $ident);
            $node->setAttribute('view', 'Candidate');
            if ($feedback !== null) {
                $node->appendChild($this->materialNode($feedback));
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }






    /*********************************************************/
    /*                                                       */
    /*            ITEMMETADATA NODES  (lv2)                  */
    /*                                                       */
    /*********************************************************/

    // creates the qmd_itemtype node, it contains metadata information about the category of the item/question
    private function qmd_itemtypeNode($itemtype)
    {
        try {
            $node = $this->root->createElement('qmd_itemtype');
            $text = $this->root->createTextNode($itemtype);
            $node->appendChild($text);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the qmd_status node, it contains metadata information about the status of the item
    private function qmd_statusNode()
    {
        try {
            $node = $this->root->createElement('qmd_status');
            $text = $this->root->createTextNode("Normal");
            $node->appendChild($text);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the qmd_toolvendor node, it contains metadata information about the creator (or a tool used for create quiz) of the item
    private function qmd_toolvendorNode()
    {
        try {
            $node = $this->root->createElement('qmd_toolvendor');
            $text = $this->root->createTextNode("LibreEOL");
            $node->appendChild($text);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the qmd_topic node, it contains metadata information about the topic of the item/question
    private function qmd_topic($topic)
    {
        try {
            $node = $this->root->createElement('qmd_topic');
            $text = $this->root->createTextNode($topic);
            $node->appendChild($text);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*            PRESENTATION NODES  (lv2)                  */
    /*                                                       */
    /*********************************************************/

    // creates the response_lid node, it contains representation information for answers of type multiple choice, multiple response, true/false, yes/no
    private function response_lidNode($answers, $rcardinality = null)
    {
        try {
            $node = $this->root->createElement('response_lid');
            $node->setAttribute('ident', "1");
            if ($rcardinality !== null) {
                $node->setAttribute('rcardinality', $rcardinality);
            }
            $node->appendChild($this->render_choiceNode($answers, 'Yes'));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the response_str node, it contains representation information for answers of type text match and essay
    private function response_strNode($rows, $columns, $max)
    {
        try {
            $node = $this->root->createElement('response_str');
            $node->setAttribute('ident', "1");
            $node->setAttribute('rcardinality', 'Single');
            $node->appendChild($this->render_fibNode('String', $rows, $columns, $max));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the response_num node, it contains representation information for answers of type numeric
    private function response_numNode($rawAnswer, $numtype = null)
    {
        try {
            $node = $this->root->createElement('response_num');
            $node->setAttribute('ident', "1");
            $node->setAttribute('rcardinality', 'Single');
            if ($numtype !== null) {
                $node->setAttribute('numtype', $numtype);
            }
            $node->appendChild($this->render_fibNode($numtype, '1', strlen($rawAnswer), strlen($rawAnswer)));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*            RESPROCESSING NODES  (lv2)                 */
    /*                                                       */
    /*********************************************************/

    // creates the outcomes node, it contains variable declarations used in the result processing. it's needed even if you don't have to declare variables
    private function outcomesNode()
    {
        try {
            $node = $this->root->createElement('outcomes');
            $node->appendChild($this->decvarNode());
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the respcondition node, it contains information (instructions) for handling the chosen answer (assign a score for example)
    private function respcondictionNode($title, $lableid, $linkrefid, $score, $unanswered = false)
    {
        try {
            $node = $this->root->createElement('respcondition');
            $node->setAttribute('title', $title);
            $node->setAttribute('continue', 'Yes');
            if ($title === 'default') {
                $node->appendChild($this->conditionvarNode('other', null, null));
                $node->appendChild($this->setvarNode('0', 'Set'));
                $node->appendChild($this->displayfeedbackNode('default'));
            } elseif ($unanswered) { // numerical
                $node->appendChild($this->conditionvarNode('unanswered', "1", null));
                $node->appendChild($this->setvarNode('0', 'Set'));
                $node->appendChild($this->displayfeedbackNode('default'));
            }
            else {
                $node->appendChild($this->conditionvarNode('varequal', "1", $lableid));
                $node->appendChild($this->setvarNode($score, 'Set'));
                if ($linkrefid !== null) {
                    $node->appendChild($this->displayfeedbackNode($linkrefid));
                }
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*           RESPONSES NODES  (lv3)                      */
    /*                                                       */
    /*********************************************************/

    // creates the render_choice node, it's used for rendering the possibles answer choices (multiple choice, multiple response, true/false, yes/no)
    private function render_choiceNode(array $answers, $shuffle = null)
    {
        try {
            $node = $this->root->createElement('render_choice');
            if ($shuffle !== null) {
                $node->setAttribute('shuffle', $shuffle);
            }
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $node->appendChild($this->response_label($answer['id'], $answer['text']));
            }
            return $node;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the render_fib node, it's used for rendering fill-in-blanck answers like numeric, essay, or text match answers
    private function render_fibNode($fibtype, $rows, $columns, $maxchars)
    {
        try {
            $node = $this->root->createElement('render_fib');
            $node->setAttribute('fibtype', $fibtype);
            $node->setAttribute('rows', $rows);
            $node->setAttribute('columns', $columns);
            $node->setAttribute('maxchars', $maxchars);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*               OUTCOMES NODES  (lv3)                   */
    /*                                                       */
    /*********************************************************/

    // creates the decvar node, it's used for declare variables that could be used into the respcondition section
    private function decvarNode($value = null, $varname = null)
    {
        try {
            $node = $this->root->createElement('decvar');
            if ($value !== null) {
                $node->setAttribute('vartype', null);
                if ($varname !== null) {
                    $node->setAttribute('varname', $varname);
                }
                $textField = $this->root->createTextNode($value);
                $node->appendChild($textField);
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*           RESPCONDICTION NODES  (lv3)                 */
    /*                                                       */
    /*********************************************************/

    // creates the conditionvar node, it contains a conditional test used for evaluate the user choice
    private function conditionvarNode($type, $respid, $lableid)
    {
        try {
            $node = $this->root->createElement('conditionvar');
            if ($type == 'varequal') {
                $node->appendChild($this->varequalNode($respid, $lableid));
            } elseif ($type == 'unanswered') {
                $node->appendChild($this->unansweredNode($respid));
            } else {
                $node->appendChild($this->otherNode());
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the setvar node, it's used for changing the value of the default variable "SCORE" used for evaluate the test
    private function setvarNode($value, $action = null)
    {
        try {
            $node = $this->root->createElement("setvar");
            if ($action !== null) {
                $node->setAttribute('action', $action);
            }
            $textField = $this->root->createTextNode($value);
            $node->appendChild($textField);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the displayfeedback node, it's used for display a feedback after the user choice
    private function displayfeedbackNode($linkrefid)
    {
        try {
            $node = $this->root->createElement("displayfeedback");
            $node->setAttribute('linkrefid', $linkrefid);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*           RENDER_CHOICE NODES  (lv4)                  */
    /*                                                       */
    /*********************************************************/

    // creates the response_label node, it's used for create an answer choice in the render_choice node
    private function response_label($ident, $text)
    {
        try {
            $node = $this->root->createElement("response_label");
            $node->setAttribute('ident', $ident);
            $node->appendChild($this->materialNode($text));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*           CONDITIONVAR NODES  (lv4 )                 */
    /*                                                       */
    /*********************************************************/

    // creates the or node, it's used to create the Boolean 'OR' operation between the two or more enclosed tests
    private function orNode()
    {
        try {
            return $this->root->createElement("or");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the and node, it's used to create the Boolean 'AND' operation between the two or more enclosed tests
    private function andNode()
    {
        try {
            return $this->root->createElement("and");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the not node, it's used to invert the logical test outcome that is required
    private function notNode()
    {
        try {
            return $this->root->createElement("not");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the other node, used for trigger the condition when all the other test have not turned a 'true' state
    private function otherNode()
    {
        try {
            return $this->root->createElement("other");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the unanswered node, used for specify the condition to apply if the answer for the question is not received
    private function unansweredNode($respident)
    {
        try {
            $node = $this->root->createElement("unanswered");
            $node->setAttribute('respident', $respident);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates the varequal node, used for make a test of equivalence
    private function varequalNode($respident, $lableid)
    {
        $node = null;
        try {
            $node = $this->root->createElement("varequal");
            $node->setAttribute('respident', $respident);
            $textField = $this->root->createTextNode($lableid);
            $node->appendChild($textField);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }




    /*********************************************************/
    /*                                                       */
    /*                   GENERAL NODES                       */
    /*                                                       */
    /*********************************************************/

    // creates a material node, used for insert text or multimedia data
    private function materialNode($text)
    {
        $node = null;
        try {
            $parText = $text;
            $node = $this->root->createElement("material");
            $resTags = $this->addRes($text);
            foreach($resTags as $res){
                if($res["type"] == "img"){
                    $splitted = preg_split('/<img .*>/i', $parText, 2, PREG_SPLIT_NO_EMPTY);
                    //echo $splitted[0]." and ".$splitted[1]." on img <br>";
                    if(count($splitted)>1) {
                        $node->appendChild($this->mattextNode($splitted[0]));
                    }
                    $node->appendChild($this->matimageNode($res["filename"]));
                    $parText = $splitted[1];
                }
                if($res["type"] == "audio") {
                    $splitted = preg_split('/<audio .*<\/audio>/i', $parText, 2, PREG_SPLIT_NO_EMPTY);
                    //echo $splitted[0] . " and " . $splitted[1] . " on audio <br>";
                    if(count($splitted)>1) {
                        $node->appendChild($this->mattextNode($splitted[0]));
                    }
                    $node->appendChild($this->mataudioNode($res["filename"]));
                    $parText = $splitted[1];
                }
            }
            $node->appendChild($this->mattextNode($parText));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates a mattext node, used for insert a text
    private function mattextNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement("mattext");
            $node->setAttribute('texttype', 'text/html');
            $textField = $this->root->createCDATASection(htmlentities($text));
            $node->appendChild($textField);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates a mattext node, used for insert an image file
    private function matimageNode($image){
        $node = null;
        try {
            $split = explode('.', $image);
            $node = $this->root->createElement("matimage");
            $node->setAttribute('imagtype', 'image/'.$split[1]);
            $node->setAttribute('uri', $image);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // creates a mattext node, used for insert an audio file
    private function mataudioNode($audio){
        $node = null;
        try {
            $split = explode('.', $audio);
            $node = $this->root->createElement("mataudio");
            $node->setAttribute('audiotype', 'audio/'.$split[1]);
            $node->setAttribute('uri', $audio);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    // add resource file path to resources array
    private function addRes($text)
    {
        if (!empty($text)) {
            try {
                $html = new DOMDocument();
                $html->loadHTML($text);
                $resTags = [];
                $xpath = new DOMXPath($html);
                $elem = $xpath->query('//img | //audio');

                foreach($elem as $child) {
                    if ($child->nodeName == "img") {
                        $srci = $child->attributes->getNamedItem("src")->nodeValue;
                        array_push($this->resources, $srci);
                        $srciList = explode("/", $srci);
                        array_push($resTags, ["type"=>"img", "filename"=>end($srciList)]);
                    }
                    if ($child->nodeName == "audio") {
                        foreach ($child->childNodes as $child2) {
                            if ($child2->nodeName == "source") {
                                $srca = $child2->attributes->getNamedItem("src")->nodeValue;
                                $srcaList = explode("/", $srca);
                                array_push($this->resources, $srca);
                                array_push($resTags, ["type"=>"audio","filename"=>end($srcaList)]);
                            }
                        }
                    }
                }

                return $resTags;
            } catch (Throwable $e) {
                $this->updateError($e, __FUNCTION__);
                return null;
            }
        }else{
            return '';
        }
    }

    private function errorSummary(Throwable $exception): string
    {
        return ": " . $exception->getMessage() . " ( line:" . $exception->getLine() . ", code:" . $exception->getCode() . ", trace:" . $exception->getTrace() . " )";
    }

    private function updateError(Throwable $exception, $function)
    {
        if ($this->error == null) {
            $this->error = $function . $this->errorSummary($exception);
        } else {
            $this->error = $function . " (line:" . $exception->getLine() . ") -> " . $this->error;
        }
    }
}
