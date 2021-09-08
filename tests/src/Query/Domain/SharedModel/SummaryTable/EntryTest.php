<?php

namespace Query\Domain\SharedModel\SummaryTable;

use Query\Domain\SharedModel\SummaryTable\Entry\EntryColumn;
use Tests\TestBase;

class EntryTest extends TestBase
{
    protected $entry;
    protected $entryColumnOne, $colNumberOne = 1, $valueOne = 'value one', $relationalArrayOne = ['colNumber' => 1, 'value' => 'value one'];
    protected $entryColumnTwo, $colNumberTwo = 2, $valueTwo = 'value two', $relationalArrayTwo = ['colNumber' => 2, 'value'=> 'value two'];
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->entryColumnOne = $this->buildMockOfClass(EntryColumn::class);
        $this->entryColumnOne->expects($this->any())->method('getColNumber')->willReturn($this->colNumberOne);
        $this->entryColumnOne->expects($this->any())->method('getValue')->willReturn($this->valueOne);
        $this->entryColumnOne->expects($this->any())->method('toArray')->willReturn($this->relationalArrayOne);
        
        $this->entryColumnTwo = $this->buildMockOfClass(EntryColumn::class);
        $this->entryColumnTwo->expects($this->any())->method('getColNumber')->willReturn($this->colNumberTwo);
        $this->entryColumnTwo->expects($this->any())->method('getValue')->willReturn($this->valueTwo);
        $this->entryColumnTwo->expects($this->any())->method('toArray')->willReturn($this->relationalArrayTwo);
        
        $this->entry = new TestableEntry([]);
        $this->entry->entryColumns[] = $this->entryColumnOne;
        $this->entry->entryColumns[] = $this->entryColumnTwo;
    }
    
    public function test_construct_setProperties()
    {
        $entry = new TestableEntry([$this->entryColumnOne, $this->entryColumnTwo]);
        $this->assertEquals($this->entryColumnOne, $entry->entryColumns[0]);
        $this->assertEquals($this->entryColumnTwo, $entry->entryColumns[1]);
    }
    
    public function test_addEntryColumn_addEntryColumnToCollection()
    {
        $this->entry->entryColumns = [];
        $this->entry->addEntryColumn($this->entryColumnTwo);
        $this->assertEquals([$this->entryColumnTwo], $this->entry->entryColumns);
    }
    
    public function test_toRelationalArray_returnRelationalArray()
    {
        $this->assertEquals([
            1 => $this->relationalArrayOne,
            2 => $this->relationalArrayTwo,
        ], $this->entry->toRelationalArray());
    }
    
    public function test_toSimplifiedArray_returnSimplifiedArray()
    {
        $this->assertEquals([
            1 => $this->valueOne,
            2 => $this->valueTwo,
        ], $this->entry->toSimplifiedArray());
    }
}

class TestableEntry extends Entry
{
    public $entryColumns;
}
