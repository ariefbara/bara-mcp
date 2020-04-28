<?php

namespace Shared\Domain\Model\FormRecordData;

use Tests\TestBase;

class MultiSelectFieldRecordDataTest extends TestBase
{
    protected $input;
    protected $selectedOptionId = 'newselectedOptionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->input = new TestableMultiSelectFieldRecordData('id');
    }
    
    public function test_construct_setProperties()
    {
        $input = new TestableMultiSelectFieldRecordData($id = 'newId');
        $this->assertEquals($id, $input->multiSelectFieldId);
        $this->assertEquals([], $input->selectedOptionIds);
    }
    
    public function test_add_addSelectedOptionIdToCollection()
    {
        $this->input->add($this->selectedOptionId);
        $this->assertEquals([$this->selectedOptionId], $this->input->selectedOptionIds);
    }
    public function test_add_addedSelectedOptionIdAlreadyInCollection_ignore()
    {
        $existingCollection = [
            'selectedOptionId', 
            $this->selectedOptionId,
        ];
        $this->input->selectedOptionIds = $existingCollection;
        $this->input->add($this->selectedOptionId);
        $this->assertEquals($existingCollection, $this->input->selectedOptionIds);
    }
}

class TestableMultiSelectFieldRecordData extends MultiSelectFieldRecordData
{
    public $multiSelectFieldId;
    public $selectedOptionIds;
}
