<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class LabelTest extends TestBase
{
    protected $name = 'new label name', $description = 'new label description';
    
    protected function setUp(): void
    {
        parent::setUp();
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
}

class TestableLabel extends Label
{
    public $name;
    public $description;
}
