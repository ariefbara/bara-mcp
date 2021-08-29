<?php

namespace Query\Domain\SharedModel\SummaryTable\Entry;

use Tests\TestBase;

class EntryColumnTest extends TestBase
{
    protected $entryColumn;
    protected $colNumber = 8;
    protected $value = 'new entry value';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->entryColumn = new TestableEntryColumn(3, 'value');
    }
    
    public function test_construct_setProperties()
    {
        $entryColumn = new TestableEntryColumn($this->colNumber, $this->value);
        $this->assertEquals($this->colNumber, $entryColumn->colNumber);
        $this->assertEquals($this->value, $entryColumn->value);
    }
    
    public function test_toArray_returnValidArray()
    {
        $this->assertEquals([
            'colNumber' => $this->entryColumn->colNumber,
            'value' => $this->entryColumn->value,
        ], $this->entryColumn->toArray());
    }
}

class TestableEntryColumn extends EntryColumn
{
    public $colNumber;
    public $value;
}
