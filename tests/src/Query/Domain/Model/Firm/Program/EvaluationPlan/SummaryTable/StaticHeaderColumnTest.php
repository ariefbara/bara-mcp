<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable;

use Tests\TestBase;

class StaticHeaderColumnTest extends TestBase
{
    protected $staticHeaderColumn;
    protected $colNumber = 5, $label = 'new label';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->staticHeaderColumn = new TestableStaticHeaderColumn('2', 'label');
    }
    
    public function test_construct_setProperties()
    {
        $staticHeaderColumn = new TestableStaticHeaderColumn($this->colNumber, $this->label);
        $this->assertEquals($this->colNumber, $staticHeaderColumn->colNumber);
        $this->assertEquals($this->label, $staticHeaderColumn->label);
    }
    
    public function test_toArray_returnArray()
    {
        $this->assertEquals([
            'colNumber' => $this->staticHeaderColumn->colNumber,
            'label' => $this->staticHeaderColumn->label,
        ], $this->staticHeaderColumn->toArray());
    }
    
}

class TestableStaticHeaderColumn extends StaticHeaderColumn
{
    public $colNumber;
    public $label;
}
