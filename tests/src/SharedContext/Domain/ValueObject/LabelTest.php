<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class LabelTest extends TestBase
{
    protected $name = 'new label name', $description = 'new label description';
    protected $label;
    protected $otherLabel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->label = new TestableLabel(new LabelData('name', 'description'));
        $this->otherLabel = new TestableLabel(new LabelData('name', 'description'));
    }
    protected function getLabelData()
    {
        return new LabelData($this->name, $this->description);
    }
    
    protected function executeConstruct()
    {
        return new TestableLabel($this->getLabelData());
    }
    public function test_construct_setProperties()
    {
        $label = $this->executeConstruct();
        $this->assertEquals($this->name, $label->name);
        $this->assertEquals($this->description, $label->description);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', 'bad request: name is mandatory');
    }
    
    //
    protected function update()
    {
        return $this->label->update($this->getLabelData());
    }
    public function test_update_returnNewLabel()
    {
        $this->assertEquals(new TestableLabel($this->getLabelData()), $this->update());
    }
    
    //
    protected function sameValueAs()
    {
        return $this->label->sameValueAs($this->otherLabel);
    }
    public function test_sameValueAs_equalsValues_returnTrue()
    {
        $this->otherLabel->name = $this->label->name;
        $this->otherLabel->description = $this->label->description;
        $this->assertTrue($this->sameValueAs());
    }
    public function test_sameValueAs_differentNameValue_returnFalse()
    {
        $this->otherLabel->name = 'different name';
        $this->assertFalse($this->sameValueAs());
    }
    public function test_sameValueAs_differentDescriptionValue_returnFalse()
    {
        $this->otherLabel->description = 'different description';
        $this->assertFalse($this->sameValueAs());
    }
}

class TestableLabel extends Label
{
    public $name;
    public $description;
}
