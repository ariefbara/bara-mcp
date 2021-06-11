<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use SharedContext\Domain\ValueObject\SelectFieldComparisonType;
use Tests\TestBase;

class MultiSelectFieldSearchFilterTest extends TestBase
{
    protected $bioSearchFilter;
    protected $multiSelectField;
    protected $multiSelectFieldSearchFilter;
    protected $id = 'new-id';
    protected $comparisonType = 2;
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        
        $multiSelectFieldSearchFilterData = new MultiSelectFieldSearchFilterData($this->multiSelectField, 1);
        $this->multiSelectFieldSearchFilter = new TestableMultiSelectFieldSearchFilter($this->bioSearchFilter, 'id', $multiSelectFieldSearchFilterData);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
    }
    
    protected function getMultiSelectFieldSearchFilterData()
    {
        return new MultiSelectFieldSearchFilterData($this->multiSelectField, $this->comparisonType);
    }
    public function test_construct_setProperties()
    {
        $multiSelectFieldSearchFilter = new TestableMultiSelectFieldSearchFilter($this->bioSearchFilter, $this->id, $this->getMultiSelectFieldSearchFilterData());
        $this->assertSame($this->bioSearchFilter, $multiSelectFieldSearchFilter->bioSearchFilter);
        $this->assertSame($this->id, $multiSelectFieldSearchFilter->id);
        $this->assertFalse($multiSelectFieldSearchFilter->disabled);
        $this->assertSame($this->multiSelectField, $multiSelectFieldSearchFilter->multiSelectField);
        $this->assertEquals(new SelectFieldComparisonType($this->comparisonType), $multiSelectFieldSearchFilter->comparisonType);
    }
    
    protected function executeUpdate()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithMultiSelectField')
                ->with($this->multiSelectField)
                ->willReturn($this->comparisonType);
        $this->multiSelectFieldSearchFilter->update($this->bioSearchFilterData);
    }
    public function test_update_updateComparisonType()
    {
        $this->executeUpdate();
        $this->assertEquals(new SelectFieldComparisonType($this->comparisonType), $this->multiSelectFieldSearchFilter->comparisonType);
    }
    public function test_update_disabledFilter_setEnabled()
    {
        $this->multiSelectFieldSearchFilter->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->multiSelectFieldSearchFilter->disabled);
    }
    public function test_update_irrelevantFilter_noComparisonTypeCorrespondWithMultiSelectField_setDisabled()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithMultiSelectField')
                ->with($this->multiSelectField)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->multiSelectFieldSearchFilter->disabled);
    }
}

class TestableMultiSelectFieldSearchFilter extends MultiSelectFieldSearchFilter
{
    public $bioSearchFilter;
    public $id;
    public $disabled;
    public $multiSelectField;
    public $comparisonType;
}
