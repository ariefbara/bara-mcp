<?php

namespace Shared\Domain\Model\FormRecord\MultiSelectFieldRecord;

use Shared\Domain\Model\ {
    Form\SelectField\Option,
    FormRecord\MultiSelectFieldRecord
};
use Tests\TestBase;

class SelectedOptionTest extends TestBase
{
    protected $multiSelectFieldRecord, $option;
    protected $selectedOption;
    
    protected function setUp(): void {
        parent::setUp();
        $this->multiSelectFieldRecord = $this->buildMockOfClass(MultiSelectFieldRecord::class);
        $this->option = $this->buildMockOfClass(Option::class);
        $this->selectedOption = new TestableSelectedOption($this->multiSelectFieldRecord, 'id', $this->option);
    }
    function test_construct() {
        $selectedOption = new TestableSelectedOption($this->multiSelectFieldRecord, $id = 'id', $this->option);
        $this->assertEquals($this->multiSelectFieldRecord, $selectedOption->multiSelectFieldRecord);
        $this->assertEquals($id, $selectedOption->id);
        $this->assertEquals($this->option, $selectedOption->option);
        $this->assertFalse($selectedOption->removed);
    }
    function test_remove_setRemovedTrue() {
        $this->selectedOption->remove();
        $this->assertTrue($this->selectedOption->removed);
    }
}

class TestableSelectedOption extends SelectedOption{
    public $id, $multiSelectFieldRecord, $option, $removed;
}
