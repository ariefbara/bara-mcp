<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form\SelectField\ {
    Option,
    OptionData
};
use Resources\Domain\Data\DataCollection;
use Tests\TestBase;

class SelectFieldTest extends TestBase
{
    protected $selectField;
    protected $id = 'newId', $fieldData, $selectFieldData;
    protected $optionDataCollection, $optionData;
    protected $option, $optionId = 'optionId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->option = $this->buildMockOfClass(Option::class);
        $this->option->expects($this->any())
                ->method('getId')
                ->willReturn($this->optionId);
        
        $fieldData = new FieldData('name', 'description', 'position', false);
        $selectFieldData = new SelectFieldData($fieldData);
        
        $this->selectField = new TestableSelectField('id', $selectFieldData);
        $this->selectField->options->add($this->option);
        
        $this->optionData = new OptionData('option name', 'option description', 'position');
        $this->optionDataCollection = new DataCollection();
        $this->optionDataCollection->push($this->optionData, null);
        
        $this->fieldData = new FieldData('new name', 'new description', 'new position', true);
        $this->selectFieldData = $this->buildMockOfClass(SelectFieldData::class);
        $this->selectFieldData->expects($this->any())
                ->method('getFieldData')
                ->willReturn($this->fieldData);
        $this->selectFieldData->expects($this->any())
                ->method('getOptionDataCollection')
                ->willReturn($this->optionDataCollection);
        
    }
    protected function executeConstruct()
    {
        return new TestableSelectField($this->id, $this->selectFieldData);
    }
    public function test_construct_setProperties()
    {
        $selectField = $this->executeConstruct();
        $this->assertEquals($this->id, $selectField->id);
        
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $selectField->fieldVO);
    }
    public function test_construct_containOptionData_addOptionToCollection()
    {
        $selectField = $this->executeConstruct();
        $this->assertEquals(1, $selectField->options->count());
        $this->assertInstanceOf(Option::class, $selectField->options->last());
    }
    
    protected function executeUpdate()
    {
        $this->selectFieldData->expects($this->any())
                ->method('pullOptionDataOfId')
                ->with($this->optionId)
                ->willReturn($this->optionData);
        $this->selectField->update($this->selectFieldData);
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $this->selectField->fieldVO);
    }
    public function test_update_containOptions_addOptionToCollection()
    {
        $this->executeUpdate();
        $this->assertEquals(2, $this->selectField->options->count());
        $this->assertInstanceOf(Option::class, $this->selectField->options->last());
    }
    public function test_update_optionInCollectionHasCorrespondingData_updateOption()
    {
        $this->option->expects($this->once())
                ->method('update')
                ->with($this->optionData);
        $this->executeUpdate();
    }
    public function test_update_optionInCollectionAlreadyRemoved_ignoreThisOption()
    {
        $this->option->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->option->expects($this->never())
                ->method('update')
                ->with($this->optionData);
        $this->executeUpdate();
    }
    public function test_update_optionInCollectionHasNoCorrespondingData_removeThisOption()
    {
        $this->selectFieldData->expects($this->once())
                ->method('pullOptionDataOfId')
                ->with($this->optionId)
                ->willReturn(null);
        $this->option->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }
    
}

class TestableSelectField extends SelectField{
    public $id, $fieldVO, $options;
}
