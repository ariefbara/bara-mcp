<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form\SelectField\OptionData;
use Resources\Domain\Data\DataCollection;
use Tests\TestBase;

class SelectFieldDataTest extends TestBase
{
    protected $fieldData;
    protected $optionDataCollection;
    protected $selectFieldData;
    protected $optionData, $optionId = 'optionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldData = $this->buildMockOfClass(FieldData::class);
        $this->optionDataCollection = $this->buildMockOfClass(DataCollection::class);
        $this->selectFieldData = new TestableSelectFieldData($this->fieldData);
        $this->selectFieldData->optionDataCollection = $this->optionDataCollection;
        
        $this->optionData = $this->buildMockOfClass(OptionData::class);
    }
    public function test_construct_setProperties()
    {
        $selectFieldData = new TestableSelectFieldData($this->fieldData);
        $this->assertEquals($this->fieldData, $selectFieldData->fieldData);
        $this->assertEquals(new DataCollection(), $selectFieldData->optionDataCollection);
    }
    
    public function test_pushOptionData_pushOptionDataToCollection()
    {
        $this->optionDataCollection->expects($this->once())
                ->method('push')
                ->with($this->optionData, $this->optionId);
        $this->selectFieldData->pushOptionData($this->optionData, $this->optionId);
    }
    public function test_pullOptionDataOfId_returnCollectionPullMethod()
    {
        $this->optionDataCollection->expects($this->once())
                ->method('pull')
                ->with($this->optionId)
                ->willReturn($this->optionData);
        $this->assertEquals($this->optionData, $this->selectFieldData->pullOptiondataOfId($this->optionId));
    }
}

class TestableSelectFieldData extends SelectFieldData
{
    public $fieldData;
    public $optionDataCollection;
}
