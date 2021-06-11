<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\IntegerField;
use SharedContext\Domain\ValueObject\IntegerFieldComparisonType;
use Tests\TestBase;

class IntegerFieldSearchFilterTest extends TestBase
{
    protected $bioSearchFilter;
    protected $integerField;
    protected $integerFieldSearchFilter;
    protected $id = 'new-id';
    protected $comparisonType = 2;
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        
        $integerFieldSearchFilterData = new IntegerFieldSearchFilterData($this->integerField, 1);
        $this->integerFieldSearchFilter = new TestableIntegerFieldSearchFilter($this->bioSearchFilter, 'id', $integerFieldSearchFilterData);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
    }
    
    protected function getIntegerFieldSearchFilterData()
    {
        return new IntegerFieldSearchFilterData($this->integerField, $this->comparisonType);
    }
    public function test_construct_setProperties()
    {
        $integerFieldSearchFilter = new TestableIntegerFieldSearchFilter($this->bioSearchFilter, $this->id, $this->getIntegerFieldSearchFilterData());
        $this->assertSame($this->bioSearchFilter, $integerFieldSearchFilter->bioSearchFilter);
        $this->assertSame($this->id, $integerFieldSearchFilter->id);
        $this->assertFalse($integerFieldSearchFilter->disabled);
        $this->assertSame($this->integerField, $integerFieldSearchFilter->integerField);
        $this->assertEquals(new IntegerFieldComparisonType($this->comparisonType), $integerFieldSearchFilter->comparisonType);
    }
    
    protected function executeUpdate()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithIntegerField')
                ->with($this->integerField)
                ->willReturn($this->comparisonType);
        $this->integerFieldSearchFilter->update($this->bioSearchFilterData);
    }
    public function test_update_updateComparisonType()
    {
        $this->executeUpdate();
        $this->assertEquals(new IntegerFieldComparisonType($this->comparisonType), $this->integerFieldSearchFilter->comparisonType);
    }
    public function test_update_disabledFilter_setEnabled()
    {
        $this->integerFieldSearchFilter->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->integerFieldSearchFilter->disabled);
    }
    public function test_update_irrelevantFilter_noComparisonTypeCorrespondWithIntegerField_setDisabled()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithIntegerField')
                ->with($this->integerField)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->integerFieldSearchFilter->disabled);
    }
}

class TestableIntegerFieldSearchFilter extends IntegerFieldSearchFilter
{
    public $bioSearchFilter;
    public $id;
    public $disabled;
    public $integerField;
    public $comparisonType;
}
