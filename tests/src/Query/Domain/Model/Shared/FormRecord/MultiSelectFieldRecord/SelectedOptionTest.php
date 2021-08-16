<?php

namespace Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;

use Query\Domain\Model\Shared\Form\SelectField\Option;
use Tests\TestBase;

class SelectedOptionTest extends TestBase
{
    protected $selectedOption;
    protected $option;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->selectedOption = new TestableSelectedOption();
        $this->option = $this->buildMockOfClass(Option::class);
        $this->selectedOption->option = $this->option;
    }
    
    public function test_getOptionName_returnOptionName()
    {
        $this->option->expects($this->once())
                ->method('getName');
        $this->selectedOption->getOptionName();
    }
}

class TestableSelectedOption extends SelectedOption
{
    public $multiSelectFieldRecord;
    public $id;
    public $option;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
