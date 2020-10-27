<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\{
    Form\MultiSelectField,
    Form\SelectField\Option,
    FormRecord,
    FormRecord\MultiSelectFieldRecord\SelectedOption
};
use Tests\TestBase;

class MultiSelectFieldRecordTest extends TestBase
{

    protected $formRecord, $multiSelectField;
    protected $multiSelectFieldRecord;
    protected $id = 'id', $option;
    protected $selectedOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);

        $this->multiSelectFieldRecord = new TestableMultiSelectFieldRecord($this->formRecord, 'id',
                $this->multiSelectField, []);

        $this->selectedOption = $this->buildMockOfClass(SelectedOption::class);
        $this->multiSelectFieldRecord->selectedOptions->add($this->selectedOption);

        $this->option = $this->buildMockOfClass(Option::class);
    }

    protected function executeConstruct()
    {
        return new TestableMultiSelectFieldRecord($this->formRecord, $this->id, $this->multiSelectField, [$this->option]);
    }

    function test_construct()
    {
        $record = $this->executeConstruct();
        $this->assertEquals($this->formRecord, $record->formRecord);
        $this->assertEquals($this->id, $record->id);
        $this->assertEquals($this->multiSelectField, $record->multiSelectField);
        $this->assertFalse($record->removed);
    }

    function test_construct_addSelectedOptionToCollection()
    {
        $record = $this->executeConstruct();
        $this->assertEquals(1, $record->selectedOptions->count());
        $this->assertInstanceOf(SelectedOption::class, $record->selectedOptions->first());
    }

    private function executeSetSelectedOptions()
    {
        $this->multiSelectFieldRecord->setSelectedOptions([$this->option]);
    }

    function test_setSelectedOption_addSelectedOptionToCollection()
    {
        $this->executeSetSelectedOptions();
        $this->assertEquals(2, $this->multiSelectFieldRecord->selectedOptions->count());
        $this->assertInstanceOf(SelectedOption::class, $this->multiSelectFieldRecord->selectedOptions->last());
    }

    function test_setSelectedOption_aSelectedOptionReferToSameOptionExistInCollection_ignoreAddition()
    {
        $this->selectedOption->expects($this->atLeastOnce())
                ->method('getOption')
                ->willReturn($this->option);

        $this->executeSetSelectedOptions();
        $this->assertEquals(1, $this->multiSelectFieldRecord->selectedOptions->count());
    }

    function test_setSelectedOption_selectedOptionReferToSameOptionAlreadyRemoved_addNewRecord()
    {
        $this->selectedOption->expects($this->atLeastOnce())
                ->method('getOption')
                ->willReturn($this->option);
        $this->selectedOption->expects($this->any())
                ->method('isRemoved')
                ->willReturn(true);

        $this->executeSetSelectedOptions();
        $this->assertEquals(2, $this->multiSelectFieldRecord->selectedOptions->count());
    }

    function test_setSelectedOption_containSelectedOptionNoLongerSelected_removedThisSelectedOption()
    {
        $unselectedOption = new TestableOption();
        $this->selectedOption->expects($this->atLeastOnce())
                ->method('getOption')
                ->willReturn($unselectedOption);

        $this->selectedOption->expects($this->once())
                ->method('remove');
        $this->executeSetSelectedOptions();
    }

    function test_setSelectedOption_containSelectedOptionNoLongerSelectedButAlreadyRemoved_ignoreThisRecord()
    {
        $unselectedOption = new TestableOption();
        $this->selectedOption->expects($this->atLeastOnce())
                ->method('getOption')
                ->willReturn($unselectedOption);
        $this->selectedOption->expects($this->atLeastOnce())
                ->method('isRemoved')
                ->willReturn(true);

        $this->selectedOption->expects($this->never())
                ->method('remove');
        $this->executeSetSelectedOptions();
    }

    function test_isReferToRemovedField_returnFieldRemovedStatus()
    {
        $this->multiSelectField->expects($this->once())->method('isRemoved')->willReturn(true);
        $this->assertTrue($this->multiSelectFieldRecord->isReferToRemovedField());
    }

    function test_remove_setRemovedStatusTrue()
    {
        $this->multiSelectFieldRecord->remove();
        $this->assertTrue($this->multiSelectFieldRecord->removed);
    }

    function test_remove_removeAllSelectedOption()
    {
        $this->selectedOption->expects($this->once())
                ->method('remove');
        $this->multiSelectFieldRecord->remove();
    }

    function test_remove_containAlreadyRemovedSelectedOption_skipRemoval()
    {
        $this->selectedOption->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->selectedOption->expects($this->never())
                ->method('remove');

        $this->multiSelectFieldRecord->remove();
    }

}

class TestableMultiSelectFieldRecord extends MultiSelectFieldRecord
{

    public $formRecord, $id, $multiSelectField, $removed, $selectedOptions;

}

class TestableOption extends Option
{

    public function __construct()
    {
        ;
    }

}
