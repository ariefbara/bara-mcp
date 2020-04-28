<?php

namespace Shared\Domain\Model\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Shared\Domain\Model\Form\SelectField\Option;
use Tests\TestBase;

class SelectFieldTest extends TestBase
{

    protected $selectField, $field;
    protected $option, $optionId = 'optionId';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->selectField = new TestableSelectField();
        $this->field = $this->buildMockOfClass(FieldVO::class);
        $this->selectField->field = $this->field;
        
        $this->selectField->options = new ArrayCollection();
        $this->option = $this->buildMockOfClass(Option::class);
        $this->selectField->options->add($this->option);
        
    }
    protected function executeGetOptionOrDie()
    {
        $this->option->expects($this->any())
                ->method('getId')
                ->willReturn($this->optionId);
        return $this->selectField->getOptionOrDie($this->optionId);
    }
    public function test_getOptionOrDie_returnOption()
    {
        $this->assertEquals($this->option, $this->executeGetOptionOrDie());
    }
    public function test_getOptionOrDie_optionNotFound_throwEx()
    {
        $this->option->expects($this->once())
                ->method('getId')
                ->willReturn('differentId');
        $operation = function (){
            $this->selectField->getOptionOrDie('non-existing-option-id');
        };
        $errorDetail = 'not found: option not found';
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_getOptionOrDie_existingOptionAlreadyRemoved_throwEx()
    {
        $this->option->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $operation = function (){
            $this->executeGetOptionOrDie();
        };
        $errorDetail = 'not found: option not found';
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_assertMandatoryRequirementSatisfied_executeFieldsAssertMandatoryRequirementSatisfiedMethod()
    {
        $this->field->expects($this->once())
                ->method('assertMandatoryRequirementSatisfied')
                ->with($value = []);
        $this->selectField->assertMandatoryRequirementSatisfied($value);
    }

}

class TestableSelectField extends SelectField
{
    public $id;
    public $field;
    public $options;

    function __construct()
    {
        parent::__construct();
    }
}
