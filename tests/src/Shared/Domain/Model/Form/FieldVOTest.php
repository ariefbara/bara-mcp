<?php

namespace Shared\Domain\Model\Form;

use Tests\TestBase;

class FieldVOTest extends TestBase
{

    protected $field;
    protected $name = 'name', $description = 'description', $position = 'position', $required = true;
    protected $value = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->field = new TestableFieldVO();
        $this->field->mandatory = true;
    }

    protected function executeAssertRequirementSatified()
    {
        $this->field->assertMandatoryRequirementSatisfied($this->value);
    }

    public function test_executeAssertRequirementSatisfied_requiredFalse_void()
    {
        $this->field->mandatory = false;
        $this->executeAssertRequirementSatified();
        $this->markAsSuccess();
    }

    public function test_assertRequirementSatisfied_requiredTrue_emptyValue_throwEx()
    {
        $this->value = null;
        $operation = function () {
            $this->executeAssertRequirementSatified();
        };
        $errorDetail = "bad request: {$this->field->name} field is required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_assertRequirementSatisfied_requiredTrue_notEmptyValue_void()
    {
        $this->value = 'not empty';
        $this->executeAssertRequirementSatified();
        $this->markAsSuccess();
    }

}

class TestableFieldVO extends FieldVO
{

    public $name, $description, $position, $mandatory;
    
    function __construct()
    {
        parent::__construct();
    }

}
