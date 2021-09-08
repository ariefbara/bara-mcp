<?php

namespace Query\Domain\SharedModel;

use Query\Domain\SharedModel\SummaryTable\Entry;
use Tests\TestBase;

class SummaryTableTest extends TestBase
{
    protected $summaryTable;
    protected $entryOne;
    protected $entryTwo;
    protected $headerColumnOne, $colNumberOne = 1, $labelOne = 'header label one';
    protected $headerColumnTwo, $colNumberTwo = 2, $labelTwo = 'header label two';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->summaryTable = new TestableSummaryTable([]);
        
        $this->entryOne = $this->buildMockOfClass(Entry::class);
        $this->entryTwo = $this->buildMockOfClass(Entry::class);
        
        $this->summaryTable->entries[] = $this->entryOne;
        $this->summaryTable->entries[] = $this->entryTwo;
        
        $this->headerColumnOne = $this->buildMockOfInterface(IHeaderColumn::class);
        $this->headerColumnOne->expects($this->any())->method('getColNumber')->willReturn($this->colNumberOne);
        $this->headerColumnOne->expects($this->any())->method('getLabel')->willReturn($this->labelOne);
        
        $this->headerColumnTwo = $this->buildMockOfInterface(IHeaderColumn::class);
        $this->headerColumnTwo->expects($this->any())->method('getColNumber')->willReturn($this->colNumberTwo);
        $this->headerColumnTwo->expects($this->any())->method('getLabel')->willReturn($this->labelTwo);
    }
    
    public function test_construct_setInitialEntries()
    {
        $summaryTable = new TestableSummaryTable([$this->entryOne, $this->entryTwo]);
        $this->assertEquals($this->entryOne, $summaryTable->entries[0]);
        $this->assertEquals($this->entryTwo, $summaryTable->entries[1]);
    }
    
    public function test_addEntry_addEntryToList()
    {
        $this->summaryTable->entries = [];
        $this->summaryTable->addEntry($this->entryTwo);
        $this->assertEquals([$this->entryTwo], $this->summaryTable->entries);
    }
    
    protected function toArraySummaryFormat()
    {
        return $this->summaryTable->toArraySummaryFormat();
    }
    public function test_toArraySummaryFormat_returnListOfEntriesRelationalArray()
    {
        $this->entryOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($entryOneRelationalArray = ['string represent array one']);
        $this->entryTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($entryTwoRelationalArray = ['string represent array Two']);
        $this->assertEquals([
            $entryOneRelationalArray,
            $entryTwoRelationalArray,
        ], $this->toArraySummaryFormat());
    }
    
    protected function toArraySummarySimplifiedFormat()
    {
        return $this->summaryTable->toArraySummarySimplifiedFormat([$this->headerColumnOne, $this->headerColumnTwo]);
    }
    public function test_toArraySummarySimplifiedForm_returnSimplifiedHeaderEntryArray()
    {
        
        $this->entryOne->expects($this->once())
                ->method('toSimplifiedArray')
                ->willReturn($entryOneSimpliedArray = ['string represent array one']);
        $this->entryTwo->expects($this->once())
                ->method('toSimplifiedArray')
                ->willReturn($entryTwoSimplifiedArray = ['string represent array Two']);
        $result = [
            [
                $this->colNumberOne => $this->labelOne,
                $this->colNumberTwo => $this->labelTwo,
            ],
            $entryOneSimpliedArray,
            $entryTwoSimplifiedArray,
        ];
        $this->assertEquals($result, $this->toArraySummarySimplifiedFormat());
    }
    
    protected function toArrayTranscriptSimplifiedFormat()
    {
        return $this->summaryTable->toArrayTranscriptSimplifiedFormat([$this->headerColumnOne, $this->headerColumnTwo]);
    }
    public function test_toArrayTranscriptSimplifiedFormat_returnTranscriptArray()
    {
        $this->entryOne->expects($this->once())
                ->method('toSimplifiedArray')
                ->willReturn($entryOneSimpliedArray = [
                    1 => $value_11 = 'value 11',
                    2 => $value_12 = 'value 12',
                ]);
        $this->entryTwo->expects($this->once())
                ->method('toSimplifiedArray')
                ->willReturn($entryTwoSimplifiedArray = [
                    1 => $value_21 = 'value 21',
                    2 => $value_22 = 'value 22',
                ]);
        
        $result = [
            1 => [$this->labelOne, $value_11, $value_21],
            2 => [$this->labelTwo, $value_12, $value_22],
        ];
        $this->assertEquals($result, $this->toArrayTranscriptSimplifiedFormat());
    }
}

class TestableSummaryTable extends SummaryTable
{
    public $entries;
}
