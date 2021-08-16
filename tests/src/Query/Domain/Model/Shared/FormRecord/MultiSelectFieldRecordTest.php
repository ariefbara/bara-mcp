<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord\SelectedOption;
use Tests\TestBase;

class MultiSelectFieldRecordTest extends TestBase
{
    protected $multiSelectFieldRecord;
    protected $multiSelectField;
    protected $selectedOptionOne;
    protected $selectedOptionTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->multiSelectFieldRecord = new TestableMultiSelectFieldRecord();
        $this->multiSelectFieldRecord->selectedOptions = new ArrayCollection();
        
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        $this->multiSelectFieldRecord->multiSelectField = $this->multiSelectField;
        
        $this->selectedOptionOne = $this->buildMockOfClass(SelectedOption::class);
        $this->selectedOptionTwo = $this->buildMockOfClass(SelectedOption::class);
    }
    
    public function test_isActiveFieldRecordCorrespondWith_sameMultiSelectField_returnTrue()
    {
        $this->assertTrue($this->multiSelectFieldRecord->isActiveFieldRecordCorrespondWith($this->multiSelectField));
    }
    public function test_isActiveFieldRecordCorrespondWith_removed_returnFalse()
    {
        $this->multiSelectFieldRecord->removed = true;
        $this->assertFalse($this->multiSelectFieldRecord->isActiveFieldRecordCorrespondWith($this->multiSelectField));
    }
    public function test_isActiveFieldRecordCorrespondWith_differentMultiSelectField_returnFalse()
    {
        $this->multiSelectFieldRecord->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        $this->assertFalse($this->multiSelectFieldRecord->isActiveFieldRecordCorrespondWith($this->multiSelectField));
    }
    
    protected function getStringOfSelectedOptionNameList()
    {
        $this->multiSelectFieldRecord->selectedOptions->add($this->selectedOptionOne);
        $this->multiSelectFieldRecord->selectedOptions->add($this->selectedOptionTwo);
        return $this->multiSelectFieldRecord->getStringOfSelectedOptionNameList();
    }
    public function test_getStringOfSelectedOptionNameList_returnListOfSelectedOptionName()
    {
        $this->selectedOptionOne->expects($this->once())
                ->method('getOptionName')
                ->willReturn($optionOneName = 'option one name');
        $this->selectedOptionTwo->expects($this->once())
                ->method('getOptionName')
                ->willReturn($optionTwoName = 'option two name');
        
        $result = "{$optionOneName}\r\n{$optionTwoName}";
        $this->assertEquals($result, $this->getStringOfSelectedOptionNameList());
    }
    public function test_getStringOfSelectedOptionNameList_containRemovedSelectedOption_excludeRemoved()
    {
        $this->selectedOptionOne->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->selectedOptionOne->expects($this->never())
                ->method('getOptionName');
        
        $this->selectedOptionTwo->expects($this->once())
                ->method('getOptionName')
                ->willReturn($optionTwoName = 'option two name');
        
        $result = "{$optionTwoName}";
        $this->assertEquals($result, $this->getStringOfSelectedOptionNameList());
    }
}

class TestableMultiSelectFieldRecord extends MultiSelectFieldRecord
{
    public $formRecord;
    public $id;
    public $multiSelectField;
    public $selectedOptions;
    public $removed = false;
    
    public function __construct()
    {
    }
}
