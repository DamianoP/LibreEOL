<?php

class QTIXMLDocument
{
    private DOMDocument $root;
    private $questestinteropNode;
    private ?string $error;

    public function __construct()
    {
        $this->root = new DOMDocument('1.0', 'utf-8');
        $this->root->formatOutput = true;
        $this->questestinteropNode = $this->root->createElement('questestinterop');
        $this->questestinteropNode->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/ims_qtiasiv1p2');
        $this->root->appendChild($this->questestinteropNode);
        $this->error = null;
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
                if ($answer['feedbackId'] !== null || $answer['feedback'] !== null) {
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

    private function presentationNode($question, $answers, $type)
    {
        try {
            $node = $this->root->createElement('presentation');
            $node->appendChild($this->materialNode($question));
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

    private function resprocessingNode($answers, $type)
    {
        try {
            $node = $this->root->createElement('resprocessing');
            $node->appendChild($this->outcomesNode());
            $index = 0;
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] !== 0) {
                    $index += 1;
                    $node->appendChild($this->respcondictionNode($index, $answer['id'], $answer['feedbackId'], $answer['score']));
                    if ($type == 'NM') {
                        $node->appendChild($this->respcondictionNode($index + 1, $answer['id'], $answer['feedbackId'], $answer['score'], true));
                    }
                }
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

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
            } elseif ($unanswered) {
                $node->appendChild($this->conditionvarNode('unanswered', "1", null));
                $node->appendChild($this->setvarNode('0', 'Set'));
                $node->appendChild($this->displayfeedbackNode('default'));
            } else {
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

    private function render_fibNode($fibtype, $rows, $columns, $maxchars)
    {
        try {
            $node = $this->root->createElement('render_fib');
            $node->setAttribute('fibtype', $fibtype);
            //$node->setAttribute('prompt',$prompt);
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

    private function displayfeedbackNode($linkrefid)
    {
        try {
            $node = $this->root->createElement("displayfeedback");
            $node->setAttribute('linkrefid', $linkrefid);
            if (null !== null) {
                $node->setAttribute('feedbacktype', null);
            }
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

    private function orNode()
    {
        try {
            return $this->root->createElement("or");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    private function andNode()
    {
        try {
            return $this->root->createElement("and");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    private function notNode()
    {
        try {
            return $this->root->createElement("not");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    private function otherNode()
    {
        try {
            return $this->root->createElement("other");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

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

    private function materialNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement("material");
            $node->appendChild($this->mattextNode($text));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    private function mattextNode($text)
    {
        $node = null;
        try {
            $node = $this->root->createElement("mattext");
            $node->setAttribute('texttype', 'text/html');
            $textField = $this->root->createCDATASection($text);
            $node->appendChild($textField);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
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

    public function convertType($type)
    {
        switch ($type) {
            case 'MR':
            case 'YN':
            case 'TF':
                return 'singleChoice';
            case 'NM':
                return 'numerical';
            case 'TM':
                return 'string';
            case 'ES':
                return 'essay';
            case 'MC':
                return 'multipleChoice';
            default:
                return '';
        }
    }

}