<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use SharedContext\Domain\ValueObject\SelectFieldComparisonType;
use Tests\TestBase;

class SingleSelectFieldSearchFilterTest extends TestBase
{
    protected $bioSearchFilter;
    protected $singleSelectField;
    protected $singleSelectFieldSearchFilter;
    protected $id = 'new-id';
    protected $comparisonType = 2;
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        
        $singleSelectFieldSearchFilterData = new SingleSelectFieldSearchFilterData($this->singleSelectField, 1);
        $this->singleSelectFieldSearchFilter = new TestableSingleSelectFieldSearchFilter($this->bioSearchFilter, 'id', $singleSelectFieldSearchFilterData);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
    }
    
    protected function getSingleSelectFieldSearchFilterData()
    {
        return new SingleSelectFieldSearchFilterData($this->singleSelectField, $this->comparisonType);
    }
    public function test_construct_setProperties()
    {
        $singleSelectFieldSearchFilter = new TestableSingleSelectFieldSearchFilter($this->bioSearchFilter, $this->id, $this->getSingleSelectFieldSearchFilterData());
        $this->assertSame($this->bioSearchFilter, $singleSelectFieldSearchFilter->bioSearchFilter);
        $this->assertSame($this->id, $singleSelectFieldSearchFilter->id);
        $this->assertFalse($singleSelectFieldSearchFilter->disabled);
        $this->assertSame($this->singleSelectField, $singleSelectFieldSearchFilter->singleSelectField);
        $this->assertEquals(new SelectFieldComparisonType($this->comparisonType), $singleSelectFieldSearchFilter->comparisonType);
    }
    
    protected function executeUpdate()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithSingleSelectField')
                ->with($this->singleSelectField)
                ->willReturn($this->comparisonType);
        $this->singleSelectFieldSearchFilter->update($this->bioSearchFilterData);
    }
    public function test_update_updateComparisonType()
    {
        $this->executeUpdate();
        $this->assertEquals(new SelectFieldComparisonType($this->comparisonType), $this->singleSelectFieldSearchFilter->comparisonType);
    }
    public function test_update_disabledFilter_setEnabled()
    {
        $this->singleSelectFieldSearchFilter->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->singleSelectFieldSearchFilter->disabled);
    }
    public function test_update_irrelevantFilter_noComparisonTypeCorrespondWithSingleSelectField_setDisabled()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithSingleSelectField')
                ->with($this->singleSelectField)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->singleSelectFieldSearchFilter->disabled);
    }
}

class TestableSingleSelectFieldSearchFilter extends SingleSelectFieldSearchFilter
{
    public $bioSearchFilter;
    public $id;
    public $disabled;
    public $singleSelectField;
    public $comparisonType;
}
