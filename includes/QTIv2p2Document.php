<?php

class QTIv2p2Document
{
    private DOMDocument $root;
    private DOMDocument $manifestRoot;
    private DOMElement $manifest;
    private DOMElement $resourcesNode;
    private ?string $error;
    private array $allResources;
    private array $tempResources;

    //template constants
    const templateMatchCorrect = 'http://www.imsglobal.org/question/qti_v2p2/rptemplates/match_correct';
    const templateMapResponse = 'http://www.imsglobal.org/question/qti_v2p2/rptemplates/map_response';
    const templateMapResponsePoint = 'http://www.imsglobal.org/question/qti_v2p2/rptemplates/map_response_point';

    //namespaces and schema constants
    const imscp_v1p1 = 'http://www.imsglobal.org/xsd/imscp_v1p1';
    const w3shemaInstance = 'http://www.w3.org/2001/XMLSchema-instance';
    const qti2p2_imscp1p2_1p0 = 'http://www.imsglobal.org/xsd/qti/qtiv2p2/qtiv2p2_imscpv1p2_v1p0.xsd';

    //manifest id constant
    const manifest_id = "MANIFEST-DOCUMENT";

    //constructor
    public function __construct()
    {
        //create the manifest
        $this->manifestRoot = new DOMDocument('1.0', 'UTF-8');
        $this->manifestRoot->formatOutput = true;
        $manifest = $this->manifestRoot->createElement('manifest');
        $manifest->setAttribute('xmlns', self::imscp_v1p1);
        $manifest->setAttribute('xmlns:xsi', self::w3shemaInstance);
        $manifest->setAttribute('identifier', self::manifest_id);
        $manifest->setAttribute('xsi:schemaLocation', self::imscp_v1p1 . " " . self::qti2p2_imscp1p2_1p0);

        $metadata = $this->manifestRoot->createElement('metadata');
        $schema = $this->manifestRoot->createElement('schema');
        $schema->appendChild($this->manifestRoot->createTextNode('QTIv2.2 Package'));
        $schemaversion = $this->manifestRoot->createElement('schemaversion');
        $schemaversion->appendChild($this->manifestRoot->createTextNode('1.0.0'));
        $metadata->appendChild($schema);
        $metadata->appendChild($schemaversion);
        $manifest->appendChild($metadata);

        $organizations = $this->manifestRoot->createElement('organizations');
        $manifest->appendChild($organizations);

        $this->resourcesNode = $this->manifestRoot->createElement('resources');
        $manifest->appendChild($this->resourcesNode);


        $this->manifest = $manifest;
        $this->manifestRoot->appendChild($this->manifest);

        $this->error = null;
        $this->allResources = [];
        $this->tempResources = [];
    }

    //get functions

    public function getManifest()
    {
        return $this->manifestRoot->saveXML();
    }

    public function getQuestionItem()
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

    public function getResources(): array
    {
        return $this->allResources;
    }


    //question items generation

    public function createQuestionByType($type, $id, $questionName, $questionText, $answers): bool
    {
        $this->resetTempResources();
        $idQuestion = 'ID' . $id;

        switch ($type) {
            case 'MC':
                return $this->createMCQuestion($idQuestion, $questionName, $questionText, $answers);
            case 'YN':
            case 'TF':
                return $this->createTFYNQuestion($idQuestion, $questionName, $questionText, $answers);
            case 'MR':
                return $this->createMRQuestion($idQuestion, $questionName, $questionText, $answers);
            case 'HS':
                return $this->createHSQuestion($idQuestion, $questionName, $questionText, $answers);
            case 'TM':
                return $this->createTMQuestion($idQuestion, $questionName, $questionText, $answers);
            case 'ES':
                return $this->createESQuestion($idQuestion, $questionName, $questionText);
            case 'NM':
                return $this->createNMQuestion($idQuestion, $questionName, $questionText, $answers);
            default :
                return false;
        }
    }

    public function createMCQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'single', 'identifier');
            $correctResponse = $this->correctResponseNode();
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > 0) {
                    $correctResponse->appendChild($this->valueNode($answer['id']));
                }
            }
            $responseDeclaration->appendChild($correctResponse);


            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "integer");
            $defaultValue = $this->defaultValueNode();
            $defaultValue->appendChild($this->valueNode("0"));
            $outcomesDeclaration->appendChild($defaultValue);


            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $choiceInteraction = $this->choiceInteractionNode("RESPONSE", "true", "1", "1");
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $simpleChoice = $this->simpleChoiceNode($answer['id']);
                $contentAnswer = $this->root->createDocumentFragment();
                $contentAnswer->appendXML($this->formattedText($answer['text']));
                $simpleChoice->appendChild($contentAnswer);
                $choiceInteraction->appendChild($simpleChoice);
            }
            $itemBody->appendChild($choiceInteraction);

            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMatchCorrect);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }

    }

    public function createMRQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'multiple', 'identifier');
            $correctResponse = $this->correctResponseNode();
            $numCorrect = 0;
            $mapping = $this->mappingNode(0);
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > 0) {
                    $correctResponse->appendChild($this->valueNode($answer['id']));
                    $numCorrect++;
                }
                $mapping->appendChild($this->mapEntry($answer['id'], $answer['score']));
            }
            $responseDeclaration->appendChild($correctResponse);
            $responseDeclaration->appendChild($mapping);

            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "float");

            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $choiceInteraction = $this->choiceInteractionNode("RESPONSE", "true", "1", $numCorrect);
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $simpleChoice = $this->simpleChoiceNode($answer['id']);
                $contentAnswer = $this->root->createDocumentFragment();
                $contentAnswer->appendXML($this->formattedText($answer['text']));
                $simpleChoice->appendChild($contentAnswer);
                $choiceInteraction->appendChild($simpleChoice);
            }
            $itemBody->appendChild($choiceInteraction);

            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMapResponse);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            //return the xml document
            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }
    }

    public function createTFYNQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'single', 'identifier');
            $correctResponse = $this->correctResponseNode();
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > 0) {
                    $correctResponse->appendChild($this->valueNode($answer['id']));
                }
            }
            $responseDeclaration->appendChild($correctResponse);


            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "integer");
            $defaultValue = $this->defaultValueNode();
            $defaultValue->appendChild($this->valueNode("0"));
            $outcomesDeclaration->appendChild($defaultValue);


            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $choiceInteraction = $this->choiceInteractionNode("RESPONSE", "true", "1", "1");
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $simpleChoice = $this->simpleChoiceNode($answer['id']);
                $simpleChoice->appendChild($this->root->createTextNode($answer['text']));
                $choiceInteraction->appendChild($simpleChoice);
            }
            $itemBody->appendChild($choiceInteraction);

            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMatchCorrect);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }
    }

    public function createESQuestion($idQuestion, $questionName, $questionText): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'single', 'string');

            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "integer");
            $defaultValue = $this->defaultValueNode();
            $defaultValue->appendChild($this->valueNode(0));
            $outcomesDeclaration->appendChild($defaultValue);

            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $extendedTextInteraction = $this->extendedTextInteractionNode("RESPONSE");
            $itemBody->appendChild($extendedTextInteraction);

            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMatchCorrect);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }
    }

    public function createTMQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'single', 'string');
            $correctResponse = $this->correctResponseNode();
            $maxScore = 0;
            $corrResp = '';
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > $maxScore) {
                    $maxScore = $answer['score'];
                    $corrResp = $answer['text'];
                }
            }
            $correctResponse->appendChild($this->valueNode($corrResp));

            $maxChars = 0; //used later

            $mapping = $this->mappingNode(0);
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $maxChars = max($maxChars, strlen($answer['text']));
                $mapping->appendChild($this->mapEntry($answer['text'], $answer['score']));
            }
            $responseDeclaration->appendChild($correctResponse);
            $responseDeclaration->appendChild($mapping);


            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "float");


            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $blockquote = $this->blockquoteNode();
            $paragraph = $this->root->createElement('p');
            $paragraph->appendChild($this->textEntryInteractionNode('RESPONSE', $maxChars));
            $blockquote->appendChild($paragraph);
            $itemBody->appendChild($blockquote);


            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMapResponse);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }

    }

    public function createHSQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'multiple', 'point');
            $areaMapping = $this->areaMappingNode(0);

            $maxScore = 0;
            //$corrResp = '';
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > $maxScore) {
                    $maxScore = $answer['score'];
                    //$corrResp = $answer['text'];
                }
                $areaMapping->appendChild($this->areaMapEntryNode('rect', $answer['text'], $answer['score']));
            }

            $responseDeclaration->appendChild($areaMapping);

            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "float");

            //itembody
            $itemBody = $this->itembodyNode();
            $selectPointInteraction = $this->selectPointInteractionNode('RESPONSE', '1');
            $questionFormatted = $this->formattedText($questionText);
            $content = $this->root->createDocumentFragment();
            $content->appendXML($questionFormatted);

            $prompt = $this->root->createElement('prompt');
            $prompt->appendChild($content);
            $selectPointInteraction->appendChild($prompt);
            $imgs = $selectPointInteraction->getElementsByTagName('img');
            foreach ($imgs as $img) {
                $object = $this->imageAsObject($img);
                $img->parentNode->removeChild($img);
                $selectPointInteraction->appendChild($object);
            }
            $itemBody->appendChild($selectPointInteraction);


            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMapResponsePoint);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }
    }

    public function createNMQuestion($idQuestion, $questionName, $questionText, $answers): bool
    {
        try {
            $this->root = new DOMDocument('1.0', 'UTF-8');
            $assItem = $this->assessmentItemNode($idQuestion, $questionName);
            $this->root->appendChild($assItem);

            //response declaration
            $responseDeclaration = $this->responseDeclarationNode('RESPONSE', 'single', 'float');
            $correctResponse = $this->correctResponseNode();
            $maxScore = 0;
            $corrResp = '';
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                if ($answer['score'] > $maxScore) {
                    $maxScore = $answer['score'];
                    $corrResp = $answer['text'];
                }
            }
            $correctResponse->appendChild($this->valueNode($corrResp));

            $maxChars = 0; //used later

            $mapping = $this->mappingNode(0);
            for ($i = 0; $i < count($answers); $i++) {
                $answer = $answers[$i];
                $maxChars = max($maxChars, strlen($answer['text']));
                $mapping->appendChild($this->mapEntry($answer['text'], $answer['score']));
            }
            $responseDeclaration->appendChild($correctResponse);
            $responseDeclaration->appendChild($mapping);


            //outcome declaration
            $outcomesDeclaration = $this->outcomeDeclarationNode("SCORE", "single", "float");

            //itembody
            $itemBody = $this->itembodyNode();
            $content = $this->root->createDocumentFragment();
            $content->appendXML($this->formattedText($questionText));
            $itemBody->appendChild($content);
            $blockquote = $this->blockquoteNode();
            $paragraph = $this->root->createElement('p');
            $paragraph->appendChild($this->textEntryInteractionNode('RESPONSE', $maxChars));
            $blockquote->appendChild($paragraph);
            $itemBody->appendChild($blockquote);

            //response processing
            $responseProcessing = $this->responseProcessingNode(self::templateMapResponse);

            //append all components
            $assItem->appendChild($responseDeclaration);
            $assItem->appendChild($outcomesDeclaration);
            $assItem->appendChild($itemBody);
            $assItem->appendChild($responseProcessing);

            //update the manifest
            $this->updateManifest($idQuestion);

            return true;

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return false;
        }

    }

    //node creation functions

    //create assessmentItem node
    private function assessmentItemNode($identifier, $title)
    {
        try {
            $node = $this->root->createElement("assessmentItem");
            $node->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/imsqti_v2p2');
            $node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $node->setAttribute('xsi:schemaLocation', 'http://www.imsglobal.org/xsd/imsqti_v2p2 http://www.imsglobal.org/xsd/qti/qtiv2p2/imsqti_v2p2p2.xsd');
            $node->setAttribute('identifier', $identifier);
            $node->setAttribute('title', $title);
            $node->setAttribute('adaptive', 'false');
            $node->setAttribute('timeDependent', 'false');
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create responseDeclaration node
    private function responseDeclarationNode($identifier, $cardinality, $basetype)
    {
        try {
            $node = $this->root->createElement("responseDeclaration");
            $node->setAttribute('identifier', $identifier);
            $node->setAttribute('cardinality', $cardinality);
            $node->setAttribute('baseType', $basetype);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create outcomeDeclaration node
    private function outcomeDeclarationNode($identifier, $cardinality, $basetype, $externalScored = null)
    {
        try {
            $node = $this->root->createElement("outcomeDeclaration");
            $node->setAttribute('identifier', $identifier);
            $node->setAttribute('cardinality', $cardinality);
            $node->setAttribute('baseType', $basetype);
            if ($externalScored != null) {
                $node->setAttribute('externalScored', $externalScored);
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create correctResponse node
    private function correctResponseNode()
    {
        try {
            return $this->root->createElement("correctResponse");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create defaultValue node
    private function defaultValueNode()
    {
        try {
            return $this->root->createElement("defaultValue");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create value node
    private function valueNode($value)
    {
        try {
            $node = $this->root->createElement("value");
            $textfield = $this->root->createTextNode($value);
            $node->appendChild($textfield);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create mapping node
    private function mappingNode($defaultValue = null, $lowerBound = null, $upperBound = null)
    {
        try {
            $node = $this->root->createElement("mapping");
            if ($lowerBound != null) {
                $node->setAttribute('lowerBound', $lowerBound);
            }
            if ($upperBound != null) {
                $node->setAttribute('upperBound', $upperBound);
            }
            if ($defaultValue != null) {
                $node->setAttribute('defaultValue', $defaultValue);
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create mapEntry node
    private function mapEntry($mapKey, $mappedValue)
    {
        try {
            $node = $this->root->createElement("mapEntry");
            $node->setAttribute('mapKey', $mapKey);
            $node->setAttribute('mappedValue', $mappedValue);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create areaMapping node
    private function areaMappingNode($defaultValue)
    {
        try {
            $node = $this->root->createElement("areaMapping");
            $node->setAttribute('defaultValue', $defaultValue);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create areaMapEntry node
    private function areaMapEntryNode($shape, $coords, $mappedValue)
    {
        try {
            $node = $this->root->createElement("areaMapEntry");
            $node->setAttribute('shape', $shape);
            $node->setAttribute('coords', $coords);
            $node->setAttribute('mappedValue', $mappedValue);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create itemBody node
    private function itembodyNode()
    {
        try {
            return $this->root->createElement("itemBody");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create blockquote node
    private function blockquoteNode()
    {
        try {
            return $this->root->createElement("blockquote");
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create choiceInteraction node
    private function choiceInteractionNode($responseIdentifier, $shuffle, $minChoices, $maxChoices)
    {
        try {
            $node = $this->root->createElement("choiceInteraction");
            $node->setAttribute('responseIdentifier', $responseIdentifier);
            $node->setAttribute('shuffle', $shuffle);
            $node->setAttribute('maxChoices', $maxChoices);
            $node->setAttribute('minChoices', $minChoices);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create simpleChoice node
    private function simpleChoiceNode($identifier)
    {
        try {
            $node = $this->root->createElement("simpleChoice");
            $node->setAttribute('identifier', $identifier);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create  selectPointInteraction node
    private function selectPointInteractionNode($responseIdentifier, $maxChoices)
    {
        try {
            $node = $this->root->createElement("selectPointInteraction");
            $node->setAttribute('responseIdentifier', $responseIdentifier);
            $node->setAttribute('maxChoices', $maxChoices);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create textEntryInteraction node
    private function textEntryInteractionNode($responseIdentifier, $expectedLength)
    {
        try {
            $node = $this->root->createElement("textEntryInteraction");
            $node->setAttribute('responseIdentifier', $responseIdentifier);
            $node->setAttribute('expectedLength', $expectedLength);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create extendedTextInteraction node
    private function extendedTextInteractionNode($responseIdentifier)
    {
        try {
            $node = $this->root->createElement("extendedTextInteraction");
            $node->setAttribute('responseIdentifier', $responseIdentifier);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create responseProcessing node
    private function responseProcessingNode($template = null)
    {
        try {
            $node = $this->root->createElement("responseProcessing");
            if ($template !== null) {
                $node->setAttribute('template', $template);
            }
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }


    //manifest node functions

    // function for update the manifest, it adds a full resource node
    private function updateManifest($idQuestion){
        $identifier = 'question_' . $idQuestion;
        $resource = $this->resourceNode($identifier, $identifier . '.xml');
        foreach ($this->tempResources as $filename => $path) {
            $resource->appendChild($this->fileNode($path));
        }
        $resource->appendChild($this->fileNode($identifier . '.xml'));
        $this->resourcesNode->appendChild($resource);
    }

    //create resource node
    private function resourceNode($identifier, $href)
    {
        try {
            $node = $this->manifestRoot->createElement("resource");
            $node->setAttribute('identifier', $identifier);
            $node->setAttribute('type', 'imsqti_item_xmlv2p2');
            $node->setAttribute('href', $href);
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //create file node
    private function fileNode($href)
    {
        try {
            $node = $this->manifestRoot->createElement("file");
            $node->setAttribute('href', $this->correctInvalidFilenames($href));
            return $node;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }


    //utils

    //function for replace invalid characters in file names
    private function correctInvalidFilenames($name)
    {
        $invalid = [' '];
        $replace = ['_'];

        return str_replace($invalid, $replace, $name);
    }

    //function for reset tempResources array
    private function resetTempResources()
    {
        $this->tempResources = [];
    }

    //function for formatting the html text of questions and answers to the qti format
    private function formattedText($text)
    {
        try {
            $html = new DOMDocument();
            $html->loadHTML($text);
            $xpath = new DOMXPath($html);
            $medias = $xpath->query('//img | //audio');

            foreach ($medias as $child) {

                if ($child->nodeName == "img") {
                    $srci = $child->attributes->getNamedItem("src")->nodeValue;
                    $child->attributes->getNamedItem("alt")->nodeValue = "image";
                    array_push($this->allResources, $srci);
                    $srciExp = explode("/", $srci);
                    $fileImg = end($srciExp);

                    //change src value and add to tempResources
                    $child->attributes->getNamedItem("src")->nodeValue = 'Resources/' . $this->correctInvalidFilenames($fileImg);
                    $this->tempResources[$fileImg] = 'Resources/' . $fileImg;
                }

                if ($child->nodeName == "audio") {
                    $this->replaceAudioToObject($child);
                }
            }

            $elementsToModify = $xpath->query('//p | //table | //div | //span');

            foreach ($elementsToModify as $elem) {
                $this->removeAllAttributes($elem);
            }

            $htmlText = $html->saveXML($html->getElementsByTagName('body')[0]);
            return $this->replaceUnsupportedHTMLTags($htmlText);

        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //function for remove all attributes from an XML/HTML element
    private function removeAllAttributes($element)
    {
        if ($element->hasAttributes()) {
            foreach (iterator_to_array($element->attributes) as $attr) {
                $element->removeAttributeNode($attr);
            }
        }
    }

    //function for replace <img> elements with <object> elements, used in hotspot questions
    private function imageAsObject($image)
    {
        try {
            $attr = $image->attributes;
            $height = $attr->getNamedItem("height")->nodeValue;
            $width = $attr->getNamedItem("width")->nodeValue;
            $alt = $attr->getNamedItem("alt")->nodeValue;
            $src = $attr->getNamedItem("src")->nodeValue;
            $srcExp = explode('.', $src);
            $object = $this->root->createElement('object');
            $object->setAttribute('type', 'image/' . end($srcExp));
            $object->setAttribute('width', $width);
            $object->setAttribute('height', $height);
            $object->setAttribute('data', $src);
            $object->appendChild($this->root->createTextNode($alt));

            return $object;
        } catch (Throwable $ex) {
            $this->updateError($ex, __FUNCTION__);
            return null;
        }
    }

    //function for replace <img> elements with <object> elements
    private function replaceAudioToObject(DOMElement $audio)
    {
        $object = $audio->ownerDocument->createElement('object');
        $childs = $audio->childNodes;
        foreach ($childs as $child) {
            if ($child->nodeName == "source") {
                $src = $child->attributes->getNamedItem("src")->nodeValue;
                array_push($this->allResources, $src);
                $srcExp = explode("/", $src);
                $fileAud = end($srcExp);

                //change src value and add to tempResources
                $newSrc = 'Resources/' . $this->correctInvalidFilenames($fileAud);
                $this->tempResources[$fileAud] = 'Resources/' . $fileAud;

                $ext = explode('.', $fileAud);
                $object->setAttribute('type', 'audio/' . end($ext));
                $object->setAttribute('data', $newSrc);
                break;
            }
        }
        $audio->parentNode->replaceChild($object, $audio);
    }

    //function for remove the unsupported html tags fro questions and answers text
    private function replaceUnsupportedHTMLTags($text)
    {

        $tagsToChange = ['<body>', '</body>', '<s>', '</s>', '<u>', '</u>'];
        $tagsReplacement = ['<div>', '</div>', '<p>', '</p>', '<em>', '</em>'];

        return str_replace($tagsToChange, $tagsReplacement, $text);

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
