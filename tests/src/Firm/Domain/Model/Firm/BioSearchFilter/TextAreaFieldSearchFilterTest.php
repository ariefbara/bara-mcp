<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use SharedContext\Domain\ValueObject\TextFieldComparisonType;
use Tests\TestBase;

class TextAreaFieldSearchFilterTest extends TestBase
{

    protected $bioSearchFilter;
    protected $textAreaField;
    protected $textAreaFieldSearchFilter;
    protected $id = 'new-id';
    protected $comparisonType = 2;
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);

        $textAreaFieldSearchFilterData = new TextAreaFieldSearchFilterData($this->textAreaField, 1);
        $this->textAreaFieldSearchFilter = new TestableTextAreaFieldSearchFilter($this->bioSearchFilter, 'id',
                $textAreaFieldSearchFilterData);

        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
    }

    protected function getTextAreaFieldSearchFilterData()
    {
        return new TextAreaFieldSearchFilterData($this->textAreaField, $this->comparisonType);
    }

    public function test_construct_setProperties()
    {
        $textAreaFieldSearchFilter = new TestableTextAreaFieldSearchFilter($this->bioSearchFilter, $this->id,
                $this->getTextAreaFieldSearchFilterData());
        $this->assertSame($this->bioSearchFilter, $textAreaFieldSearchFilter->bioSearchFilter);
        $this->assertSame($this->id, $textAreaFieldSearchFilter->id);
        $this->assertFalse($textAreaFieldSearchFilter->disabled);
        $this->assertSame($this->textAreaField, $textAreaFieldSearchFilter->textAreaField);
        $this->assertEquals(new TextFieldComparisonType($this->comparisonType),
                $textAreaFieldSearchFilter->comparisonType);
    }

    protected function executeUpdate()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithTextAreaField')
                ->with($this->textAreaField)
                ->willReturn($this->comparisonType);
        $this->textAreaFieldSearchFilter->update($this->bioSearchFilterData);
    }

    public function test_update_updateComparisonType()
    {
        $this->executeUpdate();
        $this->assertEquals(new TextFieldComparisonType($this->comparisonType),
                $this->textAreaFieldSearchFilter->comparisonType);
    }

    public function test_update_disabledFilter_setEnabled()
    {
        $this->textAreaFieldSearchFilter->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->textAreaFieldSearchFilter->disabled);
    }

    public function test_update_irrelevantFilter_noComparisonTypeCorrespondWithTextAreaField_setDisabled()
    {
        $this->bioSearchFilterData->expects($this->any())
                ->method('pullComparisonTypeCorrespondWithTextAreaField')
                ->with($this->textAreaField)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->textAreaFieldSearchFilter->disabled);
    }

}

class TestableTextAreaFieldSearchFilter extends TextAreaFieldSearchFilter
{

    public $bioSearchFilter;
    public $id;
    public $disabled;
    public $textAreaField;
    public $comparisonType;

}
