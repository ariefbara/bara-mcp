<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\StringField;
use SharedContext\Domain\ValueObject\TextFieldComparisonType;
use Tests\TestBase;

class StringFieldSearchFilterTest extends TestBase
{
    protected $bioSearchFilter;
    protected $stringField;
    protected $stringFieldSearchFilter;
    protected $id = 'new-id';
    protected $comparisonType = 2;
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        
        $stringFieldSearchFilterData = new StringFieldSearchFilterData($this->stringField, 1);
        $this->stringFieldSearchFilter = new TestableStringFieldSearchFilter($this->bioSearchFilter, 'id', $stringFieldSearchFilterData);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
    }
    
    protected function getStringFieldSearchFilterData()
    {
        return new StringFieldSearchFilterData($this->stringField, $this->comparisonType);
    }
    public function test_construct_setProperties()
    {
        $stringFieldSearchFilter = new TestableStringFieldSearchFilter($this->bioSearchFilter, $this->id, $this->getStringFieldSearchFilterData());
        $this->assertSame($this->bioSearchFilter, $stringFieldSearchFilter->bioSearchFilter);
        $this->assertSame($this->id, $stringFieldSearchFilter->id);
        $this->assertFalse($stringFieldSearchFilter->disabled);
        $this->assertSame($this->stringField, $stringFieldSearchFilter->stringField);
        $this->assertEquals(new TextFieldComparisonType($this->comparisonType), $stringFieldSearchFilter->comparisonType);
    }
    
    protected function executeUpdate()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithStringField')
                ->with($this->stringField)
                ->willReturn($this->comparisonType);
        $this->stringFieldSearchFilter->update($this->bioSearchFilterData);
    }
    public function test_update_updateComparisonType()
    {
        $this->executeUpdate();
        $this->assertEquals(new TextFieldComparisonType($this->comparisonType), $this->stringFieldSearchFilter->comparisonType);
    }
    public function test_update_disabledFilter_setEnabled()
    {
        $this->stringFieldSearchFilter->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->stringFieldSearchFilter->disabled);
    }
    public function test_update_irrelevantFilter_noComparisonTypeCorrespondWithStringField_setDisabled()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithStringField')
                ->with($this->stringField)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->stringFieldSearchFilter->disabled);
    }
}

class TestableStringFieldSearchFilter extends StringFieldSearchFilter
{
    public $bioSearchFilter;
    public $id;
    public $disabled;
    public $stringField;
    public $comparisonType;
}
