<?php

namespace Firm\Domain\Task\BioSearchFilterDataBuilder;

use Tests\TestBase;

class BioFormSearchFilterRequestTest extends TestBase
{
    protected $bioFormId = 'bio-form-id';
    protected $bioFormSearchFilterRequest;


    protected function setUp(): void
    {
        parent::setUp();
        $this->bioFormSearchFilterRequest = new TestableBioFormSearchFilterRequest('id');
    }
    
    public function test_construct_setProperties()
    {
        $bioFormSearchFilterRequest = new TestableBioFormSearchFilterRequest($this->bioFormId);
        $this->assertSame($this->bioFormId, $bioFormSearchFilterRequest->bioFormId);
        $this->assertSame([], $bioFormSearchFilterRequest->integerFieldSearchFilterRequests);
        $this->assertSame([], $bioFormSearchFilterRequest->stringFieldSearchFilterRequests);
        $this->assertSame([], $bioFormSearchFilterRequest->textAreaFieldSearchFilterRequests);
        $this->assertSame([], $bioFormSearchFilterRequest->singleSelectFieldSearchFilterRequests);
        $this->assertSame([], $bioFormSearchFilterRequest->multiSelectFieldSearchFilterRequests);
    }
    
    public function test_addIntegerFieldSearchFilterRequest_addToIntegerFieldCollection()
    {
        $this->bioFormSearchFilterRequest->addIntegerFieldSearchFilterRequest($fieldId = 'integer-field-id', $comparisonType = 1);
        $this->assertEquals([$fieldId => $comparisonType], $this->bioFormSearchFilterRequest->integerFieldSearchFilterRequests);
    }
    
    public function test_addStringFieldSearchFilterRequest_addToStringFieldCollection()
    {
        $this->bioFormSearchFilterRequest->addStringFieldSearchFilterRequest($fieldId = 'string-field-id', $comparisonType = 1);
        $this->assertEquals([$fieldId => $comparisonType], $this->bioFormSearchFilterRequest->stringFieldSearchFilterRequests);
    }
    
    public function test_addTextAreaFieldSearchFilterRequest_addToTextAreaFieldCollection()
    {
        $this->bioFormSearchFilterRequest->addTextAreaFieldSearchFilterRequest($fieldId = 'textArea-field-id', $comparisonType = 1);
        $this->assertEquals([$fieldId => $comparisonType], $this->bioFormSearchFilterRequest->textAreaFieldSearchFilterRequests);
    }
    
    public function test_addSingleSelectFieldSearchFilterRequest_addToSingleSelectFieldCollection()
    {
        $this->bioFormSearchFilterRequest->addSingleSelectFieldSearchFilterRequest($fieldId = 'singleSelect-field-id', $comparisonType = 1);
        $this->assertEquals([$fieldId => $comparisonType], $this->bioFormSearchFilterRequest->singleSelectFieldSearchFilterRequests);
    }
    
    public function test_addMultiSelectFieldSearchFilterRequest_addToMultiSelectFieldCollection()
    {
        $this->bioFormSearchFilterRequest->addMultiSelectFieldSearchFilterRequest($fieldId = 'multiSelect-field-id', $comparisonType = 1);
        $this->assertEquals([$fieldId => $comparisonType], $this->bioFormSearchFilterRequest->multiSelectFieldSearchFilterRequests);
    }
}

class TestableBioFormSearchFilterRequest extends BioFormSearchFilterRequest
{
    public $bioFormId;
    public $integerFieldSearchFilterRequests;
    public $stringFieldSearchFilterRequests;
    public $textAreaFieldSearchFilterRequests;
    public $singleSelectFieldSearchFilterRequests;
    public $multiSelectFieldSearchFilterRequests;
}
