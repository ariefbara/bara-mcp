<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\MultiSelectFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\SingleSelectFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\StringFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\TextAreaFieldSearchFilter;
use Firm\Domain\Model\Shared\Form\IntegerField;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use Firm\Domain\Model\Shared\Form\StringField;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class BioSearchFilterTest extends TestBase
{

    protected $firm;
    protected $bioSearchFilter;
    protected $integerFieldSearchFilter, $integerField;
    protected $stringFieldSearchFilter, $stringField;
    protected $textAreaFieldSearchFilter, $textAreaField;
    protected $singleSelectFieldSearchFilter, $singleSelectField;
    protected $multiSelectFieldSearchFilter, $multiSelectField;
    protected $id = 'new-id';
    protected $bioSearchFilterData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->bioSearchFilterData = new BioSearchFilterData();

        $this->bioSearchFilter = new TestableBioSearchFilter($this->firm, $this->id, $this->bioSearchFilterData);
        
        $this->integerFieldSearchFilter = $this->buildMockOfClass(IntegerFieldSearchFilter::class);
        $this->stringFieldSearchFilter = $this->buildMockOfClass(StringFieldSearchFilter::class);
        $this->textAreaFieldSearchFilter = $this->buildMockOfClass(TextAreaFieldSearchFilter::class);
        $this->singleSelectFieldSearchFilter = $this->buildMockOfClass(SingleSelectFieldSearchFilter::class);
        $this->multiSelectFieldSearchFilter = $this->buildMockOfClass(MultiSelectFieldSearchFilter::class);
        
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableBioSearchFilter($this->firm, $this->id, $this->bioSearchFilterData);
    }
    public function test_construct_setProperties()
    {
        $bioSearchFilter = $this->executeConstruct();
        $this->assertSame($this->firm, $bioSearchFilter->firm);
        $this->assertSame($this->id, $bioSearchFilter->id);
        $this->assertFalse($bioSearchFilter->disabled);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $bioSearchFilter->modifiedTime);
        $this->assertInstanceOf(ArrayCollection::class, $bioSearchFilter->integerFieldSearchFilters);
        $this->assertInstanceOf(ArrayCollection::class, $bioSearchFilter->stringFieldSearchFilters);
        $this->assertInstanceOf(ArrayCollection::class, $bioSearchFilter->textAreaFieldSearchFilters);
        $this->assertInstanceOf(ArrayCollection::class, $bioSearchFilter->singleSelectFieldSearchFilters);
        $this->assertInstanceOf(ArrayCollection::class, $bioSearchFilter->multiSelectFieldSearchFilters);
    }
    public function test_construct_addIntegerFieldSearchFilter()
    {
        $this->bioSearchFilterData->addIntegerFieldFilter($this->integerField, 1);
        $bioSearchFilter = $this->executeConstruct();
        $this->assertEquals(1, $bioSearchFilter->integerFieldSearchFilters->count());
        $this->assertInstanceOf(IntegerFieldSearchFilter::class, $bioSearchFilter->integerFieldSearchFilters->first());
    }
    public function test_construct_addStringFieldSearchFilter()
    {
        $this->bioSearchFilterData->addStringFieldFilter($this->stringField, 1);
        $bioSearchFilter = $this->executeConstruct();
        $this->assertEquals(1, $bioSearchFilter->stringFieldSearchFilters->count());
        $this->assertInstanceOf(StringFieldSearchFilter::class, $bioSearchFilter->stringFieldSearchFilters->first());
    }
    public function test_construct_addTextAreaFieldSearchFilter()
    {
        $this->bioSearchFilterData->addTextAreaFieldFilter($this->textAreaField, 1);
        $bioSearchFilter = $this->executeConstruct();
        $this->assertEquals(1, $bioSearchFilter->textAreaFieldSearchFilters->count());
        $this->assertInstanceOf(TextAreaFieldSearchFilter::class, $bioSearchFilter->textAreaFieldSearchFilters->first());
    }
    public function test_construct_addSingleSelectFieldSearchFilter()
    {
        $this->bioSearchFilterData->addSingleSelectFieldFilter($this->singleSelectField, 1);
        $bioSearchFilter = $this->executeConstruct();
        $this->assertEquals(1, $bioSearchFilter->singleSelectFieldSearchFilters->count());
        $this->assertInstanceOf(SingleSelectFieldSearchFilter::class, $bioSearchFilter->singleSelectFieldSearchFilters->first());
    }
    public function test_construct_addMultiSelectFieldSearchFilter()
    {
        $this->bioSearchFilterData->addMultiSelectFieldFilter($this->multiSelectField, 1);
        $bioSearchFilter = $this->executeConstruct();
        $this->assertEquals(1, $bioSearchFilter->multiSelectFieldSearchFilters->count());
        $this->assertInstanceOf(MultiSelectFieldSearchFilter::class, $bioSearchFilter->multiSelectFieldSearchFilters->first());
    }
    
    protected function executeUpdate()
    {
        $this->bioSearchFilter->update($this->bioSearchFilterData);
    }
    public function test_update_updateAllIntegerFieldSearchFilter()
    {
        $this->bioSearchFilter->integerFieldSearchFilters->add($this->integerFieldSearchFilter);
        $this->integerFieldSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeUpdate();
    }
    public function test_update_updateAllLefoverIntegerFieldFilterInBioSearchFilterData()
    {
        $this->bioSearchFilterData->addIntegerFieldFilter($this->integerField, 1);
        $this->executeUpdate();
        $this->assertEquals(1, $this->bioSearchFilter->integerFieldSearchFilters->count());
        $this->assertInstanceOf(IntegerFieldSearchFilter::class, $this->bioSearchFilter->integerFieldSearchFilters->first());
    }
    public function test_update_updateAllStringFieldSearchFilter()
    {
        $this->bioSearchFilter->stringFieldSearchFilters->add($this->stringFieldSearchFilter);
        $this->stringFieldSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeUpdate();
    }
    public function test_update_updateAllLefoverStringFieldFilterInBioSearchFilterData()
    {
        $this->bioSearchFilterData->addStringFieldFilter($this->stringField, 1);
        $this->executeUpdate();
        $this->assertEquals(1, $this->bioSearchFilter->stringFieldSearchFilters->count());
        $this->assertInstanceOf(StringFieldSearchFilter::class, $this->bioSearchFilter->stringFieldSearchFilters->first());
    }
    public function test_update_updateAllTextAreaFieldSearchFilter()
    {
        $this->bioSearchFilter->textAreaFieldSearchFilters->add($this->textAreaFieldSearchFilter);
        $this->textAreaFieldSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeUpdate();
    }
    public function test_update_updateAllLefoverTextAreaFieldFilterInBioSearchFilterData()
    {
        $this->bioSearchFilterData->addTextAreaFieldFilter($this->textAreaField, 1);
        $this->executeUpdate();
        $this->assertEquals(1, $this->bioSearchFilter->textAreaFieldSearchFilters->count());
        $this->assertInstanceOf(TextAreaFieldSearchFilter::class, $this->bioSearchFilter->textAreaFieldSearchFilters->first());
    }
    public function test_update_updateAllSingleSelectFieldSearchFilter()
    {
        $this->bioSearchFilter->singleSelectFieldSearchFilters->add($this->singleSelectFieldSearchFilter);
        $this->singleSelectFieldSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeUpdate();
    }
    public function test_update_updateAllLefoverSingleSelectFieldFilterInBioSearchFilterData()
    {
        $this->bioSearchFilterData->addSingleSelectFieldFilter($this->singleSelectField, 1);
        $this->executeUpdate();
        $this->assertEquals(1, $this->bioSearchFilter->singleSelectFieldSearchFilters->count());
        $this->assertInstanceOf(SingleSelectFieldSearchFilter::class, $this->bioSearchFilter->singleSelectFieldSearchFilters->first());
    }
    public function test_update_updateAllMultiSelectFieldSearchFilter()
    {
        $this->bioSearchFilter->multiSelectFieldSearchFilters->add($this->multiSelectFieldSearchFilter);
        $this->multiSelectFieldSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeUpdate();
    }
    public function test_update_updateAllLefoverMultiSelectFieldFilterInBioSearchFilterData()
    {
        $this->bioSearchFilterData->addMultiSelectFieldFilter($this->multiSelectField, 1);
        $this->executeUpdate();
        $this->assertEquals(1, $this->bioSearchFilter->multiSelectFieldSearchFilters->count());
        $this->assertInstanceOf(MultiSelectFieldSearchFilter::class, $this->bioSearchFilter->multiSelectFieldSearchFilters->first());
    }
    
    public function test_disable_setDisabled()
    {
        $this->bioSearchFilter->disable();
        $this->assertTrue($this->bioSearchFilter->disabled);
    }
    
    public function test_enable_setEnabled()
    {
        $this->bioSearchFilter->disabled = true;
        $this->bioSearchFilter->enable();
        $this->assertFalse($this->bioSearchFilter->disabled);
    }
}

class TestableBioSearchFilter extends BioSearchFilter
{

    public $firm;
    public $id;
    public $disabled;
    public $modifiedTime;
    public $integerFieldSearchFilters;
    public $stringFieldSearchFilters;
    public $textAreaFieldSearchFilters;
    public $singleSelectFieldSearchFilters;
    public $multiSelectFieldSearchFilters;

}
