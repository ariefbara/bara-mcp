<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\SummaryTable\Entry;
use Tests\TestBase;

class HeaderColumnTest extends TestBase
{
    protected $field, $fieldLabel = 'field label';
    protected $headerColumn;
    protected $colNumber = 3;
    
    protected $evaluationReport;
    protected $entry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->field = $this->buildMockOfClass(IField::class);
        $this->field->expects($this->any())
                ->method('getLabel')
                ->willReturn($this->fieldLabel);
        
        $this->headerColumn = new TestableHeaderColumn(4, $this->field);
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->entry = $this->buildMockOfClass(Entry::class);
    }
    
    public function test_construct_setProperties()
    {
        $headerClolumn = new TestableHeaderColumn($this->colNumber, $this->field);
        $this->assertEquals($this->colNumber, $headerClolumn->colNumber);
        $this->assertEquals($this->field, $headerClolumn->field);
    }
    
    public function test_getLabel_returnFieldLabel()
    {
        $this->assertEquals($this->fieldLabel, $this->headerColumn->getLabel());
    }
    
    protected function appendEntryColumnFromRecordToEntry()
    {
        $this->headerColumn->appendEntryColumnFromRecordToEntry($this->evaluationReport, $this->entry);
    }
    public function test_appendEntryColumnFromRecordToEntry_addEntryColumnToEntry()
    {
        $entryColumn = new Entry\EntryColumn($this->headerColumn->colNumber, $value = 'entry value');
        $this->field->expects($this->once())
                ->method('getCorrespondingValueFromRecord')
                ->with($this->evaluationReport)
                ->willReturn($value);
        
        $this->entry->expects($this->once())
                ->method('addEntryColumn')
                ->with($entryColumn);
        
        $this->appendEntryColumnFromRecordToEntry();
    }
    
    public function test_toArray_returnColNumberValueArray()
    {
        $this->assertEquals([
            'colNumber' => $this->headerColumn->colNumber,
            'label' => $this->fieldLabel,
        ], $this->headerColumn->toArray());
    }
}

class TestableHeaderColumn extends HeaderColumn
{
    public $colNumber;
    public $field;
}
